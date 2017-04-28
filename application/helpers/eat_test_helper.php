<?php

use EDAM\Types\Note;
use EDAM\Types\Resource;
use EDAM\Types\Data;

function get_empty_eat_object()
{
    $eat_object = array(
        "error_msg"       => "",
        "user_id"         => "",
        "note_guid"       => "",
        "access_token"    => "",
        "original_note"   => new Note(),
        "tag_list"        => array(),
        "tags_to_execute" => array(),
        "not_eat_tags"    => array(),
        "tags_executed"   => array()
    );
    return $eat_object;
}

function get_tag_action_by_tag_name($tag_name)
{
    $CI = get_instance();
    if ($CI->config->item('child_action_tags')) {
        foreach ($CI->config->item('child_action_tags') as $eat_tag) {
            if ($eat_tag['name'] == $tag_name)
                return $eat_tag;
        }
    }


    return NULL;
}
function get_simple_note_content($html_content = ' ')
{
    $note_content = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
    $note_content .= '<!DOCTYPE en-note SYSTEM "http://xml.evernote.com/pub/enml2.dtd">';
    $note_content .= '<en-note style="word-wrap: break-word; -webkit-nbsp-mode: space; -webkit-line-break: after-white-space;">';
    $note_content .= $html_content;
    $note_content .= '</en-note>';
    return $note_content;
}



?>