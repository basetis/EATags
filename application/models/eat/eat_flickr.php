<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "libraries/phpFlickr.php";

class Eat_flickr extends MY_Model
{
    private $table_name = 'action_flickr';

    public $evernote_user_id          = 'evernote_user_id';
    public $flickr_oauth_token        = 'flickr_oauth_token';
    public $flickr_oauth_token_secret = 'flickr_oauth_token_secret';

    protected $user;
    protected $photo_description = 'Uploaded by <a href="https://eatags.com">EATags</a>';

    /*====================
    == ACTION FUNCTIONS ==
    ====================*/

    public function execute_action($user_id, $user_token, $original_note, $options)
    {
        log_message('debug', __METHOD__);

        $this->error_msg = '';

        $this->user = $this->get_flickr_user_by_evernote_user_id($user_id);
        if ($this->user == NULL) {
            $this->error_msg = __METHOD__ . " - User has no Flickr data configured";
        } else {
            $photos = array();
            if (isset($original_note->resources)) {
                $count    = 1;
                $sequence = '';
                $flickr   = new phpFlickr($this->config->item('flickr_key'), $this->config->item('flickr_secret'));
                $flickr->setToken($this->user->flickr_oauth_token);
                foreach ($original_note->resources as $resource)
                {
                    if (!$this->evernote->is_image_mime($resource)) continue;

                    $extension = $this->common->get_extension_from_mime($resource->mime);
                    if (isset($extension) && !empty($extension)) {
                        $filename = md5(uniqid(rand(), TRUE));
                        $filename = $filename . $extension;
                        $this->load->helper('file');
                        if (!write_file('./uploads/flickr/'. $filename, $resource->data->body)) {
                            $this->error_msg = "Fail to write $filename";
                        } else {
                            $response = $flickr->sync_upload(
                                './uploads/flickr/' . $filename,
                                $original_note->title . $sequence,
                                $this->photo_description);
                            if (!$response)  {
                                $this->error_msg = "The Flickr API returned the following error: #{$flickr->error_code} - {$flickr->error_msg}";
                            } else {
                                $count++;
                                $sequence = ' ' . $count;
                            }
                        }
                    }
                }
            }

            if ($this->error_msg) log_message('error', $this->error_msg);
        }

        $new_note = $this->_prepare_new_note($original_note->guid, $original_note->title);
        // Flickr Action don't affect to the note object, return receive note on result
        return $new_note;
    }
    /*======================
    == DATABASE FUNCTIONS ==
    =======================*/
    public function get_flickr_user_by_evernote_user_id($evernote_user_id)
    {
        $this->db->where($this->evernote_user_id, $evernote_user_id);

        $result = $this->db->get($this->table_name);
        if ($result->num_rows() == 1) return $result->row();
        return NULL;
    }
    public function save_config_data_at_db($data)
    {
        log_message('debug', __METHOD__);
        log_message('debug', $this->common->var_dump_object($data));

        if( $this->get_flickr_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            return $this->db->update($this->table_name, $data);
        } else {
            $data['evernote_user_id'] = $this->session->userdata('evernote_user_id');
            return $this->db->insert($this->table_name, $data);
        }

        return NULL;
    }
    private function _delete_user_data_from_db(){
        if( $this->get_flickr_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            return $this->db->delete($this->table_name);
        } else {
            return FALSE;
        }
        return NULL;
    }
    public function flickr_evernote_logout(){
        $this->_delete_user_data_from_db();
    }
    /*===================================
    == FEATURE CONFIGURATION FUNCTIONS ==
    ====================================*/

    /**
    * Gets user's flickr access token after flickr callaback
    *   expects get params
    *
    * @return array('flickr_user_name' => string, 'comes_from_callback' => boolean)
    *
    */
    public function account_config( $from_callback = FALSE)
    {
        log_message('debug', __METHOD__ );
        $response = array(
            'flickr_user_name'    => '',
            'comes_from_callback' => $from_callback,
        );

        if ($from_callback) {
            if ($from_callback == 'delete') {
                $response['delete_done']    = $this->_delete_user_data_from_db(); // DELETE USER flickr DATA FROM DB
            } else {
                $callback_data = $this->_manage_callback();
                $response['config_saved']   = ($callback_data) ? $this->save_config_data_at_db($callback_data) : FALSE;
            }
        } else {
            // CHECK IF IS IT A REDIRECT FORCED BY CALLBACK
            if ($this->session->userdata('callback')) {
                $this->session->unset_userdata('callback');
                $response['comes_from_callback'] = TRUE;
            }
            if ($this->session->userdata('config_saved')) {
               $response['config_saved'] = $this->session->userdata('config_saved');
               $this->session->unset_userdata('config_saved');
            }
        }

        $flickr_data = $this->get_flickr_user_by_evernote_user_id($this->session->userdata('evernote_user_id'));
        if ($flickr_data) {
            $response['flickr_username'] = $flickr_data->flickr_username;
        }

        return $response;
    }

    private function _manage_callback()
    {
        $flickr = new phpFlickr($this->config->item('flickr_key'), $this->config->item('flickr_secret'));
        $response = $flickr->auth_getToken($this->input->get('frob', TRUE));

        if (isset($response['token']) && isset($response['user'])) {
            $result = array(
                'evernote_user_id'   => $this->session->userdata('evernote_user_id'),
                'flickr_oauth_token' => $response['token'],
                'flickr_fullname'    => $response['user']['fullname'],
                'flickr_user_nsid'   => $response['user']['nsid'],
                'flickr_username'    => $response['user']['username']
            );
            return $result;
        }
        log_message('error', __METHOD__  . ' > Error getting access token');
        return NULL;

    }
}