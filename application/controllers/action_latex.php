<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action_latex extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('eat/Eat_latex', 'latex');
    }
    public function send_data($type_form)
    {
        log_message('debug', __METHOD__);

        $params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $data = array(
            'evernote_user_id'  => $this->session->userdata('evernote_user_id'),
        );

        switch ($type_form) {
            case 'del_form':
                $data['delete_check'] = $params['del-latex'];
                $this->latex->send_data_to_db($data);
                break;
            case 'key_form':
                $data['latex_key'] = $params['latex-key'];
                $this->latex->send_key_to_db($data);
                break;
            case 'inline_form':
                $data['image_inline'] = $params['inline-latex'];
                $this->latex->send_inline_to_db($data);
                break;
            default:
                log_message('debug', 'WHAT\'S GOING ON' );
                break;
        }
        log_message('debug', 'sending ' . $type_form . ' form' );
    }
    private function _validate_params($method, $line, $params)
    {
        if (!$params) {
            return FALSE;
        }
        return TRUE;
    }
}