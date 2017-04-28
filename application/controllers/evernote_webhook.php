<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Evernote_webhook extends CI_Controller
{
    protected $main_url = "https://eatags.com/evernote_webhook/eat_that/";

    function __construct()
    {
        parent::__construct();
    }
    public function webhook_emulator_receiver()
    {
        $params = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params($method, $line, $params)) return FALSE;

        $url = $this->_get_url_for_testing_user($params);
        if (is_null($url)) $url = $this->main_url;

        $this->_curl_post_async($url, $params);
    }

    /**
    * Responds to: http[s]://[your base URL][? |&]userId=[evernoteUserId]&guid=[noteGuid]&reason=[create | update]
    */
    public function index()
    {
        header("HTTP/1.1 200 OK");
        try {
            $params = $this->input->get(NULL, TRUE);

            // Check params
            if (isset($params['test'])) return;

            if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;
            if (!isset($params['reason']))  $params['reason'] = NULL;

            // Check if user that invoked the webhook is a TEST USER to redirected to its working copy
            $url = $this->_get_url_for_testing_user($params);
            if (is_null($url)) $url = $this->main_url;

            // ASYNC CURL to begin the process
            $this->_curl_post_async($url, $params);

        } catch (Exception $e) {
            $this->eatlog->new_msg("Global Exception receiving webhook", __METHOD__, __LINE__, LOG_LEVEL_ERROR, LOG_TYPE_UNKNOWN);
            $this->eatlog->new_msg($e->getTraceAsString(), __METHOD__, __LINE__, LOG_LEVEL_ERROR, LOG_TYPE_UNKNOWN);
        }
    }

    public function eat_that()
    {
        header("HTTP/1.1 200 OK");

        log_message('debug', " == WELCOME TO: " . $this->input->server("SERVER_NAME") . " == ");

        $params    = $this->input->post(NULL,TRUE);

        if (!$this->_validate_params(__METHOD__, __LINE__, $params)) return;

        $user_id   = $params['userId'];
        $note_guid = $params['guid'];
        $reason    = $params['reason'];

        $this->load->model('rate_limit_queue');
        $this->rate_limit_queue->time_to_be_free = $this->rate_limit_queue->get_last_time_to_be_free($user_id);

        if ($this->rate_limit_queue->time_to_be_free != NULL) {
            $this->rate_limit_queue->insert_time_to_be_free($user_id, $note_guid, $reason);
            log_message('error', "== user $user_id has reached the rate limit and will be free at " . $this->rate_limit_queue->time_to_be_free . " ==");
            return;
        }

        $this->load->model('Statistics_model', 'eat_stats');
        $id_notification = $this->eat_stats->insert_evernote_notification($user_id, $note_guid, $reason);

        // inicializar librería (internamente obtiene token y nota)
        $this->load->library('action_note', array('user_id' => $user_id, 'note_guid' => $note_guid, 'reason' => $reason));
        $this->_check_error($this->action_note->get_error_message());

        // obtener array de tags
        $tags = $this->action_note->get_tags();
        $this->_check_error($this->action_note->get_error_message());

        // del objeto tag sólo nos interesan los keynames
        $tag_keynames = array();
        foreach ($tags as $tag){
            $tag_keynames[] = $tag->name;
        }

        if (count($tag_keynames) == 0){
            $this->_stop_eating(__METHOD__ . ' > Note has no tags. user_id [' . $user_id .'] and note_guid [' . $note_guid . ']');
            return;
        }

        // de la lista de tags, obtener la de mayor prioridad a ser ejecutada
        $this->load->model('eat/Tags');
        $tag = $this->Tags->get_highest_priority_tag_in_tag_list($tag_keynames, $user_id);

        // Get the Guid of the TAG that will be executed
        foreach ($tags as $single_tag){
            if ($single_tag->name == $tag['name']) {
                $tag['guid'] = $single_tag->guid;
                break;
            }
        }

        $this->eat_stats->add_id_tag($id_notification, $tag['id_tag']);

        $this->_log_remaining_tags($tag_keynames, $user_id); // Some log for production

        if ($tag == -1)
        {
            $this->_stop_eating(__METHOD__ . ' > No tags to execute found for user_id [' . $user_id .'] and note_guid [' . $note_guid . ']');
            return;
        }

        // buscar los resources (imágenes, etc), si el eatag lo requiere
        if ($tag['require_resources']) {
            $this->action_note->request_note_resources();
            if ($this->_check_error($this->action_note->get_error_message(), false)){ $this->action_note->update_note('fail'); }
        } else {
            log_message('debug', __METHOD__ . ' > Resources not required');
        }

        $this->_log_current_executing_tag($tag, $user_id); // Some log for production

        $this->eat_stats->update_field($id_notification, 'started', date('Y-m-d H:i:s'));

        // ejecutar la acción
        $this->action_note->execute_action_by_tag($tag);

        $this->eat_stats->update_field($id_notification, 'finished', date('Y-m-d H:i:s'));

        if ($this->_check_error($this->action_note->get_error_message(), false)) {
            log_message('debug', "Tag has failed!!");
            $this->eat_stats->update_field($id_notification, 'was_eaten', 0);
            $this->action_note->update_note('fail');
        } else {
            // actualizar la nota
            $this->action_note->update_note();
            $this->_check_error($this->action_note->get_error_message());
        }
    }
    private function _log_remaining_tags($tag_keynames, $user_id)
    {
        $log_msg = "== Remaining Tags for User: $user_id ==";
        log_message('error', str_pad("", strlen($log_msg), "="));
        log_message('error', $log_msg);
        foreach ($tag_keynames as $tag_name) {
            log_message('error', "== $tag_name");
        }
        log_message('error', str_pad("", strlen($log_msg), "="));
    }
    private function _log_current_executing_tag($tag, $user_id)
    {
        $log_msg = "== User: $user_id is executing: " . $tag['name'] . " ==";
        log_message('error', str_pad("", strlen($log_msg), "="));
        log_message('error', $log_msg);
        log_message('error', str_pad("", strlen($log_msg), "="));
    }
    private function _get_url_for_testing_user($params)
    {
        $this->load->model('tank_auth/users');
        $user = $this->users->get_user_by_evernote_user_id($params['userId']);

        if (is_null($user)) return NULL;
        if (!$user->is_test_user) return NULL;
        return $user->test_url;
    }

    private function _check_error($error_msg, $kill = true)
    {
        log_message('debug', __METHOD__);
        if ($error_msg) {
            $this->_stop_eating($error_msg, $kill);
            return 1;
        } else {
            return 0;
        }
    }
    private function _stop_eating($error_msg, $kill = true)
    {
        log_message('error', $error_msg);
        if ($kill) { die(); }
    }
    private function _validate_params($method, $line, $params)
    {
        if (!$params) {
            return FALSE;
        }
        if (!isset($params['userId'])) {
            return FALSE;
        }
        if (!isset($params['guid'])) {
            return FALSE;
        }
        return TRUE;
    }
    private function _curl_post_async($url, $params)
    {
        log_message('debug', __METHOD__);
        log_message('debug', $url);
        foreach ($params as $key => &$val) {
          if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        switch ($parts['scheme']) {
            case 'https':
                $scheme = 'ssl://';
                $port = 443;
                break;
            case 'http':
            default:
                $scheme = '';
                $port = 80;
        }

        $fp = @fsockopen($scheme . $parts['host'], $port, $errno, $errstr, 30);

        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }
}
