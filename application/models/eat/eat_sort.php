<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_sort extends MY_Model
{

    function __construct()
    {
        parent::__construct();
    }

    /*
    *   execute_action
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
        /* TODO: refactor this method to prevent html tag and content destroy */
        // set required params
        $access_token  = $eat_object['access_token'];
        $note_guid     = $eat_object['note_guid'];
        $options       = $tag_action['options'];
        $original_note = $eat_object['original_note'];
        // 1. set default response
        $error_place = 'Error on \models\eat\eat_sort.php execute_action';
        $response = array(
            'error_msg' => 'Unknown ' . $error_place,
            'result' => array(),
        );
        // 2. get current note content
        if( isset($current_note['content']) ){
            $current_note_content = $current_note['content'];
        } else {
            $current_note_content = $original_note->content;
        }
        // 3. clean note content from undesired enml tags
        $this->load->library('Enml','enml');
        $current_note_content = $this->enml->convert_enml_to_text($current_note_content);

        // 4. sort note content
        $content_lines = explode("\n", $current_note_content);
        sort($content_lines, SORT_STRING);
        if( $options['mode'] != 'ASC'){
            $content_lines=array_reverse($content_lines,TRUE);
        }

        // 6. construct ordered content
        $note_new_content = EN_XML_DEFINITION;
        $note_new_content .= EN_NOTE_START_TAG;
        foreach ($content_lines as $key => $value) {
            $note_new_content .= "<div>";
            $note_new_content .= $value;
            $note_new_content .= "</div>";
        }
        $note_new_content .= EN_NOTE_END_TAG;

        // 7 construct new step note
        $new_note = $current_note;
        $new_note['content'] = $note_new_content;

        $response['result']['note'] = $new_note;
        $response['error_msg'] = '';

        return $response;
    }


}