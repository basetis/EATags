<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tags extends CI_Model
{
    private $table_name = 'tags';

    function __construct()
    {
        parent::__construct();
    }

    public function get_tags_basic_info()
    {
        $results = $this->db
            ->select(array('name','description'))
            ->from($this->table_name)
            ->where('is_active','1')
            ->get();
        return $results->result_array();
    }

    public function get_active_tags_name()
    {
        $results = $this->db
            ->select(array('name'))
            ->from($this->table_name)
            ->where('is_active','1')
            ->get();
        return $results->result_array();
    }

    public function get_tags_by_tag_feature($id_tag_feature)
    {
        $results = $this->db
            ->select('name')
            ->from($this->table_name)
            ->where('is_active = 1 AND id_tag_feature = '.$id_tag_feature)
            ->get();
        return $results->result_array();
    }

    // tag type means, "tag name", "tag success name" or "tag fail name"
    public function get_all_tags_names_by_tag_type($column_name)
    {
        $query = $this->db
            ->select('t.'.$column_name)
            ->from($this->table_name . ' t')
            ->get();

        if ($query->num_rows() > 0){
            return $query->result_array();
        }
        return array();
    }
    public function get_highest_priority_tag_in_tag_list($tag_list, $user_id)
    {
        $result = $this->db
            ->select(array('t.id_tag','t.name', 't.success_name', 't.fail_name', 't.match_mode', 't.require_resources', 't.model'))
            ->from($this->table_name .' t')
            ->join('tag_features tf','tf.id_tag_feature = t.id_tag_feature AND tf.is_active = 1','inner')
            ->join('user_active_features uaf','uaf.keyname = tf.keyname AND uaf.evernote_user_id = ' . $user_id,'inner')
            ->where('t.is_active', 1)
            ->where('tf.is_active', 1)
            ->where_in('t.name', $tag_list)
            ->order_by('priority', 'desc')
            ->limit(1)
            ->get();

        if ($result->num_rows() > 0){
            $tag = $result->row_array();

            $options = $this->db
                ->select(array('key', 'value'))
                ->from('tag_options')
                ->where('id_tag', $tag['id_tag'])
                ->get();

            $fields = $this->db
                ->select(array('nf.name'))
                ->from('tag_update_note_fields tunf')
                ->join('note_fields nf','nf.id = tunf.id_field','inner')
                ->where('tunf.id_tag', $tag['id_tag'])
                ->get();

            $tag['options'] = $options->result_array();
            $tag['update_note_fields'] = $fields->result_array();

            return $tag;
        }

        return -1;
    }
}