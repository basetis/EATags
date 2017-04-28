<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Users
 *
 * This model represents user authentication data. It operates the following tables:
 * - user account data,
 * - user profiles
 *
 * @package	Tank_auth
 * @author	Ilya Konyukhov (http://konyukhov.com/soft/)
 */
class Users extends CI_Model
{
	private $table_name			= 'users';			// user accounts
	private $profile_table_name	= 'user_profiles';	// user profiles
	private $ci;

	function __construct()
	{
		parent::__construct();

		$this->ci =& get_instance();
		$this->table_name			= $this->ci->config->item('db_table_prefix', 'tank_auth').$this->table_name;
		$this->profile_table_name	= $this->ci->config->item('db_table_prefix', 'tank_auth').$this->profile_table_name;
	}

	/**
	 * Get user record by Id
	 *
	 * @param	int
	 * @param	bool
	 * @return	object
	 */
	function get_user_by_id($user_id, $activated)
	{
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}
	function get_user_by_evernote_user_id($evernote_user_id)
	{
		$this->db->where('evernote_user_id', $evernote_user_id);
		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$row->evernote_access_token = $this->eatagscrypt->fnDecrypt($row->evernote_access_token, EVERNOTE_TOKEN_SALT);
			return $row;
		}
		return NULL;
	}
	/**
	 * Get user record by login (username or email)
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_login($login)
	{
		$this->db->where('LOWER(username)=', strtolower($login));
		$this->db->or_where('LOWER(email)=', strtolower($login));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$row->evernote_access_token = $this->eatagscrypt->fnDecrypt($row->evernote_access_token, EVERNOTE_TOKEN_SALT);
			return $row;
		}
		return NULL;
	}

	/**
	 * Get user record by username
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_username($username)
	{
		$this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) {
			$row = $query->row();
			$row->evernote_access_token = $this->eatagscrypt->fnDecrypt($row->evernote_access_token, EVERNOTE_TOKEN_SALT);
			return $row;
		}
		return NULL;
	}

	/**
	 * Get user record by email
	 *
	 * @param	string
	 * @return	object
	 */
	function get_user_by_email($email)
	{
		$this->db->where('LOWER(email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() == 1) return $query->row();
		return NULL;
	}

	/**
	 * Check if username available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_username_available($username)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(username)=', strtolower($username));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Check if email available for registering
	 *
	 * @param	string
	 * @return	bool
	 */
	function is_email_available($email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('LOWER(email)=', strtolower($email));
		$this->db->or_where('LOWER(new_email)=', strtolower($email));

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 0;
	}

	/**
	 * Create new user record
	 *
	 * @param	array
	 * @param	bool
	 * @return	array
	 */
	function create_user($data, $activated = TRUE)
	{
		$data['created'] = date('Y-m-d H:i:s');
		$data['activated'] = $activated ? 1 : 0;

		if ($this->db->insert($this->table_name, $data)) {
			$user_id = $this->db->insert_id();
			if ($activated)	$this->create_profile($user_id);
			return array('user_id' => $user_id);
		}
		return NULL;
	}

	/**
	 * Activate user if activation key is valid.
	 * Can be called for not activated users only.
	 *
	 * @param	int
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function activate_user($user_id, $activation_key, $activate_by_email)
	{
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		if ($activate_by_email) {
			$this->db->where('new_email_key', $activation_key);
		} else {
			$this->db->where('new_password_key', $activation_key);
		}
		$this->db->where('activated', 0);
		$query = $this->db->get($this->table_name);

		if ($query->num_rows() == 1) {

			$this->db->set('activated', 1);
			$this->db->set('new_email_key', NULL);
			$this->db->where('id', $user_id);
			$this->db->update($this->table_name);

			$this->create_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Purge table of non-activated users
	 *
	 * @param	int
	 * @return	void
	 */
	function purge_na($expire_period = 172800)
	{
		$this->db->where('activated', 0);
		$this->db->where('UNIX_TIMESTAMP(created) <', time() - $expire_period);
		$this->db->delete($this->table_name);
	}

	/**
	 * Delete user record
	 *
	 * @param	int
	 * @return	bool
	 */
	function delete_user($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->delete($this->table_name);
		if ($this->db->affected_rows() > 0) {
			$this->delete_profile($user_id);
			return TRUE;
		}
		return FALSE;
	}

	/**
	 * Set new password key for user.
	 * This key can be used for authentication when resetting user's password.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function set_password_key($user_id, $new_pass_key)
	{
		$this->db->set('new_password_key', $new_pass_key);
		$this->db->set('new_password_requested', date('Y-m-d H:i:s'));
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Check if given password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	int
	 * @return	void
	 */
	function can_reset_password($user_id, $new_pass_key, $expire_period = 900)
	{
		$this->db->select('1', FALSE);
		$this->db->where('id', $user_id);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >', time() - $expire_period);

		$query = $this->db->get($this->table_name);
		return $query->num_rows() == 1;
	}

	/**
	 * Change user password if password key is valid and user is authenticated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	int
	 * @return	bool
	 */
	function reset_password($user_id, $new_pass, $new_pass_key, $expire_period = 900)
	{
		$this->db->set('password', $new_pass);
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('new_password_key', $new_pass_key);
		$this->db->where('UNIX_TIMESTAMP(new_password_requested) >=', time() - $expire_period);

		$this->db->update($this->table_name);

		return $this->db->affected_rows() > 0;
	}

	/**
	 * Change user password
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function change_password($user_id, $new_pass)
	{
		$this->db->set('password', $new_pass);
		$this->db->where('id', $user_id);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Set new email for user (may be activated or not).
	 * The new email cannot be used for login or notification before it is activated.
	 *
	 * @param	int
	 * @param	string
	 * @param	string
	 * @param	bool
	 * @return	bool
	 */
	function set_new_email($user_id, $new_email, $new_email_key, $activated)
	{
		$this->db->set($activated ? 'new_email' : 'email', $new_email);
		$this->db->set('new_email_key', $new_email_key);
		$this->db->where('id', $user_id);
		$this->db->where('activated', $activated ? 1 : 0);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Activate new email (replace old email with new one) if activation key is valid.
	 *
	 * @param	int
	 * @param	string
	 * @return	bool
	 */
	function activate_new_email($user_id, $new_email_key)
	{
		$this->db->set('email', 'new_email', FALSE);
		$this->db->set('new_email', NULL);
		$this->db->set('new_email_key', NULL);
		$this->db->where('id', $user_id);
		$this->db->where('new_email_key', $new_email_key);

		$this->db->update($this->table_name);
		return $this->db->affected_rows() > 0;
	}

	/**
	 * Update user login info, such as IP-address or login time, and
	 * clear previously generated (but not activated) passwords.
	 *
	 * @param	int
	 * @param	bool
	 * @param	bool
	 * @return	void
	 */
	function update_login_info($user_id, $record_ip, $record_time)
	{
		$this->db->set('new_password_key', NULL);
		$this->db->set('new_password_requested', NULL);

		if ($record_ip)		$this->db->set('last_ip', $this->input->ip_address());
		if ($record_time)	$this->db->set('last_login', date('Y-m-d H:i:s'));

		$this->db->where('id', $user_id);
		$this->db->update($this->table_name);
	}

	/**
	 * Ban user
	 *
	 * @param	int
	 * @param	string
	 * @return	void
	 */
	function ban_user($user_id, $reason = NULL)
	{
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned'		=> 1,
			'ban_reason'	=> $reason,
		));
	}

	/**
	 * Unban user
	 *
	 * @param	int
	 * @return	void
	 */
	function unban_user($user_id)
	{
		$this->db->where('id', $user_id);
		$this->db->update($this->table_name, array(
			'banned'		=> 0,
			'ban_reason'	=> NULL,
		));
	}

	function update_evernote_oauth_info($access_token_info)
	{
		$data = array(
				'evernote_user_id'				=> $access_token_info['edam_userId'],
				'evernote_access_token'			=> $this->eatagscrypt->fnEncrypt($access_token_info['oauth_token'], EVERNOTE_TOKEN_SALT),
				'evernote_note_store_url'		=> $access_token_info['edam_noteStoreUrl'],
				'evernote_web_api_url_prefix'	=> $access_token_info['edam_webApiUrlPrefix'],
				'evernote_token_expires'		=> (int)($access_token_info['edam_expires'] / 1000)
			);

		log_message('debug', $this->common->var_dump_object($data));

		$this->db->where('id', $this->ci->tank_auth->get_user_id());
		$result =
			$this->db->update($this->table_name, $data);

		if (!$result) $this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());
	}

	function mark_user_as_token_expired($evernote_user_id)
	{
		$data = array('evernote_token_has_expired' => 1);

		log_message('debug', $this->common->var_dump_object($data));

		$this->db->where('evernote_user_id', $evernote_user_id);
		$result = $this->db->update($this->table_name, $data);

		if (!$result) $this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());
	}
	function mark_user_as_has_tags_updated($evernote_user_id)
	{
		$data = array('user_has_tags_updated' => 1);

		log_message('debug', $this->common->var_dump_object($data));

		$this->db->where('evernote_user_id', $evernote_user_id);
		$result = $this->db->update($this->table_name, $data);

		if (!$result) $this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());
	}

	/**
	 * Create an empty profile for a new user
	 *
	 * @param	int
	 * @return	bool
	 */
	private function create_profile($user_id)
	{
		$this->db->set('user_id', $user_id);
		return $this->db->insert($this->profile_table_name);
	}

	/**
	 * Delete user profile
	 *
	 * @param	int
	 * @return	void
	 */
	private function delete_profile($user_id)
	{
		$this->db->where('user_id', $user_id);
		$this->db->delete($this->profile_table_name);
	}

	/**
	 * Get user Evernote Oauth Access Token by Id
	 *
	 * @param	int
	 * @return	String
	 */
	function get_user_evernote_access_token_by_evernote_user_id($user_id)
	{
		// Check if user is activated?
		// $this->db->where('activated', $activated ? 1 : 0);

		// TODO: Check evernote_token_expires

		$result =
			$this->db
			->select(array('evernote_access_token'))
			->from($this->table_name)
			->where('evernote_user_id', $user_id)
			->get();

		if ($result->num_rows() == 1)
			return $this->eatagscrypt->fnDecrypt($result->row()->evernote_access_token , EVERNOTE_TOKEN_SALT);
		if (!$result) $this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());

		return NULL;
	}

	function get_user_all_users_with_evernote_id_and_token()
	{

		$where = "evernote_user_id IS NOT NULL AND evernote_access_token IS NOT NULL AND evernote_access_token != ''";

		$this->db->select('evernote_user_id, evernote_access_token');
		$this->db->where($where);

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) {
			$result = $query->result();
			for ($i=0; $i < $query->num_rows(); $i++) {
				$result[$i]->evernote_access_token = $this->eatagscrypt->fnDecrypt($result[$i]->evernote_access_token, EVERNOTE_TOKEN_SALT);
			}
			return $result;
		}
		return NULL;
	}
	function get_user_all_user_with_evernote_account_active($check_has_tags_updated = FALSE)
	{
		$this->db->select('evernote_user_id, evernote_access_token');
		if ($check_has_tags_updated) {
			$where = "user_has_tags_updated = 0";
			$this->db->where($where);
		}
		$query = $this->db->get('users_active_evernote');
		if ($query->num_rows() > 0) {
			$result = $query->result();
			for ($i=0; $i < $query->num_rows(); $i++) {
				$result[$i]->evernote_access_token = $this->eatagscrypt->fnDecrypt($result[$i]->evernote_access_token, EVERNOTE_TOKEN_SALT);
			}
			return $result;
		}
		return NULL;
	}
	function delete_evernote_data($user_id)
	{
		$this->db->set('evernote_access_token', NULL);
		$this->db->set('evernote_user_id', NULL);
		$this->db->set('evernote_note_store_url', NULL);
		$this->db->set('evernote_web_api_url_prefix', NULL);
		$this->db->set('evernote_token_expires', NULL);
		$this->db->where('evernote_user_id', $user_id);
		$this->db->update($this->table_name);
	}

	function get_all_users_with_id_and_access_token()
	{
		$this->db->select('id, evernote_access_token');
		$this->db->where('id IS NOT NULL');

		$this->db->where('evernote_access_token IS NOT NULL');
		$this->db->where('evernote_access_token !=', '');

		$query = $this->db->get($this->table_name);
		if ($query->num_rows() > 0) {
			$result = $query->result();
			for ($i=0; $i < $query->num_rows(); $i++) {
				$result[$i]->evernote_access_token = $this->eatagscrypt->fnEncrypt($result[$i]->evernote_access_token, EVERNOTE_TOKEN_SALT);
			}
			return $result;
		}
		return NULL;
	}
	function set_all_users_encripted_token()
	{
		$evernote_access_token = array();
		$user_id               = array();
		$ids_and_tokens        = $this->get_all_users_with_id_and_access_token();
		$length                = count($ids_and_tokens);

		$this->db->trans_start();

		for ($i=0;$i < $length; $i++) {
			$evernote_access_token[$i] = $ids_and_tokens[$i]->evernote_access_token;
			$user_id[$i]               = $ids_and_tokens[$i]->id;

			$this->db->set('evernote_access_token', $evernote_access_token[$i]);
			$this->db->where('id', $user_id[$i]);
			$this->db->update($this->table_name);
		}

		$this->db->trans_complete();
	}
}

/* End of file users.php */
/* Location: ./application/models/auth/users.php */