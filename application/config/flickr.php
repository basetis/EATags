<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['flickr_key']               = FLICKR_KEY;
$config['flickr_secret']            = FLICKR_SECRET;
$config['flickr_request_token_URL'] = FLICKR_TOKEN_URL;
$config['flickr_authorize_URL']     = FLICKR_AUTHORIZE_URL;
$config['flickr_upload_URL']        = FLICKR_UPLOAD_URL;
if (ENVIRONMENT == 'production') {
    $config['flickr_callback']      = FLICKR_CALLBACK_URL;
} else {
    // Pending better control over environments
    $config['flickr_callback']      = FLICKR_CALLBACK_URL_DEV;
}

