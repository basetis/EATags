<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_latex extends MY_Model
{
    public $formula_id_img_start = '<a name="formula_img_start_';
    public $formula_id_img_start2 = '<a shape="rect" name="formula_img_start_';
    public $formula_id_img_end   = '<a name="formula_img_end_';
    public $formula_id_img_end2   = '<a shape="rect" name="formula_img_end_';

    public $formula_id_closing   = '"/>';
    public $formula_id_closing2   = '"></a>';
    private $_wiris_latex_url    = 'http://www.wiris.net/evernote.com/editor/render.png?latex=';
    private $_latex_editor_url   = 'https://eatags.com/latex_editor?formula=';

    /* TODO: add more meta data when posible, ask wiris people wich name and other data want */
    public $wiris_image_options = array(
        'MIME' => 'image/png',
        'name' => 'WIRIS_LATEX',
    );

    private $_image_error_tag = '<span style="border:1px solid red;background-color:white;color:red;">LATEX SERVER ERROR</span>';

    private $table_name      = 'action_latex';

    function __construct()
    {
        parent::__construct();
        $this->load->library('Enml', 'enml');
    }


    /*
    *   latex_note
    *       @params
    *           eat_object      object      REQUIRED
    *           tag_action      object      REQUIRED
    *           current_note    array
    *       @returns
    *           error_msg       string      empty string on success
    *           result          object
    *               note        array
    *
    */
    public function execute_action($user_id, $access_token, $original_note, $options = array())
    {
        log_message('debug', __METHOD__);

        if ($this->common->check_test_user_on_constants($user_id)) {
            log_message('debug', "LET's GET WORKING COPY");
            $wc = $this->common->get_developer_working_copy($user_id);
            $this->_latex_editor_url = $wc . "latex_editor?formula=";
            log_message('debug', "TEXT LATEX EDITOR URL" . $this->_latex_editor_url);
        }
        // 1. set default response
        $response = array(
            'error_msg' => 'Unknown ' . __METHOD__,
            'result' => array( ),
        );
        // 2. get current note content
        $current_note_content = $original_note->content;

        // 3. clean note content from Evernote headers
        $current_note_content = $this->enml->remove_enml_note_tags($current_note_content);

        // 4. find latex patterns (any text surrounded by '$$' string)

        // 4.1 check if there's latex key on db
        $pattern = "$$";
        $latex_key = $this->_check_key_on_db($user_id);
        if ($latex_key != null) {
            $pattern = $latex_key['0']->latex_key;
        }
        // 4.2 use the pattern
        $content_pieces = explode($pattern, $current_note_content);
        $total_pieces = count( $content_pieces );

        $new_content = '';
        $new_resources = array();

        // at least one $$ latex_formula $$ founded when $total_pieces > 2
        if( $total_pieces <= 2 ) {
            return $original_note;
        }

        $this->load->library('curl');
        for ($i=0; $i<$total_pieces-1; $i++)
        {
            if( !($i % 2) ) {
                $new_content.= $content_pieces[$i];
                continue;
            }
            if( !trim($content_pieces[$i]) ) { // Is Empty
                continue;
            }

            $latex_string = $this->enml->convert_enml_to_text($content_pieces[$i]);
            if( !trim($latex_string) ) { // Is Empty
                continue;
            }

            // the pair indexes corresponds to latex formula string
            $img         = '';
            $latex_image = array();
            $formula_id  = $this->get_last_formula_id_for_evernote_user_id($user_id);
            $formula_id  = (!is_null($formula_id)) ? $formula_id + 1 : 1;

            // Replace line breaks with spaces
            $line_breaks = array("\r\n", "\n", "\r");
            $latex_string = urlencode(str_replace ( $line_breaks , ' ' , $latex_string ));

            // Get image from Wiris
            $img_string = $this->curl->simple_get($this->_wiris_latex_url . $latex_string);

            if( $this->curl->error_code )
            {
                // curl error detected, log it and contiune
                $curl_error_msg = __METHOD__ . ' -> curl error, code: ->'. $this->curl->error_code . '<-';
                $curl_error_msg.= ' ; On simple_get("' .$this->_wiris_latex_url . $latex_string . '"); ';
                log_message('error', $curl_error_msg);
                $img = $this->_image_error_tag;
            } else {
                // Create a watermark almost transparent and merge with formula image
                $im = imagecreatefromstring($img_string);

                imagesavealpha($im, true);
                $trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
                imagefill($im, 0, 0, $trans_colour);
                $text_color = imagecolorallocatealpha($im, 0, 0, 0, 126);
                imagestring($im, 1, 0, 0,  $formula_id . "", $text_color);

                ob_start ();
                  imagepng ($im);
                  $image_data = ob_get_contents ();
                ob_end_clean ();

                imagedestroy($im);
                $img_string = $image_data;

                // construct evernote resource and get associated en-media tag
                $latex_image = $this->evernote->get_resource_and_tag_from_img_string($img_string, $this->wiris_image_options, $formula_id, 'vertical-align:middle;');
                if(isset($latex_image['error_msg'])){
                    $img = $this->_image_error_tag; // error detected on get_resource_and_tag_from_img_string
                } else {
                    $img .= '<a href="' . $this->_latex_editor_url . $formula_id . '">' . $latex_image['tag'] . '</a>';
                    $new_resources[] = $latex_image['resource'];
                }
            }

            $id_img_start = $this->formula_id_img_start . $formula_id  . $this->formula_id_closing;
            $id_img_end   = $this->formula_id_img_end   . $formula_id  . $this->formula_id_closing;

            $formula_content     = $pattern . $content_pieces[$i] . $pattern;

            $del_formulas = $this->_check_option_on_db($user_id);

            if($del_formulas != null) {
                $del_formulas = $del_formulas['0']->delete_check;
            }

            if ($del_formulas == 1) {
                $formula_content = '';
            }

            // CHECK IF USER WANTS FORMULA INLINE OR NEWLINE
            $inline = '';
            $latex_inline = $this->_check_option_on_db($user_id);

            if ($latex_inline != ''){
                if ($latex_inline['0']->image_inline == 0) $inline = '<br/>';
            }

            $formula_img_content = $inline . $id_img_start . $img . $id_img_end . $inline;

            $content = "$formula_content $formula_img_content";

            $new_content .= $content;

            $this->insert_new_formula_for_evernote_user_id($user_id, $formula_id, $content_pieces[$i], $original_note->guid, md5($latex_image['resource']->data->body));
        }
        $new_content.= $content_pieces[$i];

        // 4. construct new content
        $note_new_content = $this->enml->add_enml_note_tags($new_content);

        // 5 construct new step note
        $new_note = $this->_prepare_new_note($original_note->guid, $original_note->title);
        $new_note->content = $note_new_content;

        if( isset($original_note->resources) && count($original_note->resources) ){
            $new_note->resources = array_merge ( $original_note->resources, $new_resources );
        } else {
            $new_note->resources = $new_resources;
        }

        return $new_note;
    }
    public function account_config()
    {
        log_message('debug', __METHOD__ );
        $evernote_user_id = $this->session->userdata('evernote_user_id');
        $data = array();

        $del_formulas = $this->_check_option_on_db($evernote_user_id);
        if ($del_formulas != ''){
            $data['del_formulas'] = $del_formulas['0']->delete_check;
        } else {
            $data['del_formulas'] = 0;
        }

        $latex_key = $this->_check_key_on_db($evernote_user_id);
        if ($latex_key != ''){
            $data['latex_key'] = $latex_key['0']->latex_key;
        } else {
            $data['latex_key'] = '';
        }

        $latex_inline = $this->_check_option_on_db($evernote_user_id);
        if ($latex_inline != ''){
            $data['inline_latex'] = $latex_inline['0']->image_inline;
        } else {
            $data['inline_latex'] = 1;
        }

        return $data;
    }
    public function get_last_formula_id_for_evernote_user_id($evernote_user_id)
    {
        $table = 'act_latex';

        $result = $this->db
            ->select(array('formula_id'))
            ->where('evernote_user_id', $evernote_user_id)
            ->order_by('formula_id', 'desc')
            ->limit(1)
            ->get($table);

        if ($result->num_rows() > 0) {
            return $result->row()->formula_id;
        }
        return NULL;
    }
    public function insert_new_formula_for_evernote_user_id($evernote_user_id, $formula_id, $formula_text, $guid, $md5_body)
    {
        $table = 'act_latex';

        $data['evernote_user_id']  = $evernote_user_id;
        $data['formula_id']        = $formula_id;
        $data['formula_text']      = $formula_text;
        $data['note_guid']         = $guid;
        $data['resource_md5_body'] = $md5_body;

        $result = $this->db->insert($table, $data);
    }
    public function get_formula_data_by_user_and_formula_id($evernote_user_id, $formula_id)
    {
        log_message('debug', __METHOD__);

        $table = 'act_latex';

        $result = $this->db
            ->select(array('formula_text', 'note_guid', 'resource_md5_body'))
            ->where('evernote_user_id', $evernote_user_id)
            ->where('formula_id', $formula_id)
            ->limit(1)
            ->get($table);

        if ($result->num_rows() > 0) {
            return $result->row();
        }
        return NULL;
    }
    public function update_formula_data($formula_text, $md5_body, $evernote_user_id, $formula_id)
    {
        log_message('debug', __METHOD__);

        $this->db->set('formula_text', $formula_text);
        $this->db->set('resource_md5_body', $md5_body);
        $this->db->where('evernote_user_id', $evernote_user_id);
        $this->db->where('formula_id', $formula_id);
        $this->db->update('act_latex');
    }
    public function send_data_to_db($data)
    {
        // CHECK IF USER HAS CONFIGURED OPTION ON DB
        $check_option = array (
            'evernote_user_id' => $data['evernote_user_id'],
            );

        $this->db->select('id');
        $this->db->where($check_option);
        $query = $this->db->get($this->table_name);

        // IF NOT THEN INSERT DATA
        if ($query->num_rows == 0) {
            $response = $this->db->insert($this->table_name, $data);
            return $response;
        }
        // IF YES THEN UPDATE ROWS
        $result = $query->result();
        $this->db->where('id', $result[0]->id);
        $this->db->set('delete_check', $data['delete_check']);
        $response = $this->db->update($this->table_name);
        return $response;
    }
    private function _check_option_on_db ($evernote_user_id)
    {
        log_message('debug', __METHOD__);
        $check_note = array (
            'evernote_user_id' => $evernote_user_id
            );

        $query = $this->db->get_where($this->table_name, $check_note);

        if ($query->num_rows == 0) {
            return null;
        }
        $result = $query->result();
        return $result;
    }
    public function send_key_to_db($data)
    {
        // CHECK IF USER HAS CONFIGURED $$KEY$$ ON DB
        $check_key = array (
            'evernote_user_id' => $data['evernote_user_id'],
            );

        $this->db->select('id');
        $this->db->where($check_key);
        $query = $this->db->get($this->table_name);

        // IF NOT THEN INSERT DATA
        if ($query->num_rows == 0) {
            $response = $this->db->insert($this->table_name, $data);
            return $response;
        }
        // IF YES THEN UPDATE ROWS
        $result = $query->result();
        $this->db->where('id', $result[0]->id);
        $this->db->set('latex_key', $data['latex_key']);
        $response = $this->db->update($this->table_name);
        return $response;
    }
    private function _check_key_on_db ($evernote_user_id)
    {
        log_message('debug', __METHOD__);
        $check_note = array (
            'evernote_user_id' => $evernote_user_id
            );

        $query = $this->db->get_where($this->table_name, $check_note);

        $latex_key = $query->result();

        if ($query->num_rows == 0 OR $latex_key['0']->latex_key == '') {
            $latex_key = null;
            return $latex_key;
        }
        return $latex_key;
    }
    public function send_inline_to_db($data)
    {
        // CHECK IF USER HAS CONFIGURED OPTION ON DB
        $check_option = array (
            'evernote_user_id' => $data['evernote_user_id'],
            );

        $this->db->select('id');
        $this->db->where($check_option);
        $query = $this->db->get($this->table_name);

        // IF NOT THEN INSERT DATA
        if ($query->num_rows == 0) {
            $response = $this->db->insert($this->table_name, $data);
            return $response;
        }
        // IF YES THEN UPDATE ROWS
        $result = $query->result();
        $this->db->where('id', $result[0]->id);
        $this->db->set('image_inline', $data['image_inline']);
        $response = $this->db->update($this->table_name);
        return $response;
    }
}