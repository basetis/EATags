<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Latex_editor extends MY_Controller
{
    private $_wiris_latex_url = 'http://www.wiris.net/evernote.com/editor/render.png?latex=';
    private $_formula_id;
    private $_resource_md5_body;

    public function __construct()
    {
        parent::__construct();
        $this->load->language('latex_editor');
        $this->load->language('header');
        $this->load->language('footer');
    }

    public function index()
    {
        $data = array('formula_text' => '');
        $data_GET = $this->input->get(NULL,TRUE);

        // log_message('debug', print_r($data_GET,TRUE));
        if( !$this->session->userdata('eat.logged.ok') ) {
            $this->session->set_userdata('after_login_redirect_to', base_url() . "latex_editor?formula=" . $data_GET['formula']);
            redirect('auth/login');
            return;
        }

        $this->load->model('Statistics_model', 'eat_stats');
        $this->eat_stats->track_latex_editor_opened($this->session->userdata('evernote_user_id'));

        $this->load->library('evernote');
        $this->evernote->init(
            $this->session->userdata('evernote_access_token'),
            $this->session->userdata('evernote_user_id'),
            $this->session->userdata('wiris_latex_formula_note_guid'),
            'update'
        );
        $this->load->model('eat/eat_latex', 'latex');

        $formula_data = $this->latex->get_formula_data_by_user_and_formula_id($this->session->userdata('evernote_user_id'), $data_GET['formula']);
        $formula_text = $formula_data->formula_text;
        $formula_text = (!is_null($formula_text)) ? $formula_text : '';
        $this->session->set_userdata('wiris_latex_formula_note_guid', $formula_data->note_guid);
        $this->session->set_userdata('wiris_latex_formula_id', $data_GET['formula']);
        $this->session->set_userdata('wiris_latex_resource_md5_body', $formula_data->resource_md5_body);

        if ($formula_text) {
            $this->load->library('curl');
            $params = array(
                "latex" => $formula_text,
            );

            $result = $this->curl->simple_post("http://www.wiris.net/demo/editor/latex2mathml", $params);

            if ($result) {
                $result = str_replace('"', '\'', $result);
                $data['formula_text'] = $result;
            }
        }

        $this->load->view('header');
        $this->load->view('latex_editor', $data);
        $this->load->view('footer');
    }
    public function send_to_evernote()
    {
        log_message('debug', __METHOD__);
        $data = $this->input->post(NULL,TRUE);

        $formula_id   = $data['formula'];
        $formula_text = (!is_null($data['mathML'])) ? $data['mathML'] : '';

        if (!$formula_text) {
            echo $this->lang->line('latex_editor_send_no_formula_received_error');
            return;
        }

        $this->load->library('curl');
        $params = array(
            "mml" => $formula_text,
        );

        $latex_formula = $this->curl->simple_post("http://www.wiris.net/demo/editor/mathml2latex", $params);

        if (!$latex_formula) {
            echo $this->lang->line('latex_editor_send_error_getting_from_wiris');
            return;
        }

        $this->load->library('evernote');
        $this->evernote->init(
            $this->session->userdata('evernote_access_token'),
            $this->session->userdata('evernote_user_id'),
            $this->session->userdata('wiris_latex_formula_note_guid'),
            'update'
        );
        $this->load->model('eat/eat_latex', 'latex');

        // With formula id and evernote_user_id we can retrieve act_latex for the note guid
        $note = $this->_get_note();
        if (is_null($note)) {
            echo $this->lang->line('latex_editor_send_error_getting_note_from_evernote');
            return;
        }

        $resources_list = $this->evernote->get_resources_data($note->resources);
        if (is_null($resources_list)) {
           echo $this->lang->line('latex_editor_send_error_getting_resources_from_note');
           return;
        }

        // ask wiris for new image
        $img_string  = $this->curl->simple_get($this->_wiris_latex_url . $latex_formula);

        $this->load->model('eat/eat_latex', 'latex');

        $latex_image = $this->evernote->get_resource_and_tag_from_img_string(
            $img_string,
            $this->latex->wiris_image_options,
            $this->session->userdata('wiris_latex_formula_id')
        );

        $key_resource = -1;
        foreach ($note->resources as $key => $resource ) {
            $md5_body = md5($resource->data->body);
            if ($this->session->userdata('wiris_latex_resource_md5_body') == $md5_body) {
                $latex_image['resource']->guid = $resource->guid;
                $key_resource = $key;
                break;
            }
        }

        $note->resources[$key_resource] = $latex_image['resource'];

        $start_needle = $this->latex->formula_id_img_start . $formula_id . $this->latex->formula_id_closing;
        $start_needle2 = $this->latex->formula_id_img_start2 . $formula_id . $this->latex->formula_id_closing2;
        $end_needle   = $this->latex->formula_id_img_end . $formula_id . $this->latex->formula_id_closing;
        $end_needle2   = $this->latex->formula_id_img_end2 . $formula_id . $this->latex->formula_id_closing2;

        $start_formula = strpos($note->content, $start_needle);
        if ($start_formula === FALSE) {
            $start_formula = strpos($note->content, $start_needle2);
            $start_needle = $start_needle2;
        }
        $end_formula = strpos($note->content, $end_needle);
        if ($end_formula === FALSE) {
            $end_formula = strpos($note->content, $end_needle2);
            $end_needle = $end_needle2;
        }

        $len_formula   = $end_formula - ($start_formula + strlen($start_needle));

        $original_formula = substr($note->content, $start_formula + strlen($start_needle), $len_formula);

        $original_formula_marked = $start_needle . $original_formula . $end_needle;

        $new_formula_marked =
            $start_needle .
            str_replace(
                $this->session->userdata('wiris_latex_resource_md5_body'),
                $latex_image['hash_hex'],
                $original_formula
            ) .
            $end_needle;

        $note->content = str_replace($original_formula_marked, $new_formula_marked, $note->content);

        $error_msg = $this->evernote->update_note($note, $this->session->userdata('evernote_access_token'));

        if ($error_msg) {
            echo $this->lang->line('latex_editor_send_error_updating_note');
        } else {
            $this->latex->update_formula_data(
                $latex_formula,
                $latex_image['hash_hex'],
                $this->session->userdata('evernote_user_id'),
                $this->session->userdata('wiris_latex_formula_id')
            );
            $this->session->set_userdata('wiris_latex_resource_md5_body', $latex_image['hash_hex']);

            $this->load->model('Statistics_model', 'eat_stats');
            $this->eat_stats->track_latex_formula_edited_and_updated_to_evernote($this->session->userdata('evernote_user_id'));

            echo $this->lang->line('latex_editor_send_success');
        }
    }

    private function _get_note()
    {
        $result = $this->evernote->get_note_by_id(
            $this->session->userdata('evernote_access_token'),
            $this->session->userdata('wiris_latex_formula_note_guid'),
            $options = array('with_resources_data' => true)
        );
        if ($result['error_msg']) return NULL;
        return $result['note'];
    }
}