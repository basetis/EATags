<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Tag_features extends CI_Model
{
    private $table_name = 'tag_features';

    function __construct()
    {
        parent::__construct();
    }

    public function get_tag_features($id_user)
    {
        $this->load->model('eat/Tags', 'tags');

        $results = $this->db
            ->select(array(
                'tf.id_tag_feature',
                'tf.keyname',
                'tf.name',
                'tf.description',
                'tf.config_required',
                'CASE WHEN eaf.keyname IS NULL THEN 0 ELSE 1 END AS user_activated'), false)
            ->from($this->table_name .' tf')
            ->join('user_active_features eaf', 'eaf.keyname = tf.keyname AND eaf.evernote_user_id = '. $id_user, 'left')
            ->where('is_active = 1')
            ->order_by("tf._order")
            ->get();

        $tag_features = $results->result_array();
        foreach ($tag_features as $key => $tag_feature)
        {
            $tag_features[$key]['tags'] = $this->tags->get_tags_by_tag_feature($tag_feature['id_tag_feature']);
        }

        return $tag_features;
    }
}