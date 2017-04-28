<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Statistics_model extends CI_Model
{

    private $table_name      = 'eat_webhook_notifications';

    protected $id                 = 'id';
    protected $evernote_user_id   = 'evernote_user_id';
    protected $evernote_note_guid = 'evernote_note_guid';
    protected $reason             = 'reason';
    protected $id_tag             = 'id_tag';
    protected $started            = 'started';
    protected $finished           = 'finished';
    protected $was_eaten          = 'was_eaten';

    function __construct()
    {
        parent::__construct();
    }
    public function insert_evernote_notification($evernote_user_id, $evernote_note_guid, $reason)
    {
        log_message('debug', __METHOD__);
        $data = array(
           $this->evernote_user_id   => $evernote_user_id,
           $this->evernote_note_guid => $evernote_note_guid,
           $this->reason             => $reason
        );

        $result = $this->db->insert($this->table_name, $data);
        if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        else {return $this->db->insert_id();}
    }

    public function add_id_tag($id_notification, $id_tag){
        log_message('debug', __METHOD__);

        $result = $this->db->where($this->id, $id_notification)->update($this->table_name, array($this->id_tag => $id_tag));

        if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
    }
    public function update_field($id_notification, $field_name, $field_value)
    {
        log_message('debug', __METHOD__);


        $result = $this->db->where($this->id, $id_notification)->update($this->table_name, array($field_name => $field_value));

        if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
    }
    public function track_latex_editor_opened($evernote_user_id)
    {
        log_message('debug', __METHOD__);

        $this->db->select(array('id', 'times_editor_was_opened'));
        $this->db->where($this->evernote_user_id, $evernote_user_id);
        $query = $this->db->get('latex_editor_analytics');
        if ($query->num_rows() == 0) {
            $data = array(
               $this->evernote_user_id   => $evernote_user_id,
               'times_editor_was_opened' => 1
            );

            $result = $this->db->insert('latex_editor_analytics', $data);
            if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        } else {

            $query_result = $query->result();

            $result = $this->db
                ->where($this->id, $query_result[0]->id)
                ->update('latex_editor_analytics', array('times_editor_was_opened' => $query_result[0]->times_editor_was_opened + 1));
            if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        }
    }
    public function track_latex_formula_edited_and_updated_to_evernote($evernote_user_id)
    {
        log_message('debug', __METHOD__);

        $this->db->select(array('id', 'times_formula_was_updated_to_evernote'));
        $this->db->where($this->evernote_user_id, $evernote_user_id);
        $query = $this->db->get('latex_editor_analytics');
        if ($query->num_rows() == 0) {
            $data = array(
               $this->evernote_user_id   => $evernote_user_id,
               'times_formula_was_updated_to_evernote' => 1
            );

            $result = $this->db->insert('latex_editor_analytics', $data);
            if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        } else {

            $query_result = $query->result();

            $result = $this->db
                ->where($this->id, $query_result[0]->id)
                ->update('latex_editor_analytics', array('times_formula_was_updated_to_evernote' => $query_result[0]->times_formula_was_updated_to_evernote + 1));
            if (!$result) {$this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number());}
        }
    }

}