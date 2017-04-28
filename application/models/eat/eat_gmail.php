<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_gmail extends MY_Model
{
	private $table_name                 = 'action_gmail';
	private $user_google_table_name     = 'user_google_auth';
	private $label_keys_table_name      = 'gmail_draft_label_keys';
	private $google_type 				= 'gmail_draft';

	function __construct()
	{
		parent::__construct();
	}
	/*====================
	== ACTION FUNCTIONS ==
	====================*/
	public function execute_action($user_id, $user_token, $original_note, $options)
	{
		log_message('debug', __METHOD__);

		$this->error_msg = '';

		$this->load->library('VivOAuthIMAP');

		// 1. get gmail token
		$google_data = $this->get_google_data_from_db($user_id);
		$gmail_token = ($google_data != '') ? $google_data[0]->gmail_token : '';
		log_message('debug', 'hay token? ' . print_r($gmail_token, true));
		if ($gmail_token == '') {
			$this->error_msg = __METHOD__ . " - User has no Gmail token";
		}

		if (!$this->error_msg) {
			$token_expiration = $google_data[0]->token_expiration;
			$refresh_token = $google_data[0]->token_refresh;
			$time_now = time();
			$expiration_time = $token_expiration - $time_now;
			$expiration_time_min = $expiration_time/60;
			log_message('debug', "el token expira en: $expiration_time_min min");
			if ($expiration_time_min < 5) {
				// token is near to die. We must refresh
				log_message('debug', 'intentando renovar token');
				$gmail_token = $this->get_new_token($user_id, $refresh_token, 'google');
			}
			$usermail = $google_data[0]->email;

			// 2. get current note content
			$current_note_content = $original_note->content;

			// 3. clean note content from Evernote headers
			$current_note_content = $this->enml->remove_enml_note_tags($current_note_content);

			// 4. we prepare the mail

			$this->vivoauthimap->host = 'ssl://imap.gmail.com';
			$this->vivoauthimap->port = 993;
			$this->vivoauthimap->username = $usermail;
			$this->vivoauthimap->accessToken = $gmail_token;

			$from = $usermail;

			$to = '';

			$message = $current_note_content;

			$subject = $original_note->title;

			$isLogged = $this->vivoauthimap->login();

			// 5. we get the mail folders to get the Drafts one
			$mailboxes = $this->vivoauthimap->listFolders();
			$sel_lang = $this->get_sel_lang_in_db($user_id);
			$mailboxes_lang = 'Drafts';
			if ($sel_lang) {
				// USER HAS A GMAIL LANGUAGE SELECTED AT DB
				$mailboxes_lang = $sel_lang->lang_code;
				$this->db->select('lang_literal');
				$this->db->from($this->label_keys_table_name);
				$this->db->where('lang_code', $mailboxes_lang);
				$query = $this->db->get();
				$result = $query->result();
				$mailboxes_lang = $result[0]->lang_literal;
				$mailboxes_lang = rtrim($mailboxes_lang);
			}
			$draft_mailbox = array_filter($mailboxes, function($var) use($mailboxes_lang) {
				return(strpos($var, $mailboxes_lang));
			});
			$draft_mailbox = array_values($draft_mailbox);

			if (!empty($draft_mailbox)) {
				$mailbox = rtrim($draft_mailbox[0]);

				// 6. we send the note to gmail drafts

				if ($isLogged) {
					$result = $this->vivoauthimap->appendMessage($mailbox, $message, $from, $to, $subject, '', '', 'text/html');
					log_message('debug', 'Note sent to draft correctly');
				} else {
					$this->error_msg = __METHOD__ . "Login failed. Check your Access Token, user $user_id";
				}
				$this->vivoauthimap->logout();
			} else {
				$this->error_msg = __METHOD__ . " - User $user_id has to config drafts folder";
			}
		}

		if ($this->error_msg) log_message('error', $this->error_msg);

		$new_note = $this->_prepare_new_note($original_note->guid, $original_note->title);
		// Gmail Action don't affect to the note object, return receive note on result
		return $new_note;
	}
	public function account_config($from_callback = FALSE)
	{
		log_message('debug', __METHOD__ );
		$user_id = $this->session->userdata('evernote_user_id');
		$response = array(
			'gmail_username'    	=> '',
			'comes_from_callback' 	=> $from_callback,
			'languages'				=> '',
			'sel_lang'				=> 'en-US',
		);
		$lang = array();
		$languages = $this->get_lang_from_db();
		foreach ($languages as $key => $value) {
			   $lang[$value->lang_code] = $value->lang_name;
		}
		asort($lang);
		$response['languages'] = $lang;

		if( $from_callback == 'delete'){
            // DELETE USER GMAIL DATA FROM DB
            $response['delete_done'] = $this->_delete_user_data_from_db();
            return $response;
        }

        $google_data = $this->get_google_data_from_db($user_id);
        if ($google_data){
        	// USER HAS GMAIL INFO AT DB
        	foreach ($google_data as $key => $value) {
        		if ($value->type == 'gmail_draft') {
					$response['gmail_username'] = $value->email;
        		}
        	}
        }

		$sel_lang = $this->get_sel_lang_in_db($user_id);
		if ($sel_lang) {
			// USER HAS A GMAIL LANGUAGE SELECTED AT DB
			$response['sel_lang'] = $sel_lang->lang_code;
		}

		return $response;
	}
	public function set_data_in_db($data)
	{
		log_message('debug', __METHOD__);
		// CHECK IF USER HAS AN ENTRY ON DB

		if (!$this->session->userdata('evernote_user_id')) {
			$user_id = $data['evernote_user_id'];
		} else {
			$user_id = $this->session->userdata('evernote_user_id');
		}

		$this->db->where('evernote_user_id', $user_id);
		$this->db->where('type', $this->google_type);
		$query = $this->db->get($this->user_google_table_name);

		// IF NOT THEN INSERT DATA
		if ($query->num_rows == 0) {
			$response = $this->db->insert($this->user_google_table_name, $data);
			return $response;
		}
		// IF YES THEN UPDATE ROWS
		$result = $query->result();
		$email = $result[0]->email;
		if (array_key_exists('email', $data)) {
			if ($data['email'] != $email) {
				$email = $data['email'];
			}
		}
		$token_refresh = $result[0]->token_refresh;
		if (array_key_exists('token_refresh', $data)) {
			$token_refresh = $data['token_refresh'];
		}
		$uid = $result[0]->uid;
		if (array_key_exists('uid', $data)) {
			if ($data['uid'] != $uid) {
				$uid = $data['uid'];
			}
		}
		$result = $query->result();
		$this->db->where('id', $result[0]->id);
		$this->db->set('gmail_token', $data['gmail_token']);
		$this->db->set('token_expiration', $data['token_expiration']);
		$this->db->set('token_refresh', $token_refresh);
		$this->db->set('email', $email);
		$this->db->set('uid', $uid);
		$response = $this->db->update($this->user_google_table_name);
		return $response;
	}
	private function get_google_data_from_db($evernote_user_id)
	{
		log_message('debug', __METHOD__);

		$this->db->where('evernote_user_id', $evernote_user_id);
		$this->db->where('type', $this->google_type);
		$query = $this->db->get($this->user_google_table_name);

		$result = $query->result();

		if ($query->num_rows == 0) {
			$result = null;
		}
		return $result;
	}
	private function get_lang_from_db()
	{
		log_message('debug', __METHOD__);

		$query = $this->db->get($this->label_keys_table_name);

		if ($query->num_rows == 0) {
			$result = null;
		} else {
			$result = $query->result();
			foreach ($result as $key => $value) {
				if ( $value->lang_name == NULL ) {
					unset($result[$key]);
				}
			}
		}
		return $result;
	}
	public function set_lang_in_db($data)
	{
		log_message('debug', __METHOD__);
		$this->db->select('*');
		$this->db->from($this->table_name);
		$this->db->join($this->user_google_table_name, $this->table_name.'.user_google_auth_id = '.$this->user_google_table_name.'.id');
		$this->db->where($this->user_google_table_name.'.evernote_user_id', $data['evernote_user_id']);
		$this->db->where($this->user_google_table_name.'.type', $this->google_type);
		$query = $this->db->get();
		if ($query->num_rows() == 0) {
			$this->db->select('id');
			$this->db->where('evernote_user_id', $data['evernote_user_id']);
			$this->db->where('type', $this->google_type);
			$query2 = $this->db->get($this->user_google_table_name);
			$result = $query2->result();
			$data2 = array(
				'user_google_auth_id'	=> $result[0]->id,
				'lang_code'				=> $data['lang_code'],
				);
			$response = $this->db->insert($this->table_name, $data2);
			return $response;
		}

		$result = $query->result();
		$this->db->where('user_google_auth_id', $result[0]->user_google_auth_id);
		$this->db->set('lang_code', $data['lang_code']);
		$response = $this->db->update($this->table_name);
		return $response;
	}
	private function get_sel_lang_in_db($evernote_user_id)
	{
		log_message('debug', __METHOD__);
		$this->db->select($this->table_name.'.lang_code');
		$this->db->from($this->table_name);
		$this->db->join($this->user_google_table_name, $this->table_name.'.user_google_auth_id = '.$this->user_google_table_name.'.id');
		$this->db->where($this->user_google_table_name.'.evernote_user_id', $evernote_user_id);
		$this->db->where($this->user_google_table_name.'.type', $this->google_type);
		$query = $this->db->get();
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}
	private function _delete_user_data_from_db()
	{
        log_message('debug', __METHOD__);
        $user_id = $this->session->userdata('evernote_user_id');

        $google_data = $this->get_google_data_from_db($user_id);
        foreach ($google_data as $key => $value) {
        	if ($value->type == $this->google_type) {
        		$user_google_auth_id = $value->id;
		        if ($user_google_auth_id) {
		        	$this->db->where('user_google_auth_id', $user_google_auth_id);
					$this->db->delete($this->table_name);
		        }
		        $this->db->where('evernote_user_id', $user_id);
		        $this->db->where('type', $this->google_type);
				return $this->db->delete($this->user_google_table_name);
        	}
        }
        return NULL;
    }
    public function gmail_evernote_logout()
    {
        $this->_delete_user_data_from_db();
    }
    private function get_new_token($user_id, $refresh_token, $provider)
    {
    	log_message('debug', __METHOD__);
    	$this->load->helper('url_helper');
        $this->load->spark('oauth2/0.4.0');

		$result = $this->common->get_google_api_client_id_by_working_copy($user_id);

        $provider = $this->oauth2->provider($provider, array(
            'id' => $result['google_client_id'],
            'secret' => $result['google_secret'],
            'scope' => "https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email https://mail.google.com/"
        ));
        $options = array(
        		'grant_type' => 'refresh_token',
        	);

        $token = $provider->access($refresh_token, $options);
        $user = $provider->get_user_info($token);

        $data = array(
        	'evernote_user_id' 	=> $user_id,
        	'gmail_token'		=> $token->access_token,
        	'token_expiration'	=> $token->expires,
        	'type'				=> 'gmail_draft',
        	);
        $this->set_data_in_db($data);

        return $token->access_token;
    }
}