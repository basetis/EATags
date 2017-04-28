<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once BASEPATH . "../evernote-sdk-php/bootstrap.php";
require_once $GLOBALS['THRIFT_ROOT'].'/packages/UserStore/UserStore.php';
require_once $GLOBALS['THRIFT_ROOT'].'/packages/NoteStore/NoteStore.php';
use EDAM\UserStore\UserStoreClient;
use EDAM\NoteStore\NoteStoreClient;
use EDAM\Types\Tag;
use EDAM\Error\EDAMErrorCode;
use EDAM\Error\EDAMUserException;
use EDAM\Error\EDAMSystemException;
use EDAM\Error\EDAMNotFoundException;

class Home extends MY_Controller {

	protected $selected_lang;

	function __construct()
	{
		parent::__construct();
		$this->load->language('home');
		$this->load->language('header');
		$this->load->language('footer');
	}
	public function index()
	{
		$view_name = 'home';
		$data = array();
		$params = $this->input->get(NULL, TRUE);
		$this->load->model('Evernote_oauth','ev_oauth');
		if (isset($params['action'])) {
			if ($params['action'] == 'callback') {
				if ($this->ev_oauth->handle_callback()) {
					$access_token_info = $this->ev_oauth->get_token_credentials();
					if ($access_token_info) {
						$this->load->model('tank_auth/users');
						$this->users->update_evernote_oauth_info($access_token_info);
						$params = array(
							'evernote_access_token' => $this->session->userdata('evernote_access_token'),
							'evernote_user_id'      => $this->session->userdata('evernote_user_id')
						);
						curl_post_async(base_url() . 'home/evernote_callback', $params);
						redirect('account/features/first_access');
						return;
					} else {
						log_message('error', $this->ev_oauth->last_error);
						$data['oauth_error'] = $this->ev_oauth->last_error;
					}
				} else {
					log_message('error', $this->ev_oauth->last_error);
					$data['oauth_error'] = $this->ev_oauth->last_error;
				}
			} else {
				log_message('debug', 'Received Action: ' . $params['action']);
			}
		} else {
			log_message('debug', 'No Action defined on querystring.');
		}

		$this->load->view('header');
		$this->load->view($view_name, $data);
		$this->load->view('footer');
	}
	public function evernote_callback()
	{
		log_message('debug', __METHOD__);

		$params = $this->input->post(NULL, TRUE);
		if ($params['evernote_access_token'] && $params['evernote_user_id']) {
			$this->evernote->init($params['evernote_access_token'], $params['evernote_user_id']);

			$result = $this->evernote->create_initial_tags(
						$params['evernote_access_token'],
						$params['evernote_user_id']
					);
		} else {
			log_message('error', 'Access Token and/or Evernote user id are not defined');
		}

		if (!$result) log_message('error', 'Problems creating initial tags');

		return '{"success":"success"}';
	}
	public function evernote_oauth_authorize()
	{
		$this->load->model('Evernote_oauth','ev_oauth');
		if ($this->ev_oauth->get_temporary_credentials()) {
			redirect($this->ev_oauth->get_authorization_url());
		} else {
			log_message('error', $this->ev_oauth->last_error);
		}
	}

	public function about(){
		$this->load->language('about');
		$data['current_page'] = 'about';
		$this->load->view('about', $data);
	}

	public function faqs(){
		$this->load->language('faq');
		$data['current_page'] = 'faqs';
		$this->load->view('faqs', $data);
	}

	public function features(){
		$this->load->model('eat/Tags');
		$this->load->language('features');
		$data['tags'] = $this->Tags->get_tags_basic_info();
		$data['current_page'] = 'features';
		$this->load->view('features', $data);
	}

	public function feat($key)
	{
		$this->load->language('f'.$key);
		$data['current_page'] = 'f'.$key;
		$this->load->view('f'.$key,$data);
	}

	public function contact(){
		$data['current_page'] = 'contact';
		$this->load->language('contact');
		if( $this->input->post('action_type') == 'contact_form' ){
			// CONTACT FORM IS REQUIRED
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('message', 'Message', 'trim|required|xss_clean');
			if ($this->form_validation->run()) {
				// ALL CORRECT, SAVE MAIL ?
				$title = 'eat.contact.form from:' . set_value('email') . ' -> ' . set_value('title');
				$send_to = 'support@eatags.com';
				$message = set_value('message');
				if( $this->_send_email($title, $send_to, $message) ){
					$data['mail_sending'] = 'success';
					$data['mail_sending_msg'] = $this->lang->line('home_email_sent');
				} else {
					$data['mail_sending'] = 'fails';
					$data['mail_sending_msg'] = $this->lang->line('home_email_sent_fail');
				}
			}
		}
		$this->load->view('contact', $data);
	}

	public function legal(){
		$this->load->language('legal');
		$data['current_page'] = 'legal';
		$this->load->view('legal', $data);
	}

	public function cookies(){
		$this->load->language('cookies');
		$data['current_page'] = 'cookies';
		$this->load->view('cookies', $data);
	}

	public function privacy(){
		$this->load->language('privacy');
		$data['current_page'] = 'privacy';
		$this->load->view('privacy', $data);
	}

	public function eat_team(){
		$data['current_page'] = 'eat-team';
		$this->load->view('eat_team', $data);
	}

	private function _send_email($title, $email, $message)
	{
		$this->load->library('email');
		$this->email->from('noreply@eatags.com');
		$this->email->reply_to('noreply@eatags.com');
		$this->email->to($email);
		$this->email->subject($title);
		$this->email->message($message);
		$result = $this->email->send();
		log_message('debug', $this->email->print_debugger());
		return $result;
	}

	public function admin_create_tags($key = NULL) {
		log_message('debug', __METHOD__);
		if (ENVIRONMENT != 'production') {
			log_message('debug', ENVIRONMENT);
			redirect("/");
		}
		if ($this->session->userdata('evernote_user_id') != DEVELOPER_EVERNOTE_PRODUCTION_USER_ID) redirect("/");
		if ($key == ADMIN_KEY) {
			$this->load->model('eatagsadmin');
			$this->eatagsadmin->create_remaining_eatags_for_all_users();
		} else {
			redirect("/");
		}

	}
	public function accept_cookies() {
		log_message('debug', __METHOD__);
		$cookie = array(
                    'name'   => 'eatags_confirm_cookies',
                    'value'  => 'cookies_accepted',
                    'expire' =>  time() + (10 * 365 * 24 * 60 * 60),
                    'secure' => false
                );
		$this->input->set_cookie($cookie);
		return true;
	}
	public function update_wp_pass()
	{
		$this->load->model('eat/eat_wordpress', 'wp');
		$this->wp->set_all_users_encrypted_wp_pass();
		echo "updated";
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */