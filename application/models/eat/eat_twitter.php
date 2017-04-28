<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_twitter extends MY_Model
{
    private $table_name                = 'action_twitter';
    public $evernote_user_id           = 'evernote_user_id';
    public $twitter_oauth_token        = 'twitter_oauth_token';
    public $twitter_oauth_token_secret = 'twitter_oauth_token_secret';
    public $twitter_user_id            = 'twitter_user_id';
    public $twitter_screen_name        = 'twitter_screen_name';

    protected $user;
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
        $this->user = $this->get_twitter_user_by_evernote_user_id($user_id);
        if ($this->user == NULL) {
            $this->error_msg = __METHOD__ . " - User has no Twiter data configured";
        } else {
            $current_note_title = $original_note->title;
            $current_note_title = (strlen($current_note_title) <= 140) ? $current_note_title : substr($current_note_title, 0, 140);

            $result = $this->post_tweet($current_note_title);

            if (!$result) $this->error_msg = "Tweet failed";

            if ($this->error_msg) log_message('error', $this->error_msg);
        }

        $new_note = $this->_prepare_new_note($original_note->guid, $original_note->title);
        // Twitter Action don't affect to the note object, return receive note on result
        return $new_note;
    }

    public function post_tweet($tweet_text)
    {
        log_message('debug', __METHOD__);

        $configs = array(
            'consumer_key'    => $this->config->item('twitter_consumer_key'),
            'consumer_secret' => $this->config->item('twitter_consumer_secret'),
            'user_token'      => $this->user->twitter_oauth_token,
            'user_secret'     => $this->user->twitter_oauth_token_secret
        );

        $this->load->library('tmhoauth',$configs);

        return $this->tmhoauth->request('POST', $this->tmhoauth->url('1.1/statuses/update'), array('status' => $tweet_text));
    }

    /*======================
    == DATABASE FUNCTIONS ==
    =======================*/
    public function create_twitter_user_for_evernote_user_id($evernote_user_id, $tw_data)
    {
        $data = array(
            $this->evernote_user_id           => $evernote_user_id,
            $this->twitter_oauth_token        => $tw_data['twitter_oauth_token'],
            $this->twitter_oauth_token_secret => $tw_data['twitter_oauth_token_secret'],
            $this->twitter_user_id            => $tw_data['twitter_user_id'],
            $this->twitter_screen_name        => $tw_data['twitter_screen_name']
        );

        $result = $this->db->insert($this->table_name, $data);

        if (!$result) $this->common->log_database_error($this->db->last_query(), $this->db->_error_message(), $this->db->_error_number(), $data);

        return $result;
    }
    public function get_twitter_user_by_evernote_user_id($evernote_user_id)
    {
        $this->db->where($this->evernote_user_id, $evernote_user_id);

        $result = $this->db->get($this->table_name);
        if ($result->num_rows() == 1) return $result->row();
        return NULL;
    }
    private function _save_config_data_at_db( $data )
    {
        // CHECK IF USER EXISTS AT WP CONFIG TABLE
        if( $this->get_twitter_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            // EXISTS => UPDATE
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            return $this->db->update($this->table_name, $data);
        } else {
            // NOT EXISTS => INSERT
            $data['evernote_user_id'] = $this->session->userdata('evernote_user_id');
            return $this->db->insert($this->table_name, $data);
        }

        return NULL;
    }

    private function _delete_user_data_from_db(){
        if( $this->get_twitter_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            return $this->db->delete($this->table_name);
        } else {
            return FALSE;
        }
        return NULL;
    }
    public function twitter_evernote_logout(){
        $this->_delete_user_data_from_db();
    }

    /*===================================
    == FEATURE CONFIGURATION FUNCTIONS ==
    ===================================*/

    /**
    * Gets user's twitter access token after twitter callaback
    *   expects get params
    *
    * @return ??
    *
    */
    public function account_config( $from_callback = FALSE)
    {
        log_message('debug', __METHOD__ );
        $response = array(
            'twitter_screen_name' => '',
            'comes_from_callback' => FALSE,
        );

        if( $from_callback ){
            // IF IT IS COMMING FROM TWITTER CALLBACK
            $response['comes_from_callback'] = TRUE;
            $callback_data = $this->_manage_callback();
            if( $callback_data ){
                $response['config_saved'] = $this->_save_config_data_at_db($callback_data);
            } else {
                $response['config_saved'] = FALSE;
            }
        } else if( $this->input->post('action_type') == 'delete'){
            // DELETE USER TWITTER DATA FROM DB
            $response['delete_done'] = $this->_delete_user_data_from_db();
        } else {
            // CHECK IF IS IT A REDIRECT FORCED BY CALLBACK
            if( $this->session->userdata('callback') ){
                $this->session->unset_userdata('callback');
                $response['comes_from_callback'] = TRUE;
            }
            if( $this->session->userdata('config_saved') ){
               $response['config_saved'] = $this->session->userdata('config_saved');
               $this->session->unset_userdata('config_saved');
            }
        }

        $twitter_data = $this->get_twitter_user_by_evernote_user_id($this->session->userdata('evernote_user_id'));
        if( $twitter_data ){
            // USER HAS TWITTER INFO AT DB
            $response['twitter_screen_name'] = $twitter_data->twitter_screen_name;
        }

        return $response;
    }

    private function _manage_callback()
    {
        $params = array(
            'key'    =>$this->config->item('twitter_consumer_key'),
            'secret' =>$this->config->item('twitter_consumer_secret')
        );

        $this->load->library('twitter_oauth', $params);
        $request = $this->twitter_oauth->get_access_token(false, $this->session->userdata('twitter_token_secret'));

        if (isset($request['oauth_token']) && isset($request['oauth_token_secret'])) {
            $response = array();
            $response['twitter_oauth_token']        = $request['oauth_token'];
            $response['twitter_oauth_token_secret'] = $request['oauth_token_secret'];
            $response['twitter_user_id']            = $request['user_id'];
            $response['twitter_screen_name']        = $request['screen_name'];
            return $response;
        }
        log_message('error', __METHOD__  . ' > Error getting access token');
        return NULL;
    }
}