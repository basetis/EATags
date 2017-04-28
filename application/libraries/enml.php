<?php
/******************************************************************************
 * Copyright (c) 2012 EATags IT team.
 *
 *
 * Contributors:
 *    Unknown
 *
 * Public Methods:
 *		convert_enml_to_text($enml)
 *		remove_enml_note_tags($enml)
 *		add_enml_note_tags($html, $attribs)
 *
 ****************************************************************************/


class Enml{
	protected $_ci; // code igniter instance
	// enml xml definition
	protected $enml_xml = EN_XML_DEFINITION;
	// en-note tags related
	protected $enml_note_start_begining = EN_NOTE_START_TAG_BEGINNING;
	protected $enml_note_end = EN_NOTE_END_TAG;
	protected $enml_note_start_attribs = array(
		'bgcolor', 	// background color of the note
		'text', 	// text color
		'style',
		'title',
		'lang',
		'xml:lang',
		'dir',
	);
	// allowed enml tag names and its value as plain text
	protected $enml_tags_to_text = array(
		'en-media' => ' ',
		'en-todo' => ' ',
		'en-crypt' => ' ',
	);
	// allowed enml tag names and its value as html
	// causes the loss of en-media and encrypt
	protected $enml_tags_to_html_to_text = array(
		'<en-media' => '<script',
		'<en-todo' => '<input type="checkbox" ',
		'<en-crypt' => '<script',
		'</en-media>' => '</script>',
		'</en-crypt>' => '</script>',
	);
	// param to ignore href of tag <a> when converting it to plain text
	public $ignore_anchor_tags_on_text = FALSE;

	function __construct()
    {
    	// log_message('debug', 'Enml Class Constructor');
        $this->_ci = & get_instance();
		// log_message('debug', 'Enml Class Initialized');
    }
	/**
	 * Tries to convert the given ENML into a plain text format
	 *
	 *	Specific ENML tags:
	 *		en-note 	-> note start
	 *		en-media	-> media resource
	 *		en-crypt	-> crypted text
	 *		en-todo		-> to-do checkbox
	 *
	 *  BE CAREFUL, tidy_repair_string doesn't work with phpunit
	 *
	 * @param enml the input ENML
	 * @return the ENML converted, as best as possible, to text
	 */
	function convert_enml_to_text($enml) {
		$new_doc = $enml;

		// remove en-note start and end tags
		$new_doc = $this->remove_enml_note_tags($new_doc);

		// replace the other allowed en-note tags
		// with fake html tags
		foreach ($this->enml_tags_to_html_to_text as $enml_tag => $html_value) {
			$new_doc = str_replace ( $enml_tag , $html_value, $new_doc, $number_of_replaces);
		}

		// convert standard HTML to text
		// force utf-8 headers to prevent html2text missfunctions
        $html_head = '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"/></head>';
        $new_doc = $html_head . $new_doc . '</html>';

        // use html2text library
		$this->_ci->load->library('html2text', 'html2text');
		// propagate ignore_anchor_tags value
		$this->_ci->html2text->ignore_anchor_tags = $this->ignore_anchor_tags_on_text;

		$tidy_config = array('clean' => true);
		$new_doc = tidy_repair_string($new_doc, $tidy_config, 'UTF8');

		$new_doc = $this->_ci->html2text->convert_html_to_text($new_doc);

		return $new_doc;
	}

