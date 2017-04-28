<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_profile extends CI_Model
{
    private $table_name                = 'users';
    private $table_name_ml             = 'user_active_mailings';
    public  $evernote_user_id          = 'evernote_user_id';

    function __construct()
    {
        parent::__construct();
        $this->lang->load('tank_auth');
    }

    /*==================================
    == PROFILE CONFIGURATION FUNCTION ==
    ==================================*/

    public function account_config()
    {
        $response = array( );

        if( $this->input->post('action_type') == 'change_mail' ){
            // ADAPTED FROM controllers/auth/change_email
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

            $response['errors'] = array();

            if ($this->form_validation->run()) {                                // validation ok
                if (!is_null($data = $this->tank_auth->set_new_email(
                        $this->form_validation->set_value('email'),
                        $this->form_validation->set_value('password')))) {          // success

                    $data['site_name'] = $this->config->item('website_name', 'tank_auth');

                    // Send email with new email address and its activation link
                    $this->send_email('change_email', $data['new_email'], $data);

                    $response['config_message'] = sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']);
                    //$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));

                } else {
                    $errors = $this->tank_auth->get_error_message();
                    foreach ($errors as $k => $v)   $response['errors'][$k] = $this->lang->line($v);
                }
            }
        } elseif( $this->input->post('action_type') == 'change_password' ){
            $this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[5]|xss_clean');
            if ($this->form_validation->run()) {
                $this->load->library('tank_auth');
                $pass_update = $this->tank_auth->change_password($this->input->post('old_password'), $this->input->post('new_password')) ;
                $response['config_saved'] = ($pass_update)? $pass_update : 0 ;
            }
        }
        return $response;
    }

    public function get_mail_by_evernote_user_id( $user_id ){
        $this->db->select('email');
        $this->db->where('id', $user_id);
        $query = $this->db->get($this->table_name);
        if ($query->num_rows() == 1) return $query->row()->email;
        return NULL;
    }

    private function _show_message($message)
    {
        $this->session->set_flashdata('alert_message', $message);
        return ;
    }

    /**
     * Send email message of given type (activate, forgot_password, etc.)
     *
     * @param   string
     * @param   string
     * @param   array
     * @param   string
     * @return  void
     */
    public function send_email($type, $email, &$data, $evernote_user_id = '')
    {
        $this->load->library('language');

        $language = ($evernote_user_id != '') ? $this->language->get_lang_from_db($evernote_user_id) : $this->config->item('language');

        $this->lang->load('email', $language);
        $this->lang->load('tank_auth', $language);
        $this->lang->load('email_subject', $language);
        $this->load->language('email', 'es-ES');
        $this->load->library('email');

        $this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
        $this->email->to($email);

        if ($evernote_user_id != '') {
            $this->email->subject(sprintf($this->lang->line('email_subject_rate_limit'), $this->config->item('website_name', 'tank_auth')));
        } else {
            $this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
        }

        $this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
        $this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
        // $this->email->send();
        if (!$this->email->send()) {
            log_message('error', $this->email->print_debugger());
        }
    }
    public function send_data_to_db($data)
    {
        if ($data['regist_mailing'] == 'accept') {
            $mldata = array(
                'user_id'       => $data['user_id'],
                'mailing_id'    => $data['mailing_id'],
                );
            $query = $this->db->insert($this->table_name_ml, $mldata);
        } else if ($data['regist_mailing'] == 'refuse') {
            $this->db->where('user_id', $data['user_id']);
            $query = $this->db->delete($this->table_name_ml);
        }
        return $query;
    }
    public function check_ml_from_db ($user_id)
    {
        $check_ml = array (
            'user_id' => $user_id
            );
        $query = $this->db->get_where($this->table_name_ml, $check_ml);

        if ($query->num_rows == 0) {
            return FALSE;
        }
        return TRUE;
    }
}