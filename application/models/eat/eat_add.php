<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_add extends MY_Model
{
    private $table_name      = 'action_add';

    function __construct()
    {
        parent::__construct();
    }
    /*====================
    == ACTION FUNCTIONS ==
    ====================*/
    public function execute_action($user_id, $access_token, $original_note, $options)
    {
        log_message('debug', __METHOD__);

        $this->load->library('language');
        $language = $this->language->get_lang_from_db($user_id);
        $this->lang->load('account', $language);

        $opt = $options;
        $add_resources = array();

        // 1. copy the original note as a new one
        $new_note             = $this->_prepare_new_note($original_note->guid, $original_note->title);

        // 2. get current note content
        $current_note_content = $original_note->content;

        // 3. clean note content from Evernote headers
        $current_note_content = $this->enml->remove_enml_note_tags($current_note_content);

        //4. get header or footer note from Evernote
        $data = array();
        $data['evernote_user_id'] = $user_id;
        if ($opt[0]['value'] == 'HEADER') {
            $data['type'] = 'header';
        } else if ($opt[0]['value'] == 'FOOTER') {
            $data['type'] = 'footer';
        }

        $note_guid = $this->_get_guid_note_from_db($data);

        $header_start   = '<div style="height:auto;background-color:#69B300;color:white;text-align:center;padding:1px 1px 10px 1px;">';
        $header_img     = '<img style="float:right;position:relative;" src="'.base_url('assets/images/tag.png').'"></img>';
        if ($opt[0]['value'] == 'HEADER') {
            $header_title   = '<h1>'.$this->lang->line('account_add_no_header').'</h1>';
        } else if ($opt[0]['value'] == 'FOOTER') {
            $header_title   = '<h1>'.$this->lang->line('account_add_no_footer').'</h1>';
        } else {
            $header_title   = '<h2>'.$this->lang->line('account_add_no_footer_no_header').'</h2>';
        }

        $header_text    = '<a style="background-color:white;padding:.5em;" href="'.base_url('account/features/add').'" target="_blank">'.$this->lang->line('account_add_go_config').'</a>';
        $header_end     = '</div>';

        if ($note_guid == null) {

            log_message('debug', 'no hay footer/header seleccionado en config-db');

            $add_header  = $header_start . $header_img . $header_title . $header_text . $header_end;

            $current_note_content = $add_header . $current_note_content;
        } else {

            $note_guid = $note_guid[0]->note_guid;

            $options = '';
            $options = array('with_resources_data' => true);

            $add_note = $this->evernote->get_note_by_id($access_token, $note_guid, $options);


            if (isset($add_note['note']->resources)) {
                $add_resources = $add_note['note']->resources;
            }

            $add_note = $add_note['note']->content;

            $add_note = $this->enml->remove_enml_note_tags($add_note);

            // 5. preppend header and/or append footer to note
            switch ($opt[0]['value']) {
                case 'HEADER':
                    $header = $add_note;
                    $current_note_content = $header . $current_note_content;
                    break;
                case 'FOOTER':
                    $footer = $add_note;
                    $current_note_content = $current_note_content . $footer;
                    break;
                default:
                    $guids = $this->_get_header_and_footer_guids_from_db($data);
                    $total_guids = count($guids);
                    for ($i=0; $i < $total_guids; $i++) {
                        $note = $this->evernote->get_note_by_id($access_token, $guids[$i]->note_guid, $options);
                        $note_resources = $note['note']->resources;
                        $note = $note['note']->content;
                        $note = $this->enml->remove_enml_note_tags($note);
                        if ($guids[$i]->type == 'header') {
                            $header_resources = $note_resources;
                            $header = $note;
                        } else if ($guids[$i]->type == 'footer') {
                            $footer_resources = $note_resources;
                            $footer = $note;
                        }
                    }
                    if (isset($header_resources) && isset($footer_resources)){
                        $add_resources = array_merge ( $header_resources, $footer_resources );
                    } else if (isset($header_resources) && !isset($footer_resources)) {
                        $add_resources = $header_resources;
                    } else if ( !isset($header_resources) && isset($footer_resources)) {
                        $add_resources = $footer_resources;
                    }
                    if (isset($header) && isset($footer)) {
                        $current_note_content = $header . $current_note_content . $footer;
                    } else if (isset($header) && !isset($footer)) {
                        $header_title   = '<h1>'.$this->lang->line('account_add_no_footer').'</h1>';
                        $add_header  = $header_start . $header_img . $header_title . $header_text . $header_end;
                        $current_note_content = $add_header . $header . $current_note_content;
                    } else if ( !isset($header) && isset($footer)) {
                        $header_title   = '<h1>'.$this->lang->line('account_add_no_header').'</h1>';
                        $add_header  = $header_start . $header_img . $header_title . $header_text . $header_end;
                        $current_note_content = $add_header . $current_note_content . $footer;
                    }
                    break;
            }
        }

        // 6. prepare note for update
        $new_note             = $this->_prepare_new_note($original_note->guid, $original_note->title);
        $new_note->content    = $this->enml->add_enml_note_tags($current_note_content);

        if (isset($original_note->resources) && count($original_note->resources)){
            $new_note->resources = array_merge ( $original_note->resources, $add_resources );
        } else {
            $new_note->resources = $add_resources;
        }

        return $new_note;

    }
    public function account_config()
    {
        log_message('debug', __METHOD__ );
        $access_token = $this->session->userdata('evernote_access_token');

        $evernote_user_id = $this->session->userdata('evernote_user_id');

        $this->evernote->init($access_token, $evernote_user_id, 'aaa-bbb-ccccc', 'notebooks');
        $user_notebooks = $this->evernote->get_all_user_notebooks($access_token);
        $notebooks = array();
        $total_notebooks = count($user_notebooks);
        for ($i=0; $i < $total_notebooks; $i++) {
            $notebooks[$user_notebooks[$i]->guid] = $user_notebooks[$i]->name;
        };
        $selected_notes = $this->_check_notes_on_db($evernote_user_id);
        $selected_header = '';
        $selected_footer = '';
        $header_notes = '';
        $footer_notes = '';
        if ($selected_notes != null) {
            foreach ($selected_notes as $key => $value) {

                if ($value->type == 'header') {
                    $selected_header = $value;
                    if ($selected_header != '') {
                        $header_notes = $this->get_notes_by_notebook($selected_header->notebook_guid);
                    }
                } else {
                    $selected_footer = $value;
                    if ($selected_footer != '') {
                        $footer_notes = $this->get_notes_by_notebook($selected_footer->notebook_guid);
                    }
                }
            }
        }
        $data = array(
            'notebooks'         => $notebooks, // array de todos los notebooks
            'selected_header'   => $selected_header,
            'selected_footer'   => $selected_footer,
            'header_notes'      => $header_notes,
            'footer_notes'      => $footer_notes,
            );
        return $data;
    }
    public function get_notes_by_notebook($notebook_guid)
    {
        log_message('debug', __METHOD__ );
        $access_token = $this->session->userdata('evernote_access_token');

        $evernote_user_id = $this->session->userdata('evernote_user_id');
        $this->evernote->init($access_token, $evernote_user_id, 'aaa-bbb-ccccc', 'notebooks');
        $notebook_notes = $this->evernote->find_notes_metadata_by_notebook($access_token,$notebook_guid);

        $notes = array();
        $total_notes = count($notebook_notes->notes);

        for ($i=0; $i < $total_notes; $i++) {
            $notes[$notebook_notes->notes[$i]->guid] = $notebook_notes->notes[$i]->title;
        }
        return $notes;
    }
    public function send_data_to_db($data)
    {
        // CHECK IF USER HAS A HEADER OR FOOTER ON DB
        $check_note = array (
            'evernote_user_id' => $data['evernote_user_id'],
            'type' => $data['type']
            );

        $this->db->select('id');
        $this->db->where($check_note);
        $query = $this->db->get($this->table_name);

        // IF NOT THEN INSERT DATA
        if ($query->num_rows == 0) {
            $response = $this->db->insert($this->table_name, $data);
            return $response;
        }
        // IF YES THEN UPDATE ROWS
        $result = $query->result();
        $this->db->where('id', $result[0]->id);
        $this->db->set('notebook_guid', $data['notebook_guid']);
        $this->db->set('note_guid', $data['note_guid']);
        $response = $this->db->update($this->table_name);
        return $response;
    }
    public function delete_data_from_db($data)
    {
        // CHECK IF USER HAS A HEADER OR FOOTER ON DB
        $check_note = array (
            'evernote_user_id' => $data['evernote_user_id'],
            'type' => $data['type']
            );

        $this->db->select('id');
        $this->db->where($check_note);
        $query = $this->db->get($this->table_name);

        // IF NOT THERE'S NOTHING TO DELETE
        if ($query->num_rows == 0) {
            return false;
        }
        // IF YES THEN 'DELETE' DATA
        $result = $query->result();
        $this->db->where('id', $result[0]->id);
        $this->db->set('notebook_guid', NULL);
        $this->db->set('note_guid', NULL);
        $query = $this->db->update($this->table_name);
        return $query;
    }
    private function _get_guid_note_from_db($data)
    {
        log_message('debug', __METHOD__);
        $this->db->select('note_guid');
        $query = $this->db->get_where($this->table_name, $data);
        if ($query->num_rows == 0) {
            $notes_guid = null;
        } else {
            $notes_guid = $query->result();
            foreach ($notes_guid as $key => $value) {
                if ( $value->note_guid == NULL ) {
                    unset($notes_guid[$key]);
                }
            }
            $notes_guid = array_values($notes_guid);
        }
        return $notes_guid;
    }
    private function _get_header_and_footer_guids_from_db($data)
    {
        log_message('debug', __METHOD__);
        $this->db->select('note_guid');
        $this->db->select('type');
        $query = $this->db->get_where($this->table_name, $data);
        if ($query->num_rows == 0) {
            return false;
        } else {
            $notes_guid = $query->result();
            foreach ($notes_guid as $key => $value) {
                if ( $value->note_guid == NULL ) {
                    unset($notes_guid[$key]);
                }
            }
            $notes_guid = array_values($notes_guid);
        }
        return $notes_guid;
    }
    private function _check_notes_on_db ($evernote_user_id)
    {
        log_message('debug', __METHOD__);
        $check_note = array (
            'evernote_user_id' => $evernote_user_id
            );

        $query = $this->db->get_where($this->table_name, $check_note);

        if ($query->num_rows == 0) {
            $notes = null;
        } else {
            $notes = $query->result();
            foreach ($notes as $key => $value) {
                if ( $value->note_guid == NULL ) {
                    unset($notes[$key]);
                }
            }
        }
        return $notes;
    }
}