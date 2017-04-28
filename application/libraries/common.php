<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Common
{
    public function var_dump_object($object)
    {
        ob_start();
        var_dump($object);
        $dump = ob_get_clean();
        return $dump;
    }

    public function log_database_error($last_query, $error_message, $error_number, $data = null)
    {
        log_message('debug', __METHOD__);

        if ($data) {
            log_message('error', "data: " . $this->str_var_dump($data));
        }
        log_message('error', "query: " .$last_query);
        log_message('error', "DB Error: ".$error_message." (ERR_NUM: ".$error_number.")");
    }
    public function log_execute_action_params($eat_object, $tag_action, $current_note)
    {
        log_message('debug', "==============");
        log_message('debug', "= EAT_OBJECT =");
        log_message('debug', "==============");
        log_message('debug', $this->var_dump_object($eat_object));
        log_message('debug', "==============");
        log_message('debug', "= TAG_ACTION =");
        log_message('debug', "==============");
        log_message('debug', $this->var_dump_object($tag_action));
        log_message('debug', "================");
        log_message('debug', "= CURRENT_NOTE =");
        log_message('debug', "================");
        log_message('debug', $this->var_dump_object($current_note));
    }
    public function hex2bin($data) {
        $len = strlen($data);
        for($i=0;$i<$len;$i+=2) {
            $newdata .= pack("C",hexdec(substr($data,$i,2)));
        }
        return $newdata;
    }
    public function get_extension_from_mime($mime)
    {
        switch (strtolower($mime)) {
            case 'image/gif':
                return ".gif";
            case 'image/jpeg':
                return ".jpg";
            case 'image/png':
                return ".png";
            default:
                return "";
        }
    }
    function starts_with($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
    public function check_test_user_on_constants($user_id)
    {
        log_message('debug', __METHOD__);

        if (ENVIRONMENT == 'production') return FALSE;

        if (defined('DEVELOPER_EVERNOTE_SANDBOX_USER_ID')) {
            if ($user_id == DEVELOPER_EVERNOTE_SANDBOX_USER_ID) {
                return TRUE;
            }
        }
        return FALSE;
    }
    public function get_developer_working_copy($user_id)
    {
        log_message('debug', __METHOD__);

        if (ENVIRONMENT == 'production') return PRODUCTION_URL;

        if (defined('DEVELOPER_EVERNOTE_SANDBOX_USER_ID')) {
            if ($user_id == DEVELOPER_EVERNOTE_SANDBOX_USER_ID) {
                return DEVELOPMENT_URL;
            }
        }
        return PRODUCTION_URL;
    }
    public function get_google_api_client_id_by_working_copy($evernote_user_id)
    {
        switch ($evernote_user_id) {
            case DEVELOPER_EVERNOTE_SANDBOX_USER_ID:
                $result['google_client_id'] = DEVELOPER_GOOGLE_CLIENT_ID;
                $result['google_secret'] = DEVELOPER_GOOGLE_SECRET;
                break;
            default:
                $result['google_client_id'] = GOOGLE_CLIENT_ID;
                $result['google_secret'] = GOOGLE_SECRET;
                break;
        }
        return $result;
    }
}
/* End of file Common.php */