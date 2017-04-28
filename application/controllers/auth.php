<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth extends MY_Controller
{
	// CONTROLS WHEN LOGIN IS REQUESTED AFTER ACTIVATION ACCOUNT PROCESS
	// AT METHOD activate
	private $activate_callback = FALSE;
	protected $selected_lang;
	function __construct()
	{
		parent::__construct();

		$this->load->helper(array('form', 'url'));
		$this->lang->load('tank_auth');
		$this->load->language('header');
		$this->load->language('auth');
		$this->load->language('footer');
		$this->load->library('session');
	}

	function index()
	{
		if ($message = $this->session->flashdata('message')) {
			$this->load->view('auth/general_message', array('message' => $message));
		} else {
			redirect('/auth/login/');
		}
	}

	/**
	 * Login user on the site
	 *
	 * @return void
	 */
	function login()
	{

		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('account');
		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			$this->send_again();
		} else {
			if ( $this->activate_callback ){ // comes from activation account link
				$data['activate_callback'] = $this->activate_callback;
				if( $this->activate_callback == 'success'){
					$data['activate_callback_msg'] = $this->lang->line('auth_message_activation_completed');
				} else {
					$data['activate_callback_msg'] = $this->lang->line('auth_message_activation_failed');
				}
				// Back to default value
				$this->activate_callback = FALSE;
			}

			$data['login_by_username'] = ($this->config->item('login_by_username', 'tank_auth') AND
					$this->config->item('use_username', 'tank_auth'));
			$data['login_by_email'] = $this->config->item('login_by_email', 'tank_auth');
			$data['use_recaptcha'] = $this->config->item('use_recaptcha', 'tank_auth');

			$this->form_validation->set_rules('login', 'Login', 'trim|required|xss_clean');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('remember', 'Remember me', 'integer');

			// Get login for counting attempts to login
			if ($this->config->item('login_count_attempts', 'tank_auth') AND
					($login = $this->input->post('login'))) {
				$login = $this->security->xss_clean($login);
			} else {
				$login = '';
			}


			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				if ($data['use_recaptcha'])
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				else
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
			}
			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->login(
						$this->form_validation->set_value('login'),
						$this->form_validation->set_value('password'),
						$this->form_validation->set_value('remember'),
						$data['login_by_username'],
						$data['login_by_email'])) {								// success
					$this->session->set_userdata('eat.logged.ok', 1);
					if ($this->session->userdata('after_login_redirect_to')) {
						$url = $this->session->userdata('after_login_redirect_to');
						$this->session->unset_userdata('after_login_redirect_to');
						redirect($url);
					}
					redirect('account');
				} else {
					$errors = $this->tank_auth->get_error_message();
					if (isset($errors['banned'])) {								// banned user
						$this->_show_message($this->lang->line('auth_message_banned').' '.$errors['banned']);

					} elseif (isset($errors['not_activated'])) {				// not activated user
						redirect('/auth/send_again/');

					} else {													// fail
						foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					}
				}
			}
			$data['show_captcha'] = FALSE;
			if ($this->tank_auth->is_max_login_attempts_exceeded($login)) {
				$data['show_captcha'] = TRUE;
				if ($data['use_recaptcha']) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}

			$data['current_page'] = 'sign_in';
			$this->load->view('auth/login_form', $data);
		}
	}

	/**
	 * Logout user
	 *
	 * @return void
	 */
	function logout()
	{
		$this->tank_auth->logout();
		$this->session->sess_create();
		$this->_show_message($this->lang->line('auth_message_logged_out'));
	}

	/**
	 * Register user on the site
	 *
	 * @return void
	 */
	function register()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('account');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			$this->send_again();

		} elseif (!$this->config->item('allow_registration', 'tank_auth')) {	// registration is off
			$this->_show_message($this->lang->line('auth_message_registration_disabled'));

		} else {
			$use_username = $this->config->item('use_username', 'tank_auth');
			if ($use_username) {
				$this->form_validation->set_rules('username', $this->lang->line('auth_register_username'), 'trim|required|xss_clean|min_length['.$this->config->item('username_min_length', 'tank_auth').']|max_length['.$this->config->item('username_max_length', 'tank_auth').']|alpha_dash');
			}
			$this->form_validation->set_rules('email', $this->lang->line('auth_register_email'), 'trim|required|xss_clean|valid_email');
			$this->form_validation->set_rules('password', $this->lang->line('auth_register_password'), 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
			$this->form_validation->set_rules('confirm_password', $this->lang->line('auth_register_confirm'), 'trim|required|xss_clean|matches[password]');

			$captcha_registration	= $this->config->item('captcha_registration', 'tank_auth');
			$use_recaptcha			= $this->config->item('use_recaptcha', 'tank_auth');
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$this->form_validation->set_rules('recaptcha_response_field', 'Confirmation Code', 'trim|xss_clean|required|callback__check_recaptcha');
				} else {
					$this->form_validation->set_rules('captcha', 'Confirmation Code', 'trim|xss_clean|required|callback__check_captcha');
				}
			}
			$data['errors'] = array();

			$email_activation = $this->config->item('email_activation', 'tank_auth');

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->create_user(
						$use_username ? $this->form_validation->set_value('username') : '',
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password'),
						$email_activation))) {									// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					if ($email_activation) {									// send "activate" email
						$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

						$this->_send_email('activate', $data['email'], $data);

						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_1'));

					} else {
						if ($this->config->item('email_account_details', 'tank_auth')) {	// send "welcome" email

							$this->_send_email('welcome', $data['email'], $data);
						}
						unset($data['password']); // Clear password (just for any case)

						$this->_show_message($this->lang->line('auth_message_registration_completed_2').' '.anchor('/auth/login/', 'Login'));
					}
				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			if ($captcha_registration) {
				if ($use_recaptcha) {
					$data['recaptcha_html'] = $this->_create_recaptcha();
				} else {
					$data['captcha_html'] = $this->_create_captcha();
				}
			}
			$data['use_username'] = $use_username;
			$data['captcha_registration'] = $captcha_registration;
			$data['use_recaptcha'] = $use_recaptcha;
			$data['current_page'] = 'sign_up';
			$this->load->view('auth/register_form', $data);
		}
	}

	/**
	 * Send activation email again, to the same or new email address
	 *
	 * @return void
	 */
	function send_again()
	{
		if (!$this->tank_auth->is_logged_in(FALSE)) {							// not logged in or activated
			redirect('/auth/login/');
		} else {
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->change_email(
						$this->form_validation->set_value('email')))) {			// success

					$data['site_name']	= $this->config->item('website_name', 'tank_auth');
					$data['activation_period'] = $this->config->item('email_activation_expire', 'tank_auth') / 3600;

					$this->_send_email('activate', $data['email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_activation_email_sent'), $data['email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/send_again_form', $data);
		}
	}

	/**
	 * Activate user account.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function activate()
	{
		// REFACTOR
		// https://basecamp.com/1893261/projects/671537-eat-ags-2-0/todos/9700989-generar-la-vista-del
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Activate user
		if ($this->tank_auth->activate_user($user_id, $new_email_key)) {		// success
			$this->tank_auth->logout();
			$this->activate_callback = 'success';
		} else {	// fail
			$this->activate_callback = 'failed';
		}

		$this->login();

		// // OLD VERSION
		// $user_id		= $this->uri->segment(3);
		// $new_email_key	= $this->uri->segment(4);

		// // Activate user
		// if ($this->tank_auth->activate_user($user_id, $new_email_key)) {		// success
		// 	$this->tank_auth->logout();
		// 	$this->_show_message($this->lang->line('auth_message_activation_completed').' '.anchor('/auth/login/', 'Login'));

		// } else {																// fail
		// 	$this->_show_message($this->lang->line('auth_message_activation_failed'));
		// }
	}

	function testi(){
		echo ('1 -->'. $this->activate_callback . '<--');
		unset($this->activate_callback);
		die('2 -->'. $this->activate_callback . '<--');
	}

	/**
	 * Generate reset code (to change password) and send it to user
	 *
	 * @return void
	 */
	function forgot_password()
	{
		if ($this->tank_auth->is_logged_in()) {									// logged in
			redirect('');

		} elseif ($this->tank_auth->is_logged_in(FALSE)) {						// logged in, not activated
			redirect('/auth/send_again/');

		} else {
			$this->form_validation->set_rules('login', 'Email or login', 'trim|required|xss_clean');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->forgot_password(
						$this->form_validation->set_value('login')))) {

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with password activation link
					$this->_send_email('forgot_password', $data['email'], $data);

					$this->_show_message($this->lang->line('auth_message_new_password_sent'));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/forgot_password_form', $data);
		}
	}

	/**
	 * Replace user password (forgotten) with a new one (set by user).
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_password()
	{
		$user_id		= $this->uri->segment(3);
		$new_pass_key	= $this->uri->segment(4);

		$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
		$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

		$data['errors'] = array();

		if ($this->form_validation->run()) {								// validation ok
			if (!is_null($data = $this->tank_auth->reset_password(
					$user_id, $new_pass_key,
					$this->form_validation->set_value('new_password')))) {	// success

				$data['site_name'] = $this->config->item('website_name', 'tank_auth');

				// Send email with new password
				$this->_send_email('reset_password', $data['email'], $data);

				$this->_show_message($this->lang->line('auth_message_new_password_activated').' '.anchor('/auth/login/', 'Login'));

			} else {														// fail
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		} else {
			// Try to activate user by password key (if not activated yet)
			if ($this->config->item('email_activation', 'tank_auth')) {
				$this->tank_auth->activate_user($user_id, $new_pass_key, FALSE);
			}

			if (!$this->tank_auth->can_reset_password($user_id, $new_pass_key)) {
				$this->_show_message($this->lang->line('auth_message_new_password_failed'));
			}
		}
		$this->load->view('auth/reset_password_form', $data);
	}

	/**
	 * Change user password
	 *
	 * @return void
	 */
	function change_password()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|xss_clean|min_length['.$this->config->item('password_min_length', 'tank_auth').']|max_length['.$this->config->item('password_max_length', 'tank_auth').']');
			$this->form_validation->set_rules('confirm_new_password', 'Confirm new Password', 'trim|required|xss_clean|matches[new_password]');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if ($this->tank_auth->change_password(
						$this->form_validation->set_value('old_password'),
						$this->form_validation->set_value('new_password'))) {	// success
					$this->_show_message($this->lang->line('auth_message_password_changed'));

				} else {														// fail
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_password_form', $data);
		}
	}

	/**
	 * Change user email
	 *
	 * @return void
	 */
	function change_email()
	{
		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');

			$data['errors'] = array();

			if ($this->form_validation->run()) {								// validation ok
				if (!is_null($data = $this->tank_auth->set_new_email(
						$this->form_validation->set_value('email'),
						$this->form_validation->set_value('password')))) {			// success

					$data['site_name'] = $this->config->item('website_name', 'tank_auth');

					// Send email with new email address and its activation link
					$this->_send_email('change_email', $data['new_email'], $data);

					$this->_show_message(sprintf($this->lang->line('auth_message_new_email_sent'), $data['new_email']));

				} else {
					$errors = $this->tank_auth->get_error_message();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			$this->load->view('auth/change_email_form', $data);
		}
	}

	/**
	 * Replace user email with a new one.
	 * User is verified by user_id and authentication code in the URL.
	 * Can be called by clicking on link in mail.
	 *
	 * @return void
	 */
	function reset_email()
	{
		$user_id		= $this->uri->segment(3);
		$new_email_key	= $this->uri->segment(4);

		// Reset email
		if ($this->tank_auth->activate_new_email($user_id, $new_email_key)) {	// success
			$this->tank_auth->logout();
			$this->_show_message($this->lang->line('auth_message_new_email_activated').' '.anchor('/auth/login/', 'Login'));

		} else {																// fail
			$this->_show_message($this->lang->line('auth_message_new_email_failed'));
		}
	}

	/**
	 * Delete user from the site (only when user is logged in)
	 *
	 * @return void
	 */
	function unregister()
	{
		log_message('debug', __METHOD__);
		$this->load->language('account');

		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {
			$data = array();
			$data['current_page'] = 'profile_config';
			$data['current_section'] = 'profile_config';

			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			if ($this->form_validation->run()) {	// validation ok
				if ($this->tank_auth->delete_user($this->input->post('password'))) {		// success
					$delete_message = $this->lang->line('auth_unregister_success');
				} else {
					log_message('debug', "delete user fails");
					$errors = $this->tank_auth->get_error_message();			// fail
					$data['errors'] = array();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
					log_message('debug', print_r($data,true));
				}
			}
			log_message('debug', "After form validation");
			if(!isset($delete_message)){
				$this->load->view('auth/unregister_form', $data);
			} else {
				$this->session->sess_create();
				$this->_show_message($delete_message);
			}
		}
	}
	function evernote_logout()
	{
		$this->load->language('account');

		if (!$this->tank_auth->is_logged_in()) {								// not logged in or not activated
			redirect('/auth/login/');

		} else {

			$data = array();
			$data['current_page'] = 'profile_config';
			$data['current_section'] = 'profile_config';

			$this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean');

			$this->load->model('tank_auth/users', 'users');
			$user_id = $this->session->userdata('evernote_user_id');
			$access_token = $this->users->get_user_evernote_access_token_by_evernote_user_id($user_id);

			if ($this->form_validation->run()) {	// validation ok
				if ($this->tank_auth->check_password($this->input->post('password'))) {

					$this->_delete_user_data();

					$this->evernote->revokeLongSession($access_token); // revoking token

					$this->load->model('users'); // deleting evernote data
					$this->users->delete_evernote_data($user_id);

					$delete_message = $this->lang->line('auth_logout_success');

				} else {
					$errors = $this->tank_auth->get_error_message();			// fail
					$data['errors'] = array();
					foreach ($errors as $k => $v)	$data['errors'][$k] = $this->lang->line($v);
				}
			}
			if(!isset($delete_message)){
				$this->load->view('auth/logout_form', $data);
			} else {

				$this->_show_message($delete_message);
			}
		}
	}
	private function _delete_user_data()
	{
		$this->load->model('eat/eat_flickr', 'eat_flickr');
		$this->eat_flickr->flickr_evernote_logout();
		$this->load->model('eat/eat_twitter', 'eat_twitter');
		$this->eat_twitter->twitter_evernote_logout();
		$this->load->model('eat/eat_wordpress', 'eat_wordpress');
		$this->eat_wordpress->wordpress_evernote_logout();
		$this->load->model('eat/eat_gmail', 'eat_gmail');
		$this->eat_gmail->gmail_evernote_logout();
	}

	/**
	 * Show info message
	 *
	 * @param	string
	 * @return	void
	 */
	private function _show_message($message)
	{
		$this->session->set_flashdata('alert_message', $message);
		redirect('home');
	}

	/**
	 * Send email message of given type (activate, forgot_password, etc.)
	 *
	 * @param	string
	 * @param	string
	 * @param	array
	 * @return	void
	 */
	function _send_email($type, $email, &$data)
	{
		$this->lang->load('email');
        $this->load->library('email');
		$this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
		$this->email->to($email);
		$this->email->subject(sprintf($this->lang->line('auth_subject_'.$type), $this->config->item('website_name', 'tank_auth')));
		$this->email->message($this->load->view('email/'.$type.'-html', $data, TRUE));
		$this->email->set_alt_message($this->load->view('email/'.$type.'-txt', $data, TRUE));
		$this->email->send();
	}

	/**
	 * Create CAPTCHA image to verify user as a human
	 *
	 * @return	string
	 */
	function _create_captcha()
	{
		$this->load->helper('captcha');

		$cap = create_captcha(array(
			'img_path'		=> './'.$this->config->item('captcha_path', 'tank_auth'),
			'img_url'		=> base_url().$this->config->item('captcha_path', 'tank_auth'),
			'font_path'		=> './'.$this->config->item('captcha_fonts_path', 'tank_auth'),
			'font_size'		=> $this->config->item('captcha_font_size', 'tank_auth'),
			'img_width'		=> $this->config->item('captcha_width', 'tank_auth'),
			'img_height'	=> $this->config->item('captcha_height', 'tank_auth'),
			'show_grid'		=> $this->config->item('captcha_grid', 'tank_auth'),
			'expiration'	=> $this->config->item('captcha_expire', 'tank_auth'),
		));

		// Save captcha params in session
		$this->session->set_flashdata(array(
				'captcha_word' => $cap['word'],
				'captcha_time' => $cap['time'],
		));

		return $cap['image'];
	}

	/**
	 * Callback function. Check if CAPTCHA test is passed.
	 *
	 * @param	string
	 * @return	bool
	 */
	function _check_captcha($code)
	{
		$time = $this->session->flashdata('captcha_time');
		$word = $this->session->flashdata('captcha_word');

		list($usec, $sec) = explode(" ", microtime());
		$now = ((float)$usec + (float)$sec);

		if ($now - $time > $this->config->item('captcha_expire', 'tank_auth')) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_captcha_expired'));
			return FALSE;

		} elseif (($this->config->item('captcha_case_sensitive', 'tank_auth') AND
				$code != $word) OR
				strtolower($code) != strtolower($word)) {
			$this->form_validation->set_message('_check_captcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * Create reCAPTCHA JS and non-JS HTML to verify user as a human
	 *
	 * @return	string
	 */
	function _create_recaptcha()
	{
		$this->load->helper('recaptcha');

		// Add custom theme so we can get only image
		$options = "<script>var RecaptchaOptions = {theme: 'custom', custom_theme_widget: 'recaptcha_widget'};</script>\n";

		// Get reCAPTCHA JS and non-JS HTML
		$html = recaptcha_get_html($this->config->item('recaptcha_public_key', 'tank_auth'));

		return $options.$html;
	}

	/**
	 * Callback function. Check if reCAPTCHA test is passed.
	 *
	 * @return	bool
	 */
	function _check_recaptcha()
	{
		$this->load->helper('recaptcha');

		$resp = recaptcha_check_answer($this->config->item('recaptcha_private_key', 'tank_auth'),
				$_SERVER['REMOTE_ADDR'],
				$_POST['recaptcha_challenge_field'],
				$_POST['recaptcha_response_field']);

		if (!$resp->is_valid) {
			$this->form_validation->set_message('_check_recaptcha', $this->lang->line('auth_incorrect_captcha'));
			return FALSE;
		}
		return TRUE;
	}

	/**
	* TODO:
	*  - Parece que las tokens obtenidas así duran muy poco, hay que revisar como conseguir un tiempo de expiración más largo
	*  - Se tendrá que guardar en Base de Datos los tokens del usuario (ahora por temas de test, se hace un echo de lo que recibimos de Google)
	*/
	public function session($provider)
    {
        $this->load->helper('url_helper');
        $this->load->spark('oauth2/0.4.0');

        $result = $this->common->get_google_api_client_id_by_working_copy($this->session->userdata('evernote_user_id'));

        $provider = $this->oauth2->provider($provider, array(
            'id' => $result['google_client_id'],
            'secret' => $result['google_secret'],
            'scope' => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://mail.google.com/"
        ));

        if ( ! $this->input->get('code'))
        {
            // By sending no options it'll come back here
            redirect($provider->authorize() . "&access_type=offline" );
            log_message('debug', "after authorize");
        }
        else
        {
            // Howzit?
            try
            {
                $token = $provider->access($_GET['code']);
                $user = $provider->get_user_info($token);

                // Here you should use this information to A) look for a user B) help a new user sign up with existing data.
                // If you store it all in a cookie and redirect to a registration page this is crazy-simple.
                // echo "<pre>Tokens: ";
                // var_dump($token);

                // echo "\n\nUser Info: ";
                // var_dump($user);

                // echo "\n\nNow is: ";
                // var_dump(date("H:i:s"));
                // echo "\n\nand Token expires at: ";
                // var_dump(date("H:i:s",$token->expires));

                $data = array(
                	'evernote_user_id' 	=> $this->session->userdata('evernote_user_id'),
                	'gmail_token'		=> $token->access_token,
                	'token_expiration'	=> $token->expires,
                	'token_refresh'		=> $token->refresh_token,
                	'email'				=> $user['email'],
                	'uid'				=> $user['uid'],
                	'type'				=> 'gmail_draft',
                	);

                $this->load->model('eat/eat_gmail', 'eat_gmail');
                $this->eat_gmail->set_data_in_db($data);
                redirect('account/features/gmail');
            }

            catch (OAuth2_Exception $e)
            {
            	log_message('error', __METHOD__);
            	log_message('error', 'That didn\'t work: '.$e);
            	$this->_show_message($this->lang->line('auth_gmail_fail'));
            }

        }
    }
    public function set_lang()
    {
    	log_message('debug', __METHOD__);

    	$params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $data = array(
            'evernote_user_id'  => $this->session->userdata('evernote_user_id'),
            'lang_code'     	=> $params['gmail-lang'],
            );

    	$this->load->model('eat/eat_gmail', 'eat_gmail');
        return $this->eat_gmail->set_lang_in_db($data);
    }
    public function del($feature)
    {
    	log_message('debug', __METHOD__);

        $params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $action_type = $params['action_type'];

    	$this->load->model('eat/'.$feature, 'feature');
        return $this->feature->account_config($action_type);
    }
    private function _validate_params($method, $line, $params)
    {
        if (!$params) {
            return FALSE;
        }
        return TRUE;
    }
}

/* End of file auth.php */
/* Location: ./application/controllers/auth.php */