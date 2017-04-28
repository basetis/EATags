<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class EatLog extends CI_Model {

	protected $table_name = 'testing_logging';

	function __construct()
	{
		parent::__construct();
	}

	/*
	$this->eatLog->new_msg("msg");
	$this->eatLog->new_msg("msg", __METHOD__);
	$this->eatLog->new_msg("msg", __METHOD__, __LINE__);
	$this->eatLog->new_msg("msg", __METHOD__, __LINE__, LOG_LEVEL_INFO);
	$this->eatLog->new_msg("msg", __METHOD__, __LINE__, LOG_LEVEL_INFO, LOG_TYPE_UNKNOWN);
	*/

	public function new_msg($message = "", $method = "", $line = 0, $level = LOG_LEVEL_INFO , $type = LOG_TYPE_UNKNOWN)
	{
		$do_logging = FALSE;
		if (defined('ENVIRONMENT'))
		{
		    if (ENVIRONMENT == 'production') {
		    	if ($level == LOG_LEVEL_ERROR) $do_logging = TRUE;
		    }
		    else {
				$do_logging = TRUE;
		    }
		}
		else {
			$do_logging = TRUE;
		}

		if ($do_logging) {
			$data['message'] = $message;
			$data['level']   = $level;
			$data['method']  = $method;
			$data['line']    = $line;
			$data['type']    = $type;

			$this->db->insert($this->table_name, $data);
		}
	}

}