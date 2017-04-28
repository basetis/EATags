<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration extends CI_Controller
{
	private $table_name			= 'users';

    function __construct()
    {
        parent::__construct();
    }
    // function from_plain_to_encrypted_tokens()
    // {
    // 	$this->load->model('tank_auth/users', 'users');

    //     $this->users->set_all_users_encripted_token();
    // }
}

/* End of file migration.php */
/* Location: ./application/controllers/migration.php */