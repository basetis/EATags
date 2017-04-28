<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Rate_limit_queue extends CI_Model
{
    private $table_name      = 'rate_limit_queue';

    protected $evernote_user_id   = 'evernote_user_id';
    protected $evernote_note_guid = 'evernote_note_guid';
    protected $reason             = 'reason';
    protected $will_be_free_at    = 'will_be_free_at';

    public $time_to_be_free = NULL;

    function __construct()
    {
        parent::__construct();
    }
    public function insert_rate_limit_data($evernote_user_id, $evernote_note_guid, $reason, $seconds_to_be_free)
    {
        log_message('debug', __METHOD__);
        $data = array(
           $this->evernote_user_id   => $evernote_user_id,
           $this->evernote_note_guid => $evernote_note_guid,
           $this->reason             => $reason
        );
        $this->db->set($this->will_be_free_at, "DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $seconds_to_be_free second)", FALSE);

        $result = $this->db->insert($this->table_name, $data);
        if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        else {return $this->db->insert_id();}
    }

    function get_last_time_to_be_free($evernote_user_id)
    {
        $this->db->select('will_be_free_at');
        $this->db->where('evernote_user_id', $evernote_user_id);

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            $result = $query->last_row();

            return $result->will_be_free_at;
        }
        return NULL;
    }
    public function insert_time_to_be_free($evernote_user_id, $evernote_note_guid, $reason)
    {
        log_message('debug', __METHOD__);
        $data = array(
           $this->evernote_user_id   => $evernote_user_id,
           $this->evernote_note_guid => $evernote_note_guid,
           $this->reason             => $reason,
           $this->will_be_free_at    => $this->time_to_be_free
        );

        $result = $this->db->insert($this->table_name, $data);
        if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        else {return $this->db->insert_id();}
    }
}