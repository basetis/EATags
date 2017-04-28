<?php

	require_once BASEPATH . "../evernote-sdk-php/bootstrap.php";
	require_once $GLOBALS['THRIFT_ROOT'].'/packages/UserStore/UserStore.php';
	require_once $GLOBALS['THRIFT_ROOT'].'/packages/NoteStore/NoteStore.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/transport/THttpClient.php';
    require_once $GLOBALS['THRIFT_ROOT'].'/protocol/TBinaryProtocol.php';
    require_once APPPATH . 'libraries/evernote_comparator.php';
	use EDAM\UserStore\UserStoreClient;
    use EDAM\NoteStore\NoteStoreClient;
    use EDAM\NoteStore\NoteFilter;
	use EDAM\NoteStore\NotesMetadataResultSpec;
	use EDAM\Types\Tag;
	use EDAM\Types\Note;
    use EDAM\Types\Notebook;
    use EDAM\Types\Data;
    use EDAM\Types\Resource;
    use EDAM\Types\ResourceAttributes;
    use EDAM\NoteStore\NoteEmailParameters;
	use EDAM\Error\EDAMErrorCode;
	use EDAM\Error\EDAMUserException;
	use EDAM\Error\EDAMSystemException;
	use EDAM\Error\EDAMNotFoundException;

    class Evernote {
        protected $ci;
        private $note_store;
        private $user_store;
        public $image_mime_types;
        private $user; // this will be an object with table Users row structure
        private $evernote_user_id;

        private $rate_limit_data = array(
                "evernote_user_id"   => "",
                "evernote_note_guid" => "",
                "reason"             => "",
                //"will_be_free_at"    => "DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 15 second)", // temporal
            );

        function __construct()
        {
            $this->ci =& get_instance();
            $this->image_mime_types = array(
                "image/gif",
                "image/jpeg",
                "image/png"
            );
        }
        public function init($access_token, $evernote_user_id, $note_guid = "", $reason = "")
        {
            $this->evernote_user_id = $evernote_user_id;
            $result = $this->set_note_store($access_token);

            $this->rate_limit_data["evernote_user_id"]   = $evernote_user_id;
            $this->rate_limit_data["evernote_note_guid"] = $note_guid;
            $this->rate_limit_data["reason"]             = $reason;

            return $result;
        }
        /*
         * IS NOT BEING USED YET

        public function init_by_evernote_user_id($evernote_user_id)
        {
            $this->ci->load->model('tank_auth/users','users');
            $this->user = $this->ci->users->get_user_by_evernote_user_id($evernote_user_id);

            if (!is_null($this->user)) {
                $this->set_note_store($this->user->evernote_access_token);
            }
        }
        */
        public function get_all_user_tags($access_token)
        {
            log_message('debug', __METHOD__);

            $list_tags = array();
            try {
                $list_tags = $this->note_store->listTags($access_token);
            } catch (Exception $e) {
                //log_message('error', "Error listing tags: identifier: ". $e->identifier . " key: " . $e->key);
                log_message('error', $this->get_exception_error($e, __METHOD__));

            }
            return $list_tags;
        }

        public function get_all_user_notebooks($access_token)
        {
            log_message('debug', __METHOD__);

            $list_notebooks = array();
            try {
                $list_notebooks = $this->note_store->listNotebooks($access_token);
            } catch (Exception $e) {
                log_message('error', $this->get_exception_error($e, __METHOD__));

            }
            return $list_notebooks;
        }
        public function create_child_tags_with_name($tags, $access_token, $evernote_user_id, $parent_tag_guid)
        {
            if (is_null($parent_tag_guid)) {
                log_message('ERROR', "No Parent found, create a new one");
                $parent_tag = new Tag();
                $parent_tag->name = $this->ci->config->item('parent_action_tag_name');
                $parent_tag = $this->create_new_tag($parent_tag, $access_token);
                $parent_tag_guid = $parent_tag->guid;
            } else {
                log_message('error', "Parent found, guid: $parent_tag_guid");
            }

            foreach ($tags as $key => $child) {
                $child_tag = new Tag();
                $child_tag->name = $child;
                $child_tag->parentGuid = $parent_tag_guid;
                try {
                    $child = $this->create_new_tag($child_tag, $access_token);
                } catch (EDAMUserException $e) {
                    switch ($e->errorCode) {
                        case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["DATA_CONFLICT"]:
                            log_message('error', "Error creating child tag: $child_tag->name [DATA_CONFLICT]");
                            break;
                        case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["LIMIT_REACHED"]:
                            log_message('error', "Error creating child tag: $child_tag->name [LIMIT_REACHED]");
                            break;
                    }
                } catch (EDAMNotFoundException $e) {
                    log_message('error', "Error creating child tag: $child_tag->name [EDAMNotFoundException]");
                } catch (EDAMSystemException $e) {
                    if ($e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["RATE_LIMIT_REACHED"]) {
                        log_message('error', __METHOD__ . " > RATE_LIMIT_EXCEPTION");
                        return false;
                    }
                    log_message('error', "Error creating child tag: $child_tag->name [EDAMSystemException]");
                }
            }
            return true;
        }

        private function _create_initial_eat_tags($access_token, $evernote_user_id, $tag_group)
        {
            if (is_null($this->note_store)) {
                log_message('error', __METHOD__ . ' > Error getting Note Store');
                return false;
            }

            $parent_tag = new Tag();
            switch ($tag_group) {
                case 'eat_tags':
                    $parent_tag->name = $this->ci->config->item('parent_action_tag_name');
                    $child_tags       = $this->get_eat_child_tags($tag_group);
                    break;

                case 'eat_success_status_tags':
                    $parent_tag->name = $this->ci->config->item('parent_action_success_tag_name');
                    $child_tags       = $this->get_eat_child_tags($tag_group);
                    break;

                case 'eat_fail_status_tags':
                    $parent_tag->name = $this->ci->config->item('parent_action_fail_tag_name');
                    $child_tags       = $this->get_eat_child_tags($tag_group);
                    break;

                default:
                    log_message('error', "Unknow group of tags");
                    break;
            }

            $error_msg = '';
            try {
                $parent_tag = $this->create_new_tag($parent_tag, $access_token);
            } catch (EDAMUserException $e) {
                if ($e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["DATA_CONFLICT"])
                {
                    // This Tag was already created, get tag list to know his guid
                    log_message('debug', __METHOD__ . ' > Name already in use');
                    try {
                        $list_tags = $this->note_store->listTags($access_token);
                    } catch (EDAMNotFoundException $e) {
                        $error_msg = "Error listing tags: identifier: ". $e->identifier . " key: " . $e->key;
                    }
                    if (!$error_msg) {
                        foreach ($list_tags as $tag_item) {
                            if ($parent_tag->name == $tag_item->name) {
                                $parent_tag->guid = $tag_item->guid;
                                break;
                            }
                        }
                    }
                }
                else if ($e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["LIMIT_REACHED"])
                {
                    $error_msg = __METHOD__ . " > We can't add more tags, limit reached";
                }
            } catch (EDAMSystemException $e) {
                if ($e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["RATE_LIMIT_REACHED"]) {
                    log_message('error', __METHOD__ . " > RATE_LIMIT_EXCEPTION");
                    return false;
                }
                $error_msg = __METHOD__ . " > EDAMSystemException : " . $e->message;
            }
            if ($error_msg) {
                log_message('error', $error_msg);
                return false;
            }

            // create childs
            $tags = array();
            foreach ($child_tags as $key => $child) {
                $child->parentGuid = $parent_tag->guid;
                try {
                    $child = $this->create_new_tag($child, $access_token);
                    $tags[] = $child;
                } catch (EDAMUserException $e) {
                    switch ($e->errorCode) {
                        case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["DATA_CONFLICT"]:
                            log_message('error', "Error creating child tag: $child->name [DATA_CONFLICT]");
                            break;
                        case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["LIMIT_REACHED"]:
                            log_message('error', "Error creating child tag: $child->name [LIMIT_REACHED]");
                            break;
                    }
                } catch (EDAMNotFoundException $e) {
                    log_message('error', "Error creating child tag: $child->name [EDAMNotFoundException]");
                } catch (EDAMSystemException $e) {
                    if ($e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["RATE_LIMIT_REACHED"]) {
                        log_message('error', __METHOD__ . " > RATE_LIMIT_EXCEPTION");
                        return false;
                    }
                    log_message('error', "Error creating child tag: $child->name [EDAMSystemException]");
                }
            }

            if (count($tags) == 0) {
                log_message('error', 'No tags could be created');
            } else if (count($child_tags) != count($tags)) {
                log_message('error', "Some tags couldn't be created");
            }
            return true;

        }
        /**
         * Create the necessary tags to use the application on the User Evernte Account
         *
         * @param String $access_token User's OAuth Access Token
         */
        public function create_initial_tags($access_token, $evernote_user_id)
        {
            $tag_group = 'eat_tags';
            $result    = $this->_create_initial_eat_tags($access_token, $evernote_user_id, $tag_group);

            return $result;
        }

        /**
         * Returns an array of EDAM\Tag objects with the names defined on evernote_config
         *
         * @return Array $child_tags, array of EDAM\Tag objects with the names defined on evernote_config
         */
        public function get_eat_child_tags($tag_group)
        {
            $child_tags = array();
            $property   = '';
            switch ($tag_group) {
                case 'eat_tags':
                    $property = 'name';
                    break;

                case 'eat_success_status_tags':
                    $property = 'success_name';
                    break;

                case 'eat_fail_status_tags':
                    $property = 'fail_name';
                    break;

                default:
                    log_message('error', "Unknow group of child tags");
                    return $child_tags;
            }

            // Get child_action_tags structure from DB
            $this->ci->load->model('eat/tags','tags');
            $tag_names = $this->ci->tags->get_all_tags_names_by_tag_type($property);

            foreach ($tag_names as $key => $value) {
                $tag          = new Tag();
                $tag->name    = $value['name'];
                $child_tags[] = $tag;
            }

            return $child_tags;
        }

        /**
         * Create the specified EDAM\Tag on the user noteStore
         * Errors are logged and Exceptions re-thrown
         *
         * @param EDAM\Tag $tag, the tag that you want to create
         * @param String $access_token, user Evernote Oauth Access Token key
         *
         * @throws EDAMUserException, EDAMSystemException or EDAMNotFoundException
         * @return EDAM\Tag $tag, EDAM\Tag created
         */
        public function create_new_tag($tag, $access_token)
        {
            log_message('debug', __METHOD__);
            try {
                $tag = $this->note_store->createTag($access_token, $tag);
            } catch (EDAMUserException $e) {
                $error_msg = "";
                switch ($e->errorCode) {
                    case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["DATA_CONFLICT"]:
                        $error_msg = ($e->parameter == "Tag.name") ? "Creating tag '$tag->name': name already in use" : "Creating tag '$tag->name': Unknown DATA_CONFLICT error";
                        break;
                    case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["BAD_DATA_FORMAT"]:
                        if ($e->parameter == "Tag.name") {
                            $error_msg = "Creating tag '$tag->name': invalid length or pattern";
                        } else if ($e->parameter == "Tag.parentGuid") {
                            $error_msg = "Creating tag: malformed GUID";
                        } else {
                            $error_msg = "Creating tag '$tag->name' parameter $e->parameter : Unknown BAD_DATA_FORMAT error";
                        }
                        break;
                    case $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["LIMIT_REACHED"]:
                        $error_msg = ($e->parameter == "Tag") ? "Creating tag '$tag->name': max tag limit reached" : "Creating tag '$tag->name': Unknown LIMIT_REACHED error";
                        break;
                    default:
                        $error_msg = "Creating tag '$tag->name': Unknown EDAMUserException $e->errorCode";
                        break;
                }
                log_message('error', $error_msg);
                throw $e;
            } catch (EDAMSystemException $e) {
                if (isset(EDAMErrorCode::$__names[$e->errorCode])) {
                    $lastError = 'Error listing notebooks: ' . EDAMErrorCode::$__names[$e->errorCode];
                } else {
                    log_message('error', "Error creating Tag $tag->name: $e->getCode(): $e->getMessage()");
                }
                log_message('error', 'EDAMSystemException');
                log_message('error', $this->ci->common->var_dump_object($e));
                throw $e;
            } catch (EDAMNotFoundException $e) {
                if ($e->identifier == "Tag.parentGuid") {
                    log_message('error', "Creating tag '$tag->name': not found, by GUID");
                } else {
                    log_message('error', "Creating tag '$tag->name': Unknown EDAMNotFoundException identifier");
                }
                throw $e;
            }
            return $tag;
        }
        public function get_note_store()
        {
            return $this->note_store;
        }
        public function set_note_store($access_token)
        {
            log_message('debug', __METHOD__);

            $this->note_store = NULL;

            $userStoreTrans;
            try{
                $userStoreTrans = new THttpClient(USER_STORE_HOST, USER_STORE_PORT, USER_STORE_URL, USER_STORE_PROTO);
            }
            catch(TTransportException $e)
            {
                log_message('error', $e->errorCode.' Message:'.$e->parameter);
                return false;
            }

            $userStoreProt   = new TBinaryProtocol($userStoreTrans);
            $userStoreClient = new UserStoreClient($userStoreProt, $userStoreProt);

            $user;
            try {
                $user = $userStoreClient->getUser($access_token);
            } catch (Exception $e) {
                // log_message('error', __METHOD__ . $this->get_exception_error($e));
                log_message('error', $this->get_exception_error($e, __METHOD__));

                $class = get_class($e);
                if ($class == 'EDAM\\Error\\EDAMUserException' && $e->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["AUTH_EXPIRED"]) {
                    // Access Token not valid, has expired, mark user on DB
                    $this->ci->load->model('tank_auth/users');
                    $this->ci->users->mark_user_as_token_expired($this->evernote_user_id);
                }

                return false;
            }

            $noteStore = NULL;
            if (isset($user)) {
                $noteStoreTrans = new THttpClient(NOTESTORE_HOST, NOTESTORE_PORT, NOTESTORE_URL . $user->shardId, NOTESTORE_PROTOCOL);
                $noteStoreProt  = new TBinaryProtocol($noteStoreTrans);
                $noteStore      = new NoteStoreClient($noteStoreProt, $noteStoreProt);
            }

            $this->note_store = $noteStore;
            return true;
		}
        private function _set_user_store($access_token)
        {
            log_message('debug', __METHOD__);
            $error_msg = "";

            $this->user_store = NULL;

            $userStoreProt;
            $userStoreTrans;
            try{
                log_message('debug', "Getting userStoreTrans");
                $userStoreTrans = new THttpClient(USER_STORE_HOST, USER_STORE_PORT, USER_STORE_URL, USER_STORE_PROTO);
            }
            catch(TTransportException $e)
            {
                $error_msg = $e->errorCode.' Message:'.$e->parameter;
                log_message('error', $error_msg);
                return $error_msg;
            }

            try {
                log_message('debug', "Getting userStoreClient");
                $userStoreProt      = new TBinaryProtocol($userStoreTrans);
                $this->user_store   = new UserStoreClient($userStoreProt, $userStoreProt);
            } catch (Exception $e) {
                // $error_msg = $e->errorCode.' Message:'.$e->getMessage();
                $error_msg = $this->get_exception_error($e, __METHOD__);
                log_message('error', $error_msg);
                return $error_msg;
            }
        }

        public function revokeLongSession($access_token){
            log_message('debug', __METHOD__);

            $this->_set_user_store($access_token);

            try {
                if (isset($this->user_store) && !is_null($this->user_store)) {
                    $this->user_store->revokeLongSession($access_token);
                }
            } catch(Exception $e) {
                log_message('error', $this->get_exception_error($e, __METHOD__));
                return false;
            }
            return true;
        }

        public function user_granted_evernote_access($access_token)
        {
            log_message('debug', __METHOD__);

            $userStoreTrans;
            try{
                $userStoreTrans = new THttpClient(USER_STORE_HOST, USER_STORE_PORT, USER_STORE_URL, USER_STORE_PROTO);
            }
            catch(TTransportException $e)
            {
                log_message('error', $e->errorCode.' Message:'.$e->parameter);
                return false;
            }

            $userStoreProt   = new TBinaryProtocol($userStoreTrans);
            $userStoreClient = new UserStoreClient($userStoreProt, $userStoreProt);

            $user;
            try {
                $user = $userStoreClient->getUser($access_token);
            } catch (Exception $e) {
                //log_message('error', __METHOD__ . ' > ' . $this->get_exception_error($e));
                log_message('error', $this->get_exception_error($e, __METHOD__));
                return false;
            }
            return true;

        }
        public function find_notes_metadata_by_notebook($access_token, $notebook_guid)
        {
            log_message('debug', __METHOD__);
            // Order by Title ascending
            $filter = new NoteFilter(array('notebookGuid' => $notebook_guid, 'order' => 5, 'ascending' => true));
            $result_spec = new NotesMetadataResultSpec(array('includeTitle' => true));
            if (is_null($this->note_store)) {
                log_message('error', __METHOD__ . ' -> note_store is not set');
                return false;
            }
            try {
                return $this->note_store->findNotesMetadata($access_token, $filter, 0, 1000, $result_spec);
            }
            catch (Exception $e) {
                // log_message('error', __METHOD__ . ' > ' . $this->get_exception_error($e));
                log_message('error', $this->get_exception_error($e, __METHOD__));
                return false;
            }
        }
        public function find_notes_metadata_by_tags($access_token, $list_tag_guids)
        {
            log_message('debug', __METHOD__);
            // Order by Title ascending
            $filter = new NoteFilter(array('tagGuids' => $list_tag_guids, 'order' => 5, 'ascending' => true));
            $result_spec = new NotesMetadataResultSpec(array('includeTitle' => true));
            if (is_null($this->note_store)) {
                log_message('error', __METHOD__ . ' -> note_store is not set');
                return false;
            }
            try {
                return $this->note_store->findNotesMetadata($access_token, $filter, 0, 1000, $result_spec);
            }
            catch (Exception $e) {
                // log_message('error', __METHOD__ . ' > ' . $this->get_exception_error($e));
                log_message('error', $this->get_exception_error($e, __METHOD__));
                return false;
            }
        }
        public function get_user($access_token)
        {
            log_message('debug', __METHOD__);

            $this->_set_user_store($access_token);
            if (is_null($this->user_store)) {
                log_message('error', __METHOD__ . ' -> user_store is not set');
                return false;
            }
            try {
                log_message('debug', "Getting user");
                return $this->user_store->getUser($access_token);
            } catch (Exception $e) {
                log_message('error', $this->get_exception_error($e, __METHOD__));
                return false;
            }
        }
		public function get_note_by_id($access_token, $note_guid, $options = array())
		{
            log_message('debug', __METHOD__);

			$response = array(
				'error_msg' => '',
			);
            $with_resources_data = isset($options['with_resources_data']) ? $options['with_resources_data'] : false;

            if (!is_null($this->note_store)) {
                try {
                    // Types.Note getNote(string authenticationToken, Types.Guid guid, bool withContent, bool withResourcesData, bool withResourcesRecognition, bool withResourcesAlternateData)
                    // throws Errors.EDAMUserException, Errors.EDAMSystemException, Errors.EDAMNotFoundException

                    $response['note']      = $this->note_store->getNote($access_token, $note_guid, true, $with_resources_data, false, false); // true, true, true);
                    if (!isset($response['note'])) {
                        $response['error_msg'] = __METHOD__ . ' > Error getting Note';
                    }
                } catch(Exception $exception){
                    //  $response['error_msg'] = __METHOD__ . ' ' . $this->get_exception_error($exception);
                    $response['error_msg'] = $this->get_exception_error($exception, __METHOD__);
                }
            } else {
                $response['error_msg'] = __METHOD__ . ' Error getting Note Store';
            }

            if ($response['error_msg']) log_message('error', $response['error_msg']);

			return $response;
		}

        public function get_tag_by_id($access_token, $tag_guid)
        {
            $response = array(
                'error_msg' => ''
            );

            if (!is_null($this->note_store)) {
                try {
                    $response['tag']       = $this->note_store->getTag($access_token, $tag_guid);
                } catch(Exception $exception){
                    // $response['error_msg'] = __METHOD__ . $this->get_exception_error($exception);
                    $response['error_msg'] = $this->get_exception_error($exception, __METHOD__);
                }
            } else {
                $response['error_msg'] = __METHOD__ . ' Error getting Note Store';
            }

            return $response;
        }

		public function get_empty_note(){
			return new Note();
		}

        public function get_exception_error($exception, $method="")
        {
            log_message('error', __METHOD__);
            log_message('debug', print_r($exception->getTraceAsString(), TRUE));

            $error_msg = "";
            $class     = get_class($exception);

            switch ($class){
                case 'EDAM\\Error\\EDAMSystemException':
                    $error_msg = "EDAMSystemException";
                    break;
                case 'EDAM\\Error\\EDAMUserException':
                    $error_msg = "EDAMUserException";
                    break;
                case 'EDAM\\Error\\EDAMNotFoundException':
                    $error_msg = "EDAMNotFoundException";
                    break;
                default:
                    $error_msg = $method . ' > ' .  $exception->getMessage();
                    break;
            }

            if ($class == 'EDAM\\Error\\EDAMSystemException') {
                if ($exception->errorCode == $GLOBALS['\EDAM\Error\E_EDAMErrorCode']["RATE_LIMIT_REACHED"]) {
                    $this->ci->load->model('rate_limit_queue');

                    if (ENVIRONMENT == 'production') {
                        $this->ci->rate_limit_queue
                            ->insert_rate_limit_data(
                                $this->rate_limit_data["evernote_user_id"],
                                $this->rate_limit_data["evernote_note_guid"],
                                $this->rate_limit_data["reason"],
                                $exception->rateLimitDuration
                            );
                    } else {
                        log_message('debug', 'environment is ' . ENVIRONMENT . ' rate limit not inserted in db');
                    }
                    $this->ci->load->model('tank_auth/users');
                    $user_data = $this->ci->users->get_user_by_evernote_user_id($this->rate_limit_data["evernote_user_id"]);
                    $rate_time = explode(":", gmdate("H:i:s", $exception->rateLimitDuration));
                    $data = array (
                        'site_name' => 'EATags',
                        'username'  => $user_data->username,
                        'rate_time' => $rate_time[1]
                    );

                    $this->send_rate_limit_email($this->rate_limit_data["evernote_user_id"], $user_data->email, $data);


                }
            }
            return $error_msg;
        }
        /*
        * @param $data => array('site_name' => 'EATags', 'username' => 'username, 'rate_time' => 'exception->rateLimitDuration')
        */
        public function send_rate_limit_email($evernote_user_id, $email, $data)
        {
            $this->ci->load->model('account/user_profile');

            $this->ci->user_profile->send_email('rate_limit',$email,$data, $evernote_user_id);
        }
        public function filter_tags_to_create($user_tags, $tags_to_create)
        {
            log_message('debug', __METHOD__);

            // Filter user tags against tag we want to create
            $matches = array_filter($user_tags, array(new Evernote_comparator($tags_to_create), 'filter_by_tag_name'));
            if (count($matches) == count($tags_to_create)) {
                return array(); // User has all needed tags, we are done
            }

            if (count($matches) > 0) {
                // If some tags found extract what tags are missing
                $tags_to_create = array_filter($tags_to_create, array(new Evernote_comparator($matches), 'filter_by_tag_name_not_in'));
            } else {
                // No tags found, we need to create all received tags
            }

            return $tags_to_create;
        }
        public function _create_missing_tags($user_tags, $status_parent_tags, $access_token)
        {
            log_message('debug', __METHOD__);

            $tags_to_create     = $this->filter_tags_to_create($user_tags, $status_parent_tags);

            log_message('debug', "Tags to create: " . count($tags_to_create));

            $created_tags = array();
            foreach ($tags_to_create as $tag) {
                try {
                    $created_tags[] = $this->create_new_tag($tag, $access_token);
                } catch (Exception $e) {
                    // log_message('debug', __METHOD__ . ' ' . $this->get_exception_error($e));
                    log_message('debug', $this->get_exception_error($e, __METHOD__));
                }
            }
            return $created_tags;
        }

        public function create_tag_by_name($tag_name, $parent_tag_guid = NULL, $access_token)
        {
            log_message('debug', __METHOD__);

            try {
                $tag             = new Tag();
                $tag->name       = $tag_name;
                if (isset($parent_tag_guid)) { $tag->parentGuid = $parent_tag_guid; }
                return $this->create_new_tag($tag, $access_token);
            } catch (Exception $e) {
                $error_msg = $this->get_exception_error($e, __METHOD__);
                log_message('debug', $error_msg);
            }
        }

        public function update_note($note, $access_token){
            $error_msg = '';
            if (!is_null($this->note_store)) {
                try {
                    $this->note_store->updateNote($access_token, $note);
                } catch(Exception $exception){
                    $error_msg = $this->get_exception_error($exception, __METHOD__);
                }
            } else {
                $error_msg = __METHOD__ . ' Error getting Note Store';
            }

            return $error_msg;
        }

        /*
        * Determina si existe un tag en una colección de tags.
        */
        public function has_tag($tag_collection, $tag_name_to_search){
            $rtn_value = NULL;

            $matches = array_filter($tag_collection, function($tag) use (&$tag_name_to_search) {
                return (strtoupper($tag->name) == strtoupper($tag_name_to_search));
            });

            if (count($matches) == 1) {
                foreach ($matches as $key => $value) {
                    $rtn_value = $value;
                }
            }

            return $rtn_value;
        }

        /**
        * @params $hex_hash, something like: "83b8dcbc012f39cee8b2e840c05446b9"
        */
        private function _get_binary_hash_of_a_resource_by_hex_hash($hex_hash)
        {
            $chunks = explode("\n", chunk_split($hex_hash,2,"\n"));
            $calc_hash = "";
            foreach ($chunks as $chunk) {
                if (!empty($chunk)) {
                    $bin_chunk = $this->ci->common->hex2bin($chunk);
                    $calc_hash .= $bin_chunk;
                }
            }
            return $calc_hash;
        }

        public function is_image_mime($resource)
        {
            $is_valid_mime = false;
            foreach ($this->image_mime_types as $en_mime) {
                if ($en_mime == strtolower($resource->mime)) {
                    $is_valid_mime = true;
                    break;
                }
            }
            return $is_valid_mime;
        }
         /**
        * Extract a simplified list of resources from Note->resources
        * Extract only the resources that matches with image_mime_types
        *
        * @param
        *  $note EDAM\Types\Note
        *  $current_note_content String
        *
        * @return an array with this structure:
        *  $resource["hash"] -> String binary MD5 hash of the body resource
        *  $resource["mime"] -> String MIME TYPE
        *  $resource["body"] -> String binary contents of the body data
        *  $resource["valid"] -> boolean, indicates if has a valid mime
        *  $resource["hex_hash"] -> String where key is the resource index and hex_hash is the hash found inside <en-media> tag on note content
        */
        public function get_resources_data($resources)
        {
            log_message('debug', __METHOD__);
            // Extract list of resources from Note data
            $resources_list = array();
            foreach ($resources as $resource_data)
            {
                $is_valid_mime = false;
                foreach ($this->image_mime_types as $en_mime) {
                    if ($en_mime == strtolower($resource_data->mime)) {
                        $is_valid_mime = true;
                        break;
                    }
                }
                $resource["hash"]     = $resource_data->data->bodyHash;
                $resource["mime"]     = $resource_data->mime;
                $resource["body"]     = $resource_data->data->body;
                $resource["valid"]    = $is_valid_mime;
                $resource["hex_hash"] = md5($resource_data->data->body);
                $resources_list[]  = $resource;
            }

            return $resources_list;
        }
        // TODO: rename $formula_id param to $file_name = ""
		public function get_resource_and_tag_from_img_string($img_string, $options = array(), $formula_id, $style_string = "")
        {
            // Check if some img_string is received
            $response = array();
            if(!trim($img_string)){
                $response['error_msg'] = 'Void img_string on ' . __METHOD__ ;
                return $response;
            }
            // Set basic options
            /* TODO: MOVE DEFAULT VALUES TO PRIVATE MODEL PROPERTY */
            if( !isset($options['name']) ){
                $options['name'] = 'eattags_uploaded_image';
            }
            if( !isset($options['MIME']) ){
                $options['MIME'] = 'image/png';
            }
            // Create a thumbprint of the image data
            $hash     = md5($img_string, 1);
            $hash_hex = md5($img_string, 0);
            // Create a Data object containing the image data
            $data           = new Data();
            $data->size     = strlen($img_string);
            $data->bodyHash = $hash;
            $data->body     = $img_string;
            // Creating the Resource object
            $resource       = new Resource();
            $resource->mime = $options['MIME'];
            $resource->data = $data;

            // Create a ResourceAttributes object to hold metadata
            /* TODO: CHECK IF MORE METADATA IS POSIBLE wiris SHOULD BE PRESENT */
            $resource->attributes           = new ResourceAttributes();
            $resource->attributes->fileName = $formula_id;//basename($options['name']);

            $response = array();
            /* TODO:
                There are som attributes that could be added to en-media tag and perhaps reveibed via options
                These are:
                    align, alt, longdesc, height, width, border, hspace, vspace, usemap
                    style, title, lang, xml:lang, dir
            */
            if (strlen($style_string)) $style_string = "style=\"$style_string\"";
            $response['tag']      = '<en-media ' . $style_string . ' type="' . $options['MIME'] . '" hash="' . $hash_hex . '"/>';
            $response['resource'] = $resource;
            $response['hash_hex'] = $hash_hex;
            return $response;
        }
        public function remove_previous_table_of_contents($content, $key)
        {
            $start_toc = strpos($content, '<a name="ToC_start_'.$key.'"></a>');
            if ($start_toc === FALSE) {
                $start_toc = strpos($content, '<a name="ToC_start_'.$key.'"/>');
                if ($start_toc === FALSE) {
                    return $content;
                }
            }
            $end_toc_str = '<a name="ToC_end_'.$key.'"></a>';
            $end_toc = strpos($content, $end_toc_str);
            if ($end_toc === FALSE) {
                $end_toc_str = '<a name="ToC_end_'.$key.'"/>';
                $end_toc = strpos($content, $end_toc_str);
                if ($end_toc === FALSE) {
                    return $content;
                }
            }
            $end_toc += strlen($end_toc_str);
            $size    = $end_toc - $start_toc;
            $content = substr_replace($content, ' ', $start_toc, $size);
            return $content;
        }
	}


?>