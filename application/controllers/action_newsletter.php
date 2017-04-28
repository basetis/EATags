<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action_newsletter extends CI_Controller {
    
    function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        $this->load->model('eat/Eat_newsletter', 'newsletter');

        $evernote_user_id = $this->session->userdata('evernote_user_id');

        $data['email'] = $this->newsletter->get_user_newsletter_emails($evernote_user_id);

        $this->load->view('header');
        $this->load->view('action_newsletter', $data);
        $this->load->view('footer');
    }
    public function save() 
    {
        $params = $this->input->post(NULL, TRUE);
        
        $email_array = array();
        $evernote_user_id = $this->session->userdata('evernote_user_id');
        
        for ($index = 0; $index < 10; $index++){
            $data['email'][$index]['email'] = $params['email_'.$index];
    
            $this->form_validation->set_rules('email_'.$index, 'e-Mail '.($index + 1), 'trim|valid_email');

            $validated_ok = $this->form_validation->run();

            if ($validated_ok) {
                if (trim($params['email_'.$index]) != '')
                    array_push($email_array, array('evernote_user_id' => $evernote_user_id, 'email' => trim($params['email_'.$index])));
            } else {
                $data['error_msg'] = "Error";
            }
        }

        if ($validated_ok) {
            $this->load->model('eat/Eat_newsletter', 'newsletter');
            $this->newsletter->save_user_newsletter_emails($evernote_user_id, $email_array);
            $data['status_msg'] = "Data saved successfully";
        }
        
        $this->load->view('header');
        $this->load->view('action_newsletter', $data);
        $this->load->view('footer');
    }

    public function remove()
    {
        $this->load->model('eat/Eat_newsletter', 'newsletter');

        $evernote_user_id = $this->session->userdata('evernote_user_id');

        $this->newsletter->delete_user_newsletter_emails($evernote_user_id);
        
        $data = array();
        $data['status_msg'] = "Newsletter e-Mails removed successfully";    
        $data['email'] = 

        $this->load->view('header');
        $this->load->view('action_newsletter',$data);
        $this->load->view('footer');
    }

}