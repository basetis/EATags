<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Language{
	protected $ci;
	private $table_name = 'users';

	function __construct()
    {
        $this->ci =& get_instance();
    }

	public function send_lang_to_db($lang)
	{
		$this->ci->db->where('id', $this->ci->session->userdata('user_id'));
		$this->ci->db->set('language', $lang);
		$query = $this->ci->db->update($this->table_name);
		return $query;
	}
	public function get_lang_from_db($evernote_user_id = '')
	{
		$user_id = ($evernote_user_id) ? $evernote_user_id : $this->ci->session->userdata('user_id');
		$db_id = ($evernote_user_id) ? 'evernote_user_id' : 'id';
		$this->ci->db->select('language');
        $this->ci->db->where($db_id, $user_id);
        $query = $this->ci->db->get($this->table_name);
        if ($query->num_rows == 0) {
            return null;
        }
        $result = $query->result();
        $lang = $result[0]->language;
        return $lang;
	}
}


/* End of file Language.php */