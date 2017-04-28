<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class EATagsAdmin extends MY_Model
{
    public function create_specific_eatag_for_user($evernote_user_id, $evernote_access_token, $tag_name)
    {
        $this->load->library('evernote');
        $initialized = $this->evernote->init($evernote_access_token, $evernote_user_id);
        if (!$initialized) return false;

        // Get All User Tags
        $parent_guid = NULL;
        $user_active_tags = $this->evernote->get_all_user_tags($evernote_access_token);
        foreach ($user_active_tags as $key => $value) {
            if ($value->name == $this->config->item('parent_action_tag_name')) {
                $parent_guid = $value->guid;
                break;
            }
        }
        $result =
            $this->evernote->create_child_tags_with_name(
                array($tag_name),
                $evernote_access_token,
                $evernote_user_id,
                $parent_guid
            );

        return $result;
    }
    public function create_remaining_eatags_for_all_users()
    {
        $this->load->model('users');
        $rows = $this->users->get_user_all_user_with_evernote_account_active(TRUE);

        $number_users_to_be_updated = count($rows);
        log_message('error', "USERS TO BE UPDATED: " . $number_users_to_be_updated);
        if ($number_users_to_be_updated == 0) {
            log_message('error', "ALL SYSTEMS GREEN! Users has every EATags they need. Thank you All!");
            return true;
        }
        foreach ($rows as $row)
        {
            $result = $this->create_remaining_eatags_for_user($row->evernote_user_id, $row->evernote_access_token);
            if (!$result) {
                log_message('error',"[ERROR] - $row->evernote_user_id");
            } else {
                $this->users->mark_user_as_has_tags_updated($row->evernote_user_id);
            }
        }
    }
    public function create_remaining_eatags_for_user($evernote_user_id, $evernote_access_token)
    {
        $this->load->library('evernote');
        $initialized = $this->evernote->init($evernote_access_token, $evernote_user_id);
        if (!$initialized) return false;

        // Get All User Tags
        $user_active_tags = $this->evernote->get_all_user_tags($evernote_access_token);

        // Get EAT Tags that user does not have
        $user_remaining_tags = $this->get_remaining_eatags($user_active_tags);
        unset($user_active_tags);

        if (count($user_remaining_tags['tags']) == 0) {
            log_message('error', "[GOOD ] - No need to create remaining tags for user: $evernote_user_id");
            $this->load->model('tank_auth/users');
            $this->users->mark_user_as_has_tags_updated($evernote_user_id);
            return true;
        } else {
            log_message('error', "[GOOD ] - ". count($user_remaining_tags['tags']) ." tags pending to create for user: $evernote_user_id");
        }
        // Create Remaining EATTags
        $result =
            $this->evernote->create_child_tags_with_name(
                $user_remaining_tags['tags'],
                $evernote_access_token,
                $evernote_user_id,
                $user_remaining_tags['parent']
            );

        return $result;
    }
    public function get_remaining_eatags($user_tags)
    {
        $this->load->model('eat/Tags', 'tags');
        $valid_tags  = $this->tags->get_active_tags_name();
        $valid_names = array();
        foreach ($valid_tags as $key => $value) {
            array_push($valid_names, strtolower($value["name"]));
        }

        $filtered_tags =
            array(
                "tags" => array(),
                "parent" => NULL
            );
        $parent_guid = NULL;
        $filter = array();
        $user_tags_names = array();
        foreach ($user_tags as $key => $value)
        {
            if (strtolower($value->name) == strtolower($this->config->item('parent_action_tag_name'))) {
                $filtered_tags['parent'] = $value->guid;
            }
            if ($this->startsWith($value->name, "eat.")) {
                $user_tags_names[] = strtolower($value->name);
            }
        }

        $filtered_tags['tags'] = array_diff($valid_names, $user_tags_names);

        return $filtered_tags;
    }
    private function startsWith($haystack, $needle)
    {
         $length = strlen($needle);
         return (substr($haystack, 0, $length) === $needle);
    }

    private function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
