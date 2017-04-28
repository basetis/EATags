<?php
class Action_note{

    private $original_note;
    private $new_note;
    private $user_token;
    private $error_message;
    private $CI;
    private $all_user_tags;
    private $eatag;
    private $parent_status_tags = array();
    private $status_tags = array();

    public $user_id;
    public $note_guid;
    public $reason;

    public function __construct($params = null)
    {
        $this->CI =& get_instance();
        $this->CI->load->library('evernote');

        $this->user_id   = $params['user_id'];
        $this->note_guid = $params['note_guid'];
        $this->reason    = $params['reason'];

        // get token
        if ($this->_request_user_token() == -1){
            die();
        };

        // init evernote library
        $this->CI->evernote->init($this->user_token, $this->user_id, $this->note_guid, $this->reason);

        // get note
        if ($this->_request_original_note() == -1){
            die();
        }
    }

    public function get_note(){
        return $this->original_note;
    }
    public function set_new_note($note)
    {
        $this->new_note = $note;
    }

    public function get_error_message(){
        return $this->error_message;
    }

    public function get_tags(){
        // obtener todos los tags del usuario (contiene toda la información de cada tag)
        $this->all_user_tags = $this->CI->evernote->get_all_user_tags($this->user_token);

        // nos quedamos con los que empiezan por eat
        $user_eatags = $this->_filter_eat_tags($this->all_user_tags);

        // tags de la nota (sólo los guids)
        $note_tags = $this->original_note->tagGuids;

        // de los eatags del usuario, nos quedamos con los que están en la nota
        $note_eatags = array_filter($user_eatags, function($tag) use (&$note_tags) {
            if (is_null($tag->guid) || is_null($note_tags)) return FALSE;
            return in_array($tag->guid, $note_tags);
        });

        return $note_eatags; // contiene toda la información de cada tag
    }

    public function request_note_resources(){
        $this->_request_original_note(true);
    }

    public function execute_action_by_tag($tag)
    {
        log_message('debug', __METHOD__);
        $this->eatag = $tag;

        $this->CI->load->model($tag['model'], 'tag_model');
        $this->CI->tag_model->tag = $tag;
        $this->new_note      = $this->CI->tag_model->execute_action($this->user_id, $this->user_token, $this->original_note, $tag['options']);
        $this->error_message = $this->CI->tag_model->get_error_message();
    }

    public function update_note($status = 'success'){
        if (isset($this->all_user_tags)){
            // 1. revisar si el usuario tiene creados los tags success y fail para el tag actual.
            $this->_create_status_tags_for_current_tag();

            // 2. remplazar eatag por status tag.
            $this->_replace_note_tag_by_status_tag($status);

            // 3. actualizar la nota
            $this->error_msg = $this->CI->evernote->update_note($this->new_note, $this->user_token);
        }
    }

    // Requests and sets user_token
    private function _request_user_token()
    {
        if (isset($this->user_id))
        {
            $this->CI->load->model('Tank_auth/Users','users');
            $this->user_token = $this->CI->users->get_user_evernote_access_token_by_evernote_user_id($this->user_id);

            if ( !$this->user_token ) {
                $this->error_message = __METHOD__ . ' > No access_token found for user_id [' . $this->user_id .']';
                log_message('error', $this->error_message);
                return -1;
            }
            else{
                log_message('debug', 'user_token ok');
            }
        }
    }

    // Requests and sets original note
    private function _request_original_note($get_resources = false){
        if (isset($this->user_token) && isset($this->note_guid))
        {
            if ($get_resources)
                $request_note = $this->CI->evernote->get_note_by_id($this->user_token, $this->note_guid, array('with_resources_data' => true));
            else
                $request_note = $this->CI->evernote->get_note_by_id($this->user_token, $this->note_guid);

            if ($request_note['error_msg']) {
                $this->error_message = "ERROR found getting note: ". $request_note['error_msg'];
                return -1;
            } else {
                log_message('debug', "Note was get successfully");
            }

            $this->original_note = $request_note['note'];
        }
    }

    public function _filter_eat_tags($tags){
        $this->CI->load->model('eat/Tags', 'tags');
        $valid_tags  = $this->CI->tags->get_active_tags_name();
        $valid_names = array();
        foreach ($valid_tags as $key => $value) {
            array_push($valid_names, $value["name"]);
        }

        $filtered_tags = array();
        foreach ($tags as $key => $value) {
            if (in_array($value->name, $valid_names)) {
                array_push($filtered_tags, $value);
            }
        }

        return $filtered_tags;
    }

    private function _check_create_parent_status_tags(){
        log_message('debug', __METHOD__);

        $success_name   = $this->CI->config->item('parent_action_success_tag_name');
        $fail_name      = $this->CI->config->item('parent_action_fail_tag_name');

        // comprobar si el usuario tiene las etiquetas de estado padres (en la lista de tags del usuario)
        $this->parent_status_tags['success']    = $this->CI->evernote->has_tag($this->all_user_tags, $success_name);
        $this->parent_status_tags['fail']       = $this->CI->evernote->has_tag($this->all_user_tags, $fail_name);

        // si no las tiene, crearlas
        if (!isset($this->parent_status_tags['success'])){
            $this->parent_status_tags['success'] = $this->CI->evernote->create_tag_by_name($success_name, NULL, $this->user_token);
        }
        if (!isset($this->parent_status_tags['fail'])){
            $this->parent_status_tags['fail'] = $this->CI->evernote->create_tag_by_name($fail_name, NULL, $this->user_token);
        }
    }

    private function _check_create_status_tags(){
        log_message('debug', __METHOD__);

        // comprobar si el usuario tiene las etiquetas de estado del eatag procesado (en la lista de tags del usuario)
        $this->status_tags['success']    = $this->CI->evernote->has_tag($this->all_user_tags, $this->eatag['success_name']);
        $this->status_tags['fail']       = $this->CI->evernote->has_tag($this->all_user_tags, $this->eatag['fail_name']);

        // si no las tiene, crearlas
        if (!isset($this->status_tags['success'])){
            $this->status_tags['success'] = $this->CI->evernote->create_tag_by_name($this->eatag['success_name'], $this->parent_status_tags['success']->guid, $this->user_token);
        }
        if (!isset($this->status_tags['fail'])){
            $this->status_tags['fail'] = $this->CI->evernote->create_tag_by_name($this->eatag['fail_name'], $this->parent_status_tags['fail']->guid, $this->user_token);
        }
    }

    private function _create_status_tags_for_current_tag()
    {
        log_message('debug', __METHOD__);

        // de ser necesario crear tags de estado padres
        $this->_check_create_parent_status_tags();

        // de ser necesario crear tags de estado del eatag que se está procesando
        $this->_check_create_status_tags();
    }

    private function _replace_note_tag_by_status_tag($status)
    {
        foreach ($this->all_user_tags as $tag){
            if ($tag->name == $this->eatag['name']){
                $tag_to_replace = $tag;
            }
        }

        $note_tag_guids = $this->original_note->tagGuids;
        foreach ($note_tag_guids as $key => $tag_guid){
            if ($tag_guid == $tag_to_replace->guid){
                $note_tag_guids[$key] = $this->status_tags[$status]->guid;
            }
        }

        $this->new_note->tagGuids = $note_tag_guids;
    }
}
