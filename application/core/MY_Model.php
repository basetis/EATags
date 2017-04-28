<?php
    class MY_Model extends CI_Model
    {
        public $error_msg = '';
        public $tag = NULL; // Object with the structure of the result of model Tags, method get_highest_priority_tag_in_tag_list plus TAG Guid fetched just after

        public function __construct()
        {
             parent::__construct();
             $this->load->library('Enml','enml');
        }
        public function get_error_message(){
            return $this->error_msg;
        }

        public function _prepare_new_note($guid, $title){
            $new_note         = $this->evernote->get_empty_note();
            $new_note->guid   = $guid; // guid required
            $new_note->title  = $title; // title required

            return $new_note;
        }
    }


/* End of file MY_Model.php */