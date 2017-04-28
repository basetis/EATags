<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_notebook_toc extends MY_Model
{
    protected $list_of_links = array();
    protected $toc_key = 'notebook_toc';

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

        // 1. Get Notebook GUID
        $notebook_guid        = $original_note->notebookGuid;

        // 2. Get all notes within the same notebook
        if ($options[0]['key'] == 'mode' && $options[0]['value'] == 'NOTEBOOK') {
            $notes_list = $this->evernote->find_notes_metadata_by_notebook($access_token, $notebook_guid);
        } else {
            log_message('debug', "TAG GUIDS");
            log_message('debug', print_r($original_note->tagGuids,true));
            $tag_guids = array();
            if (!is_null($this->tag)) {
                foreach ($original_note->tagGuids as $guid) {
                    if ($guid != $this->tag['guid']) {
                        $tag_guids[] = $guid;
                    }
                }
            } else {
                $tag_guids = $original_note->tagGuids;
            }
            $notes_list  = $this->evernote->find_notes_metadata_by_tags($access_token, $tag_guids);
            log_message('debug', "RESULT NOTES");
            log_message('debug', print_r($notes_list,true));
        }


        if ($notes_list){
            $toc_str              = '';
            if ( count($notes_list->notes) )
            {
                // 3. Get User info
                $user             = $this->evernote->get_user($access_token);

                // 4. Create unordered list with titles and links to notes
                $toc_str          = $this->_get_notebook_toc_str($notes_list->notes, $user, $original_note->guid);
            }

            // 5. Delete previous toc
            $current_note_content = $this->enml->remove_enml_note_tags($original_note->content);
            $current_note_content = $this->evernote->remove_previous_table_of_contents($current_note_content, $this->toc_key);

            // 6. preppend new toc
            $current_note_content = $toc_str . $current_note_content;

            // 7. prepare note for update
            $new_note             = $this->_prepare_new_note($original_note->guid, $original_note->title);
            $new_note->content    = $this->enml->add_enml_note_tags($current_note_content);

            return $new_note;

        } else {
            $this->error_msg = 'ERROR getting notebook notes metadata';
            return false;
        }
    }

    private function _get_notebook_toc_str($notes, $user, $self_note_guid)
    {
        $result = '';

        if (isset($notes)){

            foreach($notes as $note){

                if ($note->guid != $self_note_guid) {
                    $result .= '<li><a href="evernote:///view/'.$user->id.'/'.$user->shardId.'/'.$note->guid.'/'.$note->guid.'/">'.htmlspecialchars($note->title).'</a></li>';
                }
            }

            if ($result != ''){
                $result = '<a name="ToC_start_'.$this->toc_key.'"></a><div><strong><u>Table of Contents</u></strong></div><ul>'.$result.'</ul><hr/><a name="ToC_end_'.$this->toc_key.'"></a>';
            }
        }

        return $result;
    }
}