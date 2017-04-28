<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH . "libraries/IXR_Library.php";

class Eat_wordpress extends MY_Model
{
    private $table_name      = 'action_wordpress';
    public $evernote_user_id = 'evernote_user_id';
    public $wp_username      = 'wp_username';
    public $wp_pass          = 'wp_pass';
    public $wp_blog_url      = 'wp_blog_url';

    private $table_note_post_link = 'action_wordpress_note_post_link';
    public $blog_id               = 'blog_id';
    public $note_guid             = 'note_guid';
    public $post_id               = 'post_id';

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

        $this->user = $this->get_wp_user_by_evernote_user_id($user_id);
        if ($this->user == NULL) {
            $this->error_msg = __METHOD__ . " - User has no Wordpress data configured";
        } else {

            $this->user->wp_blog_url = $this->_get_xmlrpc_url($this->user->wp_blog_url);
            log_message('debug', 'Blog: ' . $this->user->wp_blog_url);

            $current_note_content = $original_note->content;

            $this->error_msg = '';
            $note_content;
            if (isset($original_note->resources)) {
                $resources_list = $this->evernote->get_resources_data($original_note->resources);
                if (isset($resources_list)) {
                    $media_tags = $this->_get_media_tags_from_content($current_note_content, $resources_list);
                } else {
                    $this->error_msg = __METHOD__ . ' > Resources list is not set';
                }
                if (isset($media_tags)) {
                    $base64_imgs = $this->_get_base64_imgs($resources_list, $media_tags);
                    $base64_imgs = $this->xml_rpc_upload_images_to_wordpress($base64_imgs);
                    $note_content = $this->_replace_media_tags_to_img_links($current_note_content, $media_tags, $base64_imgs);
                } else {
                    $this->error_msg = __METHOD__ . ' > Media tags are not set';
                }
            } else {
                $note_content = $current_note_content;
            }

            if (!$this->error_msg) {
                $this->load->library('Enml','enml');
                $note_content = $this->enml->remove_enml_note_tags($note_content);
                if (!$note_content) $this->error_msg = __METHOD__ . " - Error extracting evernote XML headers";
            }

            $categories = "";
            $is_post    = ($options[0]['key'] == 'mode' && $options[0]['value'] == 'POST');

            if (!$this->error_msg) {
                $result = $this->xml_rpc_to_wordpress(
                            $original_note->guid,
                            $original_note->title,
                            $note_content,
                            $categories,
                            $is_post);
            } else {
                log_message('error', " == WORDPRESS FAIL - Note content: == ");
                if (isset($current_note_content)) log_message('error', $current_note_content);
                else log_message('error', "No content found");
            }
        }

        if ($this->error_msg) log_message('error', $this->error_msg);

