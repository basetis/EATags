<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Evernote_comparator
{
    protected $ci;
    private $reference_tags;

    function __construct($reference_tags = array())
    {
        $this->ci =& get_instance();
        $this->reference_tags = $reference_tags;
    }
    function filter_by_tag_name($tag)
    {
        foreach ($this->reference_tags as $reference_tag) {
            if ($tag->name == $reference_tag->name) {
                return true;
            }
        }
        return false;
    }
    function filter_by_tag_name_not_in($tag)
    {
        $tag_exists = false;
        foreach ($this->reference_tags as $reference_tag) {
            if ($tag->name == $reference_tag->name) {
                $tag_exists = true;
                break;
            }
        }
        return ($tag_exists) ? false : true;
    }
}