<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->language('header');
        $this->load->language('footer');
        $this->load->language('account');
    }

    function index()
    {
        $this->features();
    }

    private function check_userdata( $page = '')
    {
        if( $this->session->userdata('eat.logged.ok') ) {
            if ($page == 'profile_config') {
                $this->profile_config();
            } else {
                if( $this->session->userdata('evernote_access_token') ) {
                    if ($this->evernote->user_granted_evernote_access($this->session->userdata('evernote_access_token'))) {
                        $this->features_config($page);
                    } else {
                        $this->session->set_userdata('evernote_access_token','');
                        $this->require_evernote_access();
                    }
                } else {
                    $this->require_evernote_access();
                }
            }
        } else {
            redirect('auth/login');
        }
    }
    function evernote_login()
    {
        $this->check_userdata('require_evernote_access');
    }

    function features($features_section = 'features_config')
    {
        $this->check_userdata($features_section);
    }

    function profile()
    {
        $this->check_userdata('profile_config');
    }

    private function require_evernote_access()
    {
        $data['current_page'] = 'require_evernote_access';
        $this->load->model('eat/Tag_features', 'tag_features');
        $data['user_features'] = $this->tag_features->get_tag_features($this->session->userdata('user_id'));
        $this->load->view('account/user_require_evernote', $data);
    }

    private function features_config($features_section = 'features_config')
    {
        $this->load->language('features');
        $data = array();
        $data['current_page'] = 'features_config';
        $data['current_section'] = $features_section;

        $view = 'account/user_features_config';
        $this->load->model('eat/Tag_features', 'tag_features');
        $pre_user_features = $this->tag_features->get_tag_features($this->session->userdata('evernote_user_id'));
        foreach ($pre_user_features as $feature) {
            if( $feature['keyname'] == $features_section ){
                $this->load->model('eat/eat_' . $feature['keyname'], 'feature_model');
                $data['config_data'] = $this->feature_model->account_config();
                $data['user_activated'] = $feature['user_activated'];
                $view = 'account/user_' . $feature['keyname'] . '_config';
                break;
            }
        }
        // IF COMMING FROM A FETURE CONFIG USER FEATURES COULD BEEN CHANGED
        if( $features_section != 'features_config' ){
            $data['user_features'] = $this->tag_features->get_tag_features($this->session->userdata('evernote_user_id'));
        } else {
            $data['user_features'] = $pre_user_features;
        }

        if ($features_section == 'first_access') {
            $data['config_data'] = array(
                'info_message' => 'We are creating some tags to your Evernote account, you will find your next sync.
            Otherwise you can start to using them, enjoy!'
            );
        }

        $this->load->view($view, $data);
    }

    private function profile_config()
    {
        log_message('debug', __METHOD__);

        $data = array();
        $data['current_page'] = 'profile_config';
        $data['current_section'] = 'profile_config';

        // LOAD ACTUAL EMAIL
        $this->load->model('account/user_profile','user_profile');
        $data['current_email'] = $this->user_profile->get_mail_by_evernote_user_id($this->session->userdata('user_id'));
        // CHECK IF USER IS REGISTERED TO MAILING LISTS
        $data['mailing_check'] = $this->user_profile->check_ml_from_db($this->session->userdata('user_id'));
        // CHECK IF SOME POST IS RECEIVED
        if( $this->input->post('action_type') == 'goto_unregister'){
            // UNREGISTER REQUIRED
            $view = 'auth/unregister_form';
        }
        else if( $this->input->post('action_type') == 'goto_logout'){
            // LOGOUT REQUIRED
            $view = 'auth/logout_form';
        } else {
            if( $this->input->post('action_type') ){
                // CHECK FOR PROFILE CHANGES

                $data['config_data'] = $this->user_profile->account_config();
                log_message('debug', $data['config_data']);
            }
            $view = 'account/user_profile_config';
        }
        $this->load->model('eat/Tag_features', 'tag_features');
        $data['user_features'] = $this->tag_features->get_tag_features($this->session->userdata('user_id'));
        $this->load->view($view, $data);

    }

    public function request($key)
    {
        log_message('debug', __METHOD__);
        /* TODO: WHEN MORE THAN THREE REQUEST IMPLEMENTED, THINK ABOUT A NEW LESS SPECIFIC STRATEGY */
        if( $this->session->userdata('evernote_access_token') ){
            switch ($key){
                case 'twitter':
                    /* TODO: MOVE THE MAJOR PART OF THIS TO eat_twitter MODEL */
                    $params = array(
                        'key'    => $this->config->item('twitter_consumer_key'),
                        'secret' => $this->config->item('twitter_consumer_secret')
                    );
                    $this->load->library('twitter_oauth', $params);

                    $response = $this->twitter_oauth->get_request_token($this->config->item('twitter_callback_URL'));
                    $this->session->set_userdata('twitter_token_secret', $response['token_secret']);
                    redirect($response['redirect']);
                break;
                case 'flickr':
                    require_once APPPATH . "libraries/phpFlickr.php";
                    $flickr = new phpFlickr($this->config->item('flickr_key'), $this->config->item('flickr_secret'));
                    $flickr->setToken('');
                    $_SESSION['phpFlickr_auth_token'] = '';
                    $response = $flickr->auth("write", $this->config->item('flickr_callback'));
                    log_message('debug', $this->common->var_dump_object($response));

                break;
                default:
                    log_message('error', __METHOD__ . ' Key unexpected on request action : ->' . $key . '<-' );
                    $this->_show_message('<strong>Ups !</strong> Unexpected error');
                break;
            }
        } else {
            $this->features();
        }
    }
    public function callback($key)
    {
        if( $this->session->userdata('evernote_access_token') ){
            switch ($key){
                // CHECK WICH CALLBACK IS CALLING
                case 'twitter':
                    $this->load->model('eat/eat_twitter', 'twitter_model');
                    $this->_manage_callback($key, $this->twitter_model);
                    break;

                case 'flickr':
                    $this->load->model('eat/eat_flickr', 'flickr_model');
                    $this->_manage_callback($key, $this->flickr_model);
                    break;

                default:
                    log_message('error', __METHOD__ . ' Key unexpected on callback action : ->' . $key . '<-' );
                    $this->_show_message('<strong>Ups !</strong> Unexpected error');
                    break;
            }
        } else {
            $this->features();
        }
    }
    private function _manage_callback($key, $model)
    {
        log_message('debug', __METHOD__ . " > for key: $key");
        log_message('debug', $this->common->var_dump_object($this->input->get()));

        $are_required_get_params_set = false;

        if ($key == 'twitter')
            $are_required_get_params_set = ($this->input->get('oauth_token', TRUE) && $this->input->get('oauth_verifier', TRUE));
        if ($key == 'flickr')
            $are_required_get_params_set = ($this->input->get('frob', TRUE));

        if ($are_required_get_params_set) {
            $config_data = $model->account_config(TRUE);
            $this->session->set_userdata('callback', 1);
            if (isset($config_data['config_saved'])) {
                $this->session->set_userdata('config_saved', $config_data['config_saved']);
            }
            redirect('account/features/' . $key);
        } else {
            log_message('error', __METHOD__ . ' $key callback without required GET params' );
            $this->_show_message('<strong>Ups !</strong> Unexpected error');
        }

    }

    private function _show_message($message)
    {
        $this->session->set_flashdata('alert_message', $message);
        redirect('home');
    }
    public function send_mailing($type_form)
    {
        log_message('debug', __METHOD__);
        $this->load->model('account/user_profile','user_profile');

        $params = $this->input->post(NULL,TRUE);

        if($params){
            $data = array(
                'user_id'       => $this->session->userdata('user_id'),
                'mailing_id'    => $params['mailing-id-hidden'],
                );
            switch ($type_form) {
                case 'reg_ml':
                    $data['regist_mailing'] = $params['mailing'];
                    break;
                case 'unreg_ml':
                    $data['regist_mailing'] = $params['mailing-hidden'];
                    break;
                default:
                    log_message('debug', 'WHAT\'S GOING ON' );
                    break;
            }
            log_message('debug', 'sending ' . $type_form . ' form' );
            $this->user_profile->send_data_to_db($data);
        }
    }
}

/* End of file account.php */
/* Location: ./application/controllers/account.php */
