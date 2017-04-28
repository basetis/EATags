<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_newsletter extends MY_Model
{
    private $table_name = 'action_user_newsletter_emails';
    protected $email    = 'email';
    protected $evernote_user_id = 'evernote_user_id';

    function __construct()
    {
        parent::__construct();
    }
    /*====================
    == ACTION FUNCTIONS ==
    ====================*/
    public function execute_action($eat_object, $tag_action, $current_note)
    {
        log_message('debug', __METHOD__);

        log_message('debug', "EAT OBJ");
        log_message('debug', $this->common->var_dump_object($eat_object));
        // log_message('debug', "TAG ACTION");
        // log_message('debug', $this->common->var_dump_object($tag_action));
        // log_message('debug', "CURRENT NOTE");
        // log_message('debug', $this->common->var_dump_object($current_note));

        $email_array = $this->get_user_newsletter_emails($eat_object['user_id']);

        foreach ($email_array as $email_item) {
            $emails[] = $email_item["email"];
        }

        if (isset($emails) && count($emails)) {
            // NOT IMPLEMENTED, EVERNOTE DON'T ALLOW TO USE HIS API TO SEND MAILS
        } else {
            $this->error_msg = __METHOD__ . " - User has no emails configured";
        }

        // Newsletter Action don't affect to the note object, return receive note on result
        $response = array(
            'error_msg' => $this->error_msg,
            'result'    => array('note' => $current_note),
        );

        return $response;
    }
    /*======================
    == DATABASE FUNCTIONS ==
    =======================*/
    function get_user_newsletter_emails($evernote_user_id)
    {
        return $this->db
            ->select($this->email)
            ->from($this->table_name)
            ->where($this->evernote_user_id, $evernote_user_id)
            ->get()->result_array();
    }

    function save_user_newsletter_emails($evernote_user_id, $email_array)
    {
        $this->delete_user_newsletter_emails($evernote_user_id);

        $this->db->insert_batch($this->table_name, $email_array);
    }

    function delete_user_newsletter_emails($evernote_user_id)
    {
        $this->db
            ->where($this->evernote_user_id, $evernote_user_id)
            ->delete($this->table_name);
    }

}