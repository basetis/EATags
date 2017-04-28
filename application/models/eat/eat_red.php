<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_red extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    /*
    *   sort_note
    *       @params
    *           eat_object      object      REQUIRED
    *           tag_action      object      REQUIRED
    *           current_note    array
    *       @returns
    *           error_msg       string      empty string on success
    *           result          object
    *               note        array
    *
    */
    public function execute_action($eat_object, $tag_action, $current_note)
    {
        // set required params
        $access_token  = $eat_object['access_token'];
        $note_guid     = $eat_object['note_guid'];
        $options       = $tag_action['options'];
        $original_note = $eat_object['original_note'];
        // 1. set default response
        $error_place = 'Error on \models\eat\eat_red.php execute_action';
        $response = array(
            'error_msg' => 'Unknown ' . $error_place,
            'result' => array( ),
        );
        // 2. get current note content
        if( isset($current_note['content']) ){
            $current_note_content = $current_note['content'];
        } else {
            $current_note_content = $original_note->content;
        }

        // 3. clean note content from Evernote headers
        $this->load->library('Enml','enml');
        $current_note_content = $this->enml->remove_enml_note_tags($current_note_content);

        // 4. construct new content
        $note_new_content = EN_XML_DEFINITION;
        $note_new_content .= EN_NOTE_START_TAG;
        $note_new_content .= '<div style="background-color:red;color:white;">';
        $note_new_content .= $current_note_content;
        $note_new_content .= '</div>';
        $note_new_content .= EN_NOTE_END_TAG;

        // 5 construct new step note
        $new_note = $current_note;
        $new_note['content'] = $note_new_content;

        $response['result']['note'] = $new_note;
        $response['error_msg'] = '';

        return $response;
    }


}