        $new_note = $this->_prepare_new_note($original_note->guid, $original_note->title);
        // Wordpress Action don't affect to the note object, return receive note on result
        return $new_note;
    }

    private function _get_xmlrpc_url($url)
    {
        if ((! $this->common->starts_with($url, "http://")) &&
            (! $this->common->starts_with($url, "https://")))
        {
            $url = "http://" . $url;
        }

        $url .= (substr($url, -1) == "/") ? "xmlrpc.php" : "/xmlrpc.php";
        return $url;
    }
    private function _get_base64_imgs($resources_list, $media_tags)
    {
        log_message('debug', __METHOD__);
        $base64_imgs = array();

        foreach ($media_tags['valids'] as $media_tag) {
            foreach ($resources_list as $res_item) {
                if (strpos($media_tag, $res_item["hex_hash"])) {
                    $image = array(
                        "base64"   => new IXR_Base64($res_item['body']),
                        "mime"     => $res_item['mime'],
                        "hex_hash" => $res_item["hex_hash"]
                    );
                    $base64_imgs[] = $image;
                    break;
                }
            }
        }

        return $base64_imgs;
    }
    private function _replace_media_tags_to_img_links($note_content, $media_tags, $base64_imgs)
    {
        log_message('debug', __METHOD__);

        foreach ($media_tags['valids'] as $media_tag) {
            $img_tag_str = "";
            foreach ($base64_imgs as $image) {
                if (strpos($media_tag, $image["hex_hash"])) {
                    $img_tag_str = '<img src="' . $image["url"] . '" />';
                    break;
                }
            }
            $note_content = str_replace($media_tag, $img_tag_str, $note_content);
        }
        foreach ($media_tags['invalids'] as $media_tag) {
            $note_content = str_replace($media_tag, "", $note_content);
        }
        return $note_content;
    }
    private function _get_media_tags_from_content($note_content, $resources_list)
    {
        log_message('debug', __METHOD__);

        $media_tags = array('valids'   => array(), 'invalids' => array());

        try {
            foreach ($resources_list as $resource) {

                $note_content = substr($note_content, strpos($note_content, "<en-media"));
                $end_media    = -1;
                $end_media_1  = strpos($note_content, '</en-media>');
                $end_media_2  = strpos($note_content, '/>');

                if ($end_media_1 && $end_media_2)
                {
                    // Found "</end-media>" tag and "/>", get first found: smaller position
                    $end_media =
                        ($end_media_1 < $end_media_2) ?
                            $end_media_1 + strlen('</en-media>') :
                            $end_media_2  + strlen('/>');
                }
                else if ($end_media_1 === FALSE)
                {
                    // Found "/>" instead of "</en-media>"
                    $end_media = $end_media_2  + strlen('/>');
                }
                else
                {
                    // Found "</en-media>" instead of "/>"
                    $end_media = $end_media_1 + strlen('</en-media>');
                }

                $media_tag = substr($note_content, 0, $end_media);

                if ($resource['valid']) {
                    $media_tags['valids'][] = $media_tag;
                } else {
                    $media_tags['invalids'][] = $media_tag;
                }

                $note_content = substr($note_content, $end_media);
            }
        } catch (Exception $e) {
            $this->error_msg = __METHOD__ . " - $e->getMessage()";
        }

        return $media_tags;
    }
    /**
    * Uploads images to Wordpress blog
    *
    * @return $base64_imgs with "url" property added, the url of every image
    *
    */
    protected function xml_rpc_upload_images_to_wordpress($base64_imgs)
    {
        log_message('debug', __METHOD__);

        $l = count($base64_imgs);
        for ($i=0; $i < $l; $i++) {
            $image = $base64_imgs[$i];
            $ext = '.' . substr("image/png", strpos("image/png", "/") +1);

            $file_data = array(
                'name' => strtotime(' ') . $ext,// name,
                'type' => $image["mime"],
                'bits' => $image["base64"] //'bits' => $image["bits"]
            );

            $upload_params = array(
                0, //blogid
                $this->user->wp_username,
                $this->user->wp_pass,
                $file_data
            );

            $q = $this->_get_IXR_Client();

            $response;
            if(!$q->query('wp.uploadFile', 0, $this->user->wp_username, $this->user->wp_pass, $file_data)){
                log_message('error', $q->getErrorCode().': '.$q->getErrorMessage());
            } else {
                $response = $q->getResponse();
            }
            if (isset($response)) {
                if (isset($response["url"])) {
                    $base64_imgs[$i]["url"] = $response["url"];
                }
            }
        }

        return $base64_imgs;
    }
    public function check_wp_credentials($blog_url, $wp_username, $wp_pass)
    {
            $blog_url = $this->_get_xmlrpc_url($blog_url);
            $params = array(
                0, //blogid
                $wp_username,
                $wp_pass
            );
            $q = $this->_get_IXR_Client($blog_url, $params);
            if(!$q->query('wp.getOptions', 0, $wp_username, $wp_pass)){
                log_message('error', 'check_wp_credentials: ' . $q->getErrorCode().': '.$q->getErrorMessage());
                return false;
            }
            return true;
    }

    /*
    * post_inmediately = false will send a draft post
    */
    public function xml_rpc_to_wordpress(
        $note_guid,
        $title,
        $body,
        $category,
        $post_inmediately = false,
        $keywords='',
        $encoding='UTF-8'
        )
    {
        log_message('debug', __METHOD__);

        $title    = htmlentities($title, ENT_NOQUOTES, $encoding);
        $keywords = htmlentities($keywords, ENT_NOQUOTES, $encoding);

        $content = array(
            'title'             => $title,
            'description'       => $body,
            'mt_allow_comments' => 1,  // 1 to allow comments
            'mt_allow_pings'    => 1,  // 1 to allow trackbacks
            'post_type'         => 'post',
            'mt_keywords'       => $keywords,
            'categories'        => array($category)
        );

        $q = $this->_get_IXR_Client();

        $api_function_name = 'metaWeblog.newPost';

        // 1.- Check if post was already posted
        $post_link = $this->get_note_post_link_by_note_guid($this->user->id, $note_guid);
        if (!is_null($post_link)) {
            $api_function_name = 'metaWeblog.editPost';
        }

        switch ($api_function_name) {
            case 'metaWeblog.newPost':
                if(!$q->query($api_function_name, 0, $this->user->wp_username, $this->user->wp_pass, $content, $post_inmediately)){
                    $this->error_msg = $q->getErrorCode().': '.$q->getErrorMessage();
                    log_message('error', $this->error_msg);
                    return false;
                } else {
                    $response = $q->getResponse();
                    if (is_null($post_link)) {
                        $this->insert_note_post_link($this->user->id, $note_guid, $response);
                    }
                }
                break;
            case 'metaWeblog.editPost':
                if(!$q->query($api_function_name, $post_link->post_id, $this->user->wp_username, $this->user->wp_pass, $content, $post_inmediately)){
                    $this->error_msg = $q->getErrorCode().': '.$q->getErrorMessage();
                    log_message('error', $this->error_msg);
                    return false;
                }
                break;
        }

        return true;

    }
    private function _get_IXR_Client($blog_url = null)
    {
        if ($blog_url == null) $blog_url = $this->user->wp_blog_url;

        if (strpos($blog_url, "https",0) === 0) {
            $server = str_replace("https", "ssl", $blog_url);
            $bits = parse_url($server);
            $path = isset($bits['path']) ? $bits['path'] : '/';
            $q = new IXR_ClientSSL($bits['host'], $path, 443, 15);
        } else {
            $q = new IXR_Client($blog_url);
        }
        return $q;
    }
    /*======================
    == DATABASE FUNCTIONS ==
    =======================*/

    public function get_all_wp_users_with_encrypted_pass()
    {
        $this->db->select('id, wp_pass');

        $query = $this->db->get($this->table_name);
        if ($query->num_rows() > 0) {
            $result = $query->result();
            for ($i=0; $i < $query->num_rows(); $i++) {
                $result[$i]->wp_pass = $this->eatagscrypt->fnEncrypt($result[$i]->wp_pass, EVERNOTE_TOKEN_SALT);
            }
            return $result;
        }
        return NULL;
    }
    public function set_all_users_encrypted_wp_pass()
    {
        $wpusers = $this->get_all_wp_users_with_encrypted_pass();
        $length = count($wpusers);

        $this->db->trans_start();

        for ($i=0; $i <$length ; $i++) {
            $this->db->set('wp_pass', $wpusers[$i]->wp_pass);
            $this->db->where('id', $wpusers[$i]->id);
            $this->db->update($this->table_name);
        }

        $this->db->trans_complete();
    }
    public function get_wp_user_by_evernote_user_id($evernote_user_id)
    {
        $this->db->where($this->evernote_user_id, $evernote_user_id);

        $result = $this->db->get($this->table_name);
        if ($result->num_rows() == 1) {
            $row = $result->row();
            $row->wp_pass = $this->eatagscrypt->fnDecrypt($row->wp_pass, EVERNOTE_TOKEN_SALT);
            return $row;
        }
        return NULL;
    }
    public function get_note_post_link_by_note_guid($blog_id, $note_guid)
    {
        log_message('debug', __METHOD__);
        $this->db->where($this->blog_id, $blog_id);
        $this->db->where($this->note_guid, $note_guid);

        $result = $this->db->get($this->table_note_post_link);
        if ($result->num_rows() == 1) return $result->row();
        return NULL;
    }
    public function insert_note_post_link($blog_id, $note_guid, $post_id)
    {
        log_message('debug', __METHOD__);
        $data = array(
            $this->blog_id   => $blog_id,
            $this->note_guid => $note_guid,
            $this->post_id   => $post_id
        );
        $response = $this->db->insert($this->table_note_post_link, $data);
    }
    private function _save_config_data_at_db()
    {
        $data = array(
            'wp_blog_url'      => $this->input->post('wp_blog_url'),
            'wp_username'      => $this->input->post('wp_username'),
            'wp_pass'          => $this->eatagscrypt->fnEncrypt($this->input->post('wp_pass'), EVERNOTE_TOKEN_SALT),
        );

        // CHECK IF USER EXISTS AT WP CONFIG TABLE
        if( $this->get_wp_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            // EXISTS => UPDATE
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            $response = $this->db->update($this->table_name, $data);
        } else {
            // NOT EXISTS => INSERT
            $data['evernote_user_id'] = $this->session->userdata('evernote_user_id');
            $response = $this->db->insert($this->table_name, $data);
        }

        return $response;
    }

    private function _delete_user_data_from_db()
    {
        // CHECK IF USER EXISTS AT WP CONFIG TABLE BECAUSE I AM A "CAGADO"
        if( $this->get_wp_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) ){
            $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
            $response = $this->db->delete($this->table_name);
        } else {
            $response = FALSE;
        }
        return $response;
    }
    public function wordpress_evernote_logout()
    {
        if( !$this->get_wp_user_by_evernote_user_id($this->session->userdata('evernote_user_id')) )
            return FALSE;

        $this->db->trans_start();

        $this->db->select(array('id'));
        $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
        $query = $this->db->get($this->table_name);
        $blog_id = $query->row()->id;

        $this->db->where('blog_id', $blog_id);
        $this->db->delete($this->table_note_post_link);

        $this->db->where('evernote_user_id', $this->session->userdata('evernote_user_id'));
        $this->db->delete($this->table_name);

        $this->db->trans_complete();

        return TRUE;
    }
    /*==================================
    == FEATURE CONFIGURATION FUNCTION ==
    ==================================*/

    public function account_config()
    {
        $response = array(
            'wp_blog_url' => '',
            'wp_username' => '',
            'wp_pass' => '',
        );

        if( $this->input->post('action_type') ){
            // IF IT IS COMMING FROM THE WORDPRESS CONFIG FORM
            if($this->input->post('action_type') == 'save'){
                // TRY TO SAVE NEW CONFIGURATION
                /* TODO: CHECK NOT ONLY IF IS VOID TRY TO CHECK WITH WP THAT DATA IS CORRECT */
                $response['wp_blog_url'] = 'save';
                $this->form_validation->set_rules('wp_blog_url', 'Wordpress URL', 'required');
                $this->form_validation->set_rules('wp_username', 'Wordpress Username', 'required');
                $this->form_validation->set_rules('wp_pass', 'Wordpress Password', 'required');
                if ($this->form_validation->run()) {
                    $are_valid_credentials = $this->check_wp_credentials(
                        $this->input->post('wp_blog_url'),
                        $this->input->post('wp_username'),
                        $this->input->post('wp_pass')
                    );
                    if ($are_valid_credentials) {
                        $response['config_saved'] = $this->_save_config_data_at_db();
                    } else {
                        $response['config_saved'] = FALSE;
                    }

                }
            } else if ($this->input->post('action_type') == 'delete'){
                // DELETE USER WORDPRESS DATA FROM DB AND MARK FEATURE AS UNACTIVE FOR HIM
                $response['delete_done'] = $this->_delete_user_data_from_db();
            }
        } else {
            // IF IT IS GOING TO WORDPRESS CONFIG FORM TAKE DB DATA
            $user_data = $this->get_wp_user_by_evernote_user_id($this->session->userdata('evernote_user_id'));
            if( $user_data ){
                // USER HAS WP INFO AT DB
                $response['wp_blog_url'] = $user_data->wp_blog_url;
                $response['wp_username'] = $user_data->wp_username;
                // $response['wp_pass'] = '******';//$user_data->wp_pass;
            }
        }
        return $response;
    }
}