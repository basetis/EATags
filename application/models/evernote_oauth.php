<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Evernote_oauth extends CI_Model {
	public $last_error;
	public $current_status;

	function __construct()
	{
		parent::__construct();
	}

	/*
	* The first step of OAuth authentication: the client (this application) 
	* obtains temporary credentials from the server (Evernote). 
	*
	* After successfully completing this step, the client has obtained the
	* temporary credentials identifier, an opaque string that is only meaningful 
	* to the server, and the temporary credentials secret, which is used in 
	* signing the token credentials request in step 3.
	*
	* This step is defined in RFC 5849 section 2.1:
	* http://tools.ietf.org/html/rfc5849#section-2.1
	*
	* @return boolean TRUE on success, FALSE on failure
	*/
	public function get_temporary_credentials() 
	{
		try {
			log_message('debug', __METHOD__);
           	log_message('debug', OAUTH_CONSUMER_KEY);
			$oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
			$requestTokenInfo = $oauth->getRequestToken(REQUEST_TOKEN_URL, $this->get_callback_url());
			if ($requestTokenInfo) {
				$this->session->set_userdata(array(
					'evernote_request_token'        => $requestTokenInfo['oauth_token'],
					'evernote_request_token_secret' => $requestTokenInfo['oauth_token_secret']
				));

				$this->current_status = 'Obtained temporary credentials';
				return TRUE;
			} else {
				$this->last_error = 'Failed to obtain temporary credentials: ' . $oauth->getLastResponse();
			}
		} catch (OAuthException $e) {
		 	$this->last_error = 'Error obtaining temporary credentials: ' . $e->getMessage();
		}
		return false;
	}

	/*
	* The completion of the second step in OAuth authentication: the resource owner 
	* authorizes access to their account and the server (Evernote) redirects them 
	* back to the client (this application).
	* 
	* After successfully completing this step, the client has obtained the
	* verification code that is passed to the server in step 3.
	*
	* This step is defined in RFC 5849 section 2.2:
	* http://tools.ietf.org/html/rfc5849#section-2.2
	*
	* @return boolean TRUE if the user authorized access, FALSE if they declined access.
	*/
	public function handle_callback() 
	{
		$params = $this->input->get(NULL, TRUE);

		if (isset($params['oauth_verifier'])) {
			$this->session->set_userdata(array('evernote_oauth_verifier' => $params['oauth_verifier']));
			$this->current_status = 'Content owner authorized the temporary credentials';
			return TRUE;
		} else {
			// If the User clicks "decline" instead of "authorize", no verification code is sent
			$this->last_error = 'Content owner did not authorize the temporary credentials';
			return FALSE;
		}
	}

	/*
	* The third and final step in OAuth authentication: the client (this application)
	* exchanges the authorized temporary credentials for token credentials.
	*
	* After successfully completing this step, the client has obtained the
	* token credentials that are used to authenticate to the Evernote API.
	* In this sample application, we simply store these credentials in the user's
	* session. A real application would typically persist them.
	*
	* This step is defined in RFC 5849 section 2.3:
	* http://tools.ietf.org/html/rfc5849#section-2.3
	*
	* @return boolean TRUE on success, FALSE on failure
	*/
	public function get_token_credentials() 
	{
		if (strlen($this->session->userdata('evernote_access_token')) > 0) {
			$this->last_error = 'Temporary credentials may only be exchanged for token credentials once';
			return FALSE;
		}

		try {
			log_message('debug', __METHOD__);
           	log_message('debug', OAUTH_CONSUMER_KEY);
			$oauth = new OAuth(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
			$oauth->setToken($this->session->userdata('evernote_request_token'), $this->session->userdata('evernote_request_token_secret'));
			$accessTokenInfo = $oauth->getAccessToken(ACCESS_TOKEN_URL, null, $this->session->userdata('oauthVerifier'));
			if ($accessTokenInfo) {
				$this->session->set_userdata('evernote_access_token', $accessTokenInfo['oauth_token']);
				$this->session->set_userdata('evernote_token_expires', (int)($accessTokenInfo['edam_expires'] / 1000));
				$this->session->set_userdata('evernote_user_id', $accessTokenInfo['edam_userId']);
				
				// $this->session->set_userdata('evernote_access_token_secret', $accessTokenInfo['oauth_token_secret']);
				// $this->session->set_userdata('evernote_note_store_url', $accessTokenInfo['edam_noteStoreUrl']);
				// $this->session->set_userdata('evernote_web_api_url_prefix', $accessTokenInfo['edam_webApiUrlPrefix']);
				
				$this->current_status = 'Exchanged the authorized temporary credentials for token credentials';
				return $accessTokenInfo;
			} else {
				$this->last_error = 'Failed to obtain token credentials: ' . $oauth->getLastResponse();
			}
		} catch (OAuthException $e) {
			$this->last_error = 'Error obtaining token credentials: ' . $e->getMessage();
		}  
		return FALSE;
	}
	
	/*
	* Get the URL of this application. This URL is passed to the server (Evernote)
	* while obtaining unauthorized temporary credentials (step 1). The resource owner 
	* is redirected to this URL after authorizing the temporary credentials (step 2).
	*/
	public function get_callback_url() 
	{
		$protocol = $this->input->server('HTTPS');
		$thisUrl = (empty($protocol)) ? "http://" : "https://";
		$thisUrl .= $this->input->server('SERVER_NAME');
		$thisUrl .= ($this->input->server('SERVER_PORT') == 80 || $this->input->server('SERVER_PORT') == 443) ? "" : (":".$this->input->server('SERVER_PORT'));
		$thisUrl .= $this->input->server('SCRIPT_NAME');
		$thisUrl .= '?action=callback';
		return $thisUrl;
	}
	/*
	* Get the Evernote server URL used to authorize unauthorized temporary credentials.
	*/
	public function get_authorization_url() 
	{
		$url = AUTHORIZATION_URL;
		$url .= '?oauth_token=';
		$url .= urlencode($this->session->userdata('evernote_request_token'));
		return $url;
	}  

}	