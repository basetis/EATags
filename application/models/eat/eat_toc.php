<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Eat_toc extends MY_Model
{
    protected $list_of_links = array();
    protected $toc_key = 'simple_toc';

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

        $new_note             = $this->_prepare_new_note($original_note->guid, $original_note->title);
        $current_note_content = $original_note->content;
        $current_note_content = $this->enml->remove_enml_note_tags($current_note_content);
        $current_note_content = $this->evernote->remove_previous_table_of_contents($current_note_content, $this->toc_key);
        $current_note_content = $this->_clean_toc_anchors_from_content($current_note_content);

        if (!$this->error_msg) {
            $result               = $this->_get_list_of_headers($current_note_content);
            $current_note_content = $result["content"];

            $toc                  = $this->_get_toc($result["headers"]);
            $current_note_content = $toc . $current_note_content;

            $new_note             = $this->_prepare_new_note($original_note->guid, $original_note->title);
            $new_note->content    = $this->enml->add_enml_note_tags($current_note_content);
        }

        return $new_note;
    }
    private function _get_toc($headers)
    {
        $previous_h;
        $current_h;
        $toc = '<a name="ToC_start_'.$this->toc_key.'"></a><div><strong><u>Table of Contents</u></strong></div>';
        $toc .= '<div>';
        $toc .= '<ul>';
        foreach ($headers as $value) {
            $li = '';
            $current_h = $value["h"];
            $number_of_depths = 0;
            if (isset($previous_h)) {
                $number_of_depths = $current_h - $previous_h;
            }

            if ($number_of_depths > 0) {
                for ($i=0; $i < $number_of_depths; $i++) $li .= '<ul>'; // Open <ul>'s
            } else if ($number_of_depths < 0) {
                for ($i=0; $i < abs($number_of_depths); $i++) $li .= '</ul>'; // Close <ul>'s
            }

            $li .= '<li><a href="#';
            $li .= $value["key"];
            $li .= '">';
            $li .= $value["text"];
            $li .= '</a></li>';
            $toc.= $li;
            $previous_h = $value["h"];
        }
        if (isset($previous_h)) {
            for ($i=1; $i < $previous_h; $i++) $toc .= '</ul>';
        }
        $toc .= '</ul>';
        $toc .= '</div>';
        // $toc .= '<div><br clear="none"/></div>';
        $toc .= '<hr/><a name="ToC_end_'.$this->toc_key.'"></a>';
        return $toc;
    }
    private function _get_list_of_headers($content)
    {
        log_message('debug', __METHOD__);
        $this->load->library('Enml','enml');
        $this->enml->ignore_anchor_tags_on_text = TRUE;
        $offset        = 0;
        $hash_char_pos = strpos(substr($content, $offset), "#");
        $safe_count    = 100;
        $all_headers   = array();
        $found_count   = 0;
        while ($hash_char_pos !== FALSE) {
            $safe_count--;
            for ($i=6; $i >= 1; $i--) {
                // 1 Find all '#' up to '######'
                $pattern = '/' . str_pad("", $i, "#", STR_PAD_LEFT) . '/'; // Find patterns '######', '#####', '####', '###', '##', '#'
                $subject = substr($content, $hash_char_pos, $i);
                if (preg_match($pattern, $subject, $matches) == 1) {
                    // 2 Store as all_headers
                    $offset = $hash_char_pos + $i;
                    $found_count++;
                    $all_headers[] = array(
                        "position" => $offset,
                        "key"      => 'eat.toc.'.$found_count.'.eat.toc'
                    );
                    break;
                }
            }
            $hash_char_pos = strpos($content , "#", $offset);
            if ($safe_count <= 0) $hash_char_pos = FALSE;
        }

        // 3 Add keyword eat.toc.N.eat.toc on html
        $start = count($all_headers) -1;
        for ($i=$start; $i >= 0; $i--) {
            $content = substr_replace($content, $all_headers[$i]["key"], $all_headers[$i]["position"], 0);
        }

        // 4 Convert to plain text
        // 4.1 (we will have lost some headers in plain text, those that were inside html tags)
        // 5.1 Convert to plain text
        $content_plain = $this->enml->convert_enml_to_text($content);
        // 5.2 Explode plain text into lines
        $content_lines = explode("\n", $content_plain);
        $valid_headers = array();
        foreach ($content_lines as $line) {
            // 6 Find lines starting by "#"
            if ($line && substr($line, 0,1) == "#") {
                for ($i=6; $i >= 1; $i--) {
                    $pattern = '/' . str_pad("", $i, "#", STR_PAD_LEFT) . '/'; // Find patterns '######', '#####', '####', '###', '##', '#'
                    if (preg_match($pattern, $line, $matches) == 1) {
                        // 7 Collect data on $valid_headers
                        // 7.1 Get the keyword
                        // 7.2 Get the number of hashes that determines the header's depth
                        // 7.3 Get header text (without keys and hashes), will be the header name on ToC
                        $pattern = "/eat.toc.\d+.eat.toc/";
                        if (preg_match($pattern, $line, $key_matches)) {
                            $header = substr($line, strlen($key_matches[0]) + $i);
                            $header = preg_replace($pattern, '' , $header);
                            $header = htmlentities($header, ENT_NOQUOTES, 'UTF-8');
                            $valid_headers[] = array(
                                "h"    => $i,
                                "text" => $header,
                                "key"  => $key_matches[0]
                            );
                        }
                        break;
                    }
                }
            }
        }

        $invalid_headers = array();
        // 8 Match $all_headers against $valid_headers and replace by corresponding html on $content
        foreach ($all_headers as $header) {
            $is_valid = false;
            foreach ($valid_headers as $valid_header) {
                if ($valid_header["key"] == $header["key"]) {
                    // 8.1 $valid_headers found on all_headers will be replaced by the hidden anchors <a name='key'></a>
                    $replace = '<a name="'.$header["key"].'"></a>';
                    $content = str_replace($header["key"], $replace, $content);
                    // 8.3 Collect data to build later the ToC
                    $is_valid = true;
                    break;
                }
            }
            if (!$is_valid) {
                $invalid_headers[] = $header;
            }
        }

        // 8.2 $invalid_headers will be deleted from content
        foreach ($invalid_headers as $header) {
            $content = str_replace($header["key"], '', $content);
        }

        // 9 Return an array with new content and necessary data from $valid_headers to build ToC later
        $result = array(
            "content" => $content,
            "headers" => $valid_headers
        );

        $this->html2text->ignore_anchor_tags = FALSE;
        // 10. May the Force be with you
        return $result;
    }


    private function _clean_toc_anchors_from_content($content)
    {
        log_message('debug', __METHOD__);
        // 1 Find eat.toc.N.eat.toc
        $toc_anchors = array();
        $pattern = "/eat.toc.\d+.eat.toc/";
        if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $haystack = substr($content, 0, $match[1]);
                // 2 Find previous <a
                $start_anchor = strripos($haystack, "<a");
                if ($start_anchor !== FALSE) {
                    // 3 Find next a>
                    $end_anchor_1 = strpos($content, "a>",$match[1]);
                    $end_anchor_2 = strpos($content, "/>",$match[1]);
                    $end_anchor   = FALSE;
                    if ($end_anchor_1 !== FALSE && $end_anchor_2 !== FALSE ) {
                        $end_anchor = ($end_anchor_1 <= $end_anchor_2) ? $end_anchor_1 : $end_anchor_2;
                    } else {
                        if ($end_anchor_1 !== FALSE) $end_anchor = $end_anchor_1;
                        if ($end_anchor_2 !== FALSE) $end_anchor = $end_anchor_2;
                    }

                    if ($end_anchor !== FALSE) {
                        $end_anchor += 2; // length of 'a>' or '/>'
                        $toc_anchor = substr($content, $start_anchor, $end_anchor - $start_anchor);
                        $toc_anchors[] = $toc_anchor;
                    } else {
                        log_message('error', "Not found end of anchor");
                    }
                }
            }
        }

        // 4 Clean
        $start = count($toc_anchors) -1;
        for ($i=$start; $i >= 0; $i--) {
            $content = str_replace($toc_anchors[$i], '', $content);
        }
        return $content;
    }

}