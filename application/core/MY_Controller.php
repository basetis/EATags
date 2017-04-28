<?
class MY_Controller extends CI_Controller {

    protected $data = array();
    protected $selected_lang = 'en-US';

    public function __construct() {
        parent::__construct();

        $this->load->library('language');

        if ($this->input->post('lang')){
            if ($this->session->userdata('user_id')) {
                $selected_lang = $this->input->post('lang');
                $this->language->send_lang_to_db($selected_lang);
            } else {
                $this->session->set_userdata('selected_lang', $this->input->post('lang'));
            }
            $this->config->set_item('language', $this->input->post('lang'));
        }
        if ($this->session->userdata('user_id')) {
            $selected_lang = $this->language->get_lang_from_db();
            $this->config->set_item('language', $selected_lang);
        } elseif ($this->session->userdata('selected_lang')) {
            $this->config->set_item('language', $this->session->userdata('selected_lang'));
        } else {
            $languages = array('en' => 'en-US', 'es' => 'es-ES', 'ca' => 'ca-ES');
            $server_lang = 'en';
            if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"])) {
                $server_lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'],0,2);
                if (array_key_exists($server_lang , $languages )) {
                    $selected_lang = $languages[$server_lang];
                    $this->session->set_userdata('selected_lang', $selected_lang);
                    $this->config->set_item('language', $selected_lang);
                }
            }
        }
    }
}