	/**
	 * Removes en-note start and end tag, wich are the top level element of an ENML document
	 *
	 * @param enml the input ENML
	 * @return the ENML without the en-note tags
	 */
	public function remove_enml_note_tags($enml)
	{
		log_message('debug', __METHOD__);

		$new_doc        = $enml;
		// remove en-note start tag
		$enml_doc_start = strpos($new_doc, EN_NOTE_START_TAG_BEGINNING);

		if ($enml_doc_start ||  $enml_doc_start === 0) {
			// discard anything before <en-note
			$new_doc             = substr ( $new_doc, $enml_doc_start + strlen(EN_NOTE_START_TAG_BEGINNING) );
			$enml_ennote_tag_end = $this->_find_tag_close($new_doc);
			$new_doc             = substr ( $new_doc, $enml_ennote_tag_end+1 ); // remove en-note start tag
		} else {
			log_message('debug', 'No en-note start tag found at ->' . $enml . '<-');
		}

		// remove en-note end tag when exists
		$new_doc = str_replace ( $this->enml_note_end , '', $new_doc, $number_of_replaces);
		if( $number_of_replaces != 1 ){
			$log_number = ($number_of_replaces)? $number_of_replaces : '0';
			log_message('debug', 'en-note end tag found ' . $log_number. ' times at ->' . $enml . '<-');
		}

		return $new_doc;
	}

	/**
	 * Adds the top level element of an ENML document
	 *	xml definition and en-note start and end tag
	 *
	 * @param html
	 * @param attribs array of allowed attributes for en-note start tag described at enml_note_start_attribs library property
	 * @return the ENML
	 */
	public function add_enml_note_tags($html, $attribs = array())
    {
    	// add xml definition
        $enml = $this->enml_xml;

        // add en-note start tag
        $note_start_tag = $this->enml_note_start_begining;
        foreach ($attribs as $attrib => $value) {
        	if(in_array($attrib, $this->enml_note_start_attribs)){
        		$note_start_tag.= ' ' . $attrib . '="' . $value . '"';
        	} else {
        		log_message('error', __METHOD__ . ' > Unrecognized attribute ' . $attrib . '');
        	}
        }
        $note_start_tag .= '>';
        $enml .= $note_start_tag;

        $enml .= $html;
        // add en-note end tag
        $enml .= $this->enml_note_end;
        return $enml;
    }

	/**
	 * Returns the first tag close position
	 *
	 * @param string starting at a tag
	 * @param plus is a integer to add to the final result
	 * @return the position of the > wich closes the tag
	 */
	private function _find_tag_close($string, $plus = 0){
		$string_length = strlen($string);
		$next_tag_end = strpos($string,'>');
		if(! ($next_tag_end ||  $next_tag_end === 0) ){
			// no tag close found => error
			// anyway we return as the tag close = the last letter of the string
			log_message('error', __METHOD__ . ' > No tag close found at ->' . $string . '<-');
			return $plus + $string_length-1;
		}

		// check that > is not in a tag attribute => 'blabla>bla' or "blabla>bla"
		$next_apos = strpos($string, "'");
		if( !$next_apos )
			$next_apos = $string_length+1;
		$next_quot = strpos($string, '"');
		if( !$next_quot )
			$next_quot = $string_length+1;
		if( $next_apos < $next_tag_end ){
			$attrib_delimiter = "'";
			$attrib_start = $next_apos+1;
		} else if( $next_quot < $next_tag_end ){
			$attrib_delimiter = '"';
			$attrib_start = $next_quot+1;
		} else {
			return $plus + $next_tag_end;
		}
		// attribute found
		// jump to the end of tag attribute and search again
		$new_string = substr($string, $attrib_start);
		$new_plus = $plus + $attrib_start;
		// prevent from something like "blablabla\"bla"
		$new_string = str_replace ( '\\' . $attrib_delimiter,  '' , $new_string, $number_of_replaces );
		$new_plus+= $number_of_replaces*2;
		$attrib_end = strpos($new_string, $attrib_delimiter);
		if(! ($attrib_end ||  $attrib_end === 0) ){
			// no attribute close found => error
			// anyway we return as the attribute close = the last letter of the reamining string
			log_message('error', __METHOD__ . ' > No attribute delimiter close found at ->' . $new_string . '<-');
			return $plus + $string_length-1;
		} else {
			$new_plus+= $attrib_end + 1;
			$new_string = substr ( $new_string, $attrib_end+1 );
			return $this->_find_tag_close($new_string, $new_plus);
		}
	}
}
