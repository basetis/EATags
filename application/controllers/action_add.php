<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Action_add extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->model('eat/Eat_add', 'add');
    }
    public function get_notes_by_notebook($notebook_guid)
    {
        header('Content-Type: application/x-json; charset=utf-8');
        echo json_encode($this->add->get_notes_by_notebook($notebook_guid));
    }
    public function send_data()
    {
        log_message('debug', __METHOD__);

        $params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $data = array(
            'evernote_user_id'  => $this->session->userdata('evernote_user_id'),
            'notebook_guid'     => $params['id_notebooks'],
            'note_guid'         => $params['id_notes'],
            'type'              => $params['type']
        );

        $this->add->send_data_to_db($data);
    }
    public function delete_data()
    {
        log_message('debug', __METHOD__);

        $params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $data = array(
            'evernote_user_id'  => $this->session->userdata('evernote_user_id'),
            'notebook_guid'     => $params['id_notebooks'],
            'note_guid'         => $params['id_notes'],
            'type'              => $params['type']
        );

        $this->add->delete_data_from_db($data);
    }
    public function check_notes_on_db()
    {
        log_message('debug', __METHOD__);
        $result = $this->add->check_notes_on_db();
        return $result;
    }
    private function _validate_params($method, $line, $params)
    {
        if (!$params) {
            return FALSE;
        }
        if (!isset($params['id_notebooks']) OR $params['id_notebooks']=='') {
            return FALSE;
        }
        if (!isset($params['id_notes']) OR $params['id_notes']=='') {
            return FALSE;
        }
        if (!isset($params['type']) OR $params['type']=='') {
            return FALSE;
        }
        return TRUE;
    }

}