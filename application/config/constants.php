<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


/*
|--------------------------------------------------------------------------
| Evernote SDK Constants
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

// define('THRIFT_ROOT',BASEPATH . '../libraries/evernote-sdk-php/lib/');
// define('THRIFT_ROOT',APPPATH . 'libraries/evernote-sdk-php/lib/');

// Client credentials. Fill in these values with the consumer key and consumer secret
// that you obtained from Evernote. If you do not have an Evernote API key, you may
// request one from http://dev.evernote.com/documentation/cloud/

if (defined('ENVIRONMENT'))
{
    switch (ENVIRONMENT)
    {
        case 'development':
           define('OAUTH_CONSUMER_KEY', 'dev oauth key');
           define('OAUTH_CONSUMER_SECRET', 'dev oauth secret');
           define('EVERNOTE_SERVER', 'https://sandbox.evernote.com');
           define('NOTESTORE_HOST', 'sandbox.evernote.com');
           define("USER_STORE_HOST", "sandbox.evernote.com");
          break;
        case 'testing':
           define('OAUTH_CONSUMER_KEY', 'test oauth key');
           define('OAUTH_CONSUMER_SECRET', 'test oauth secret');
           define('EVERNOTE_SERVER', 'https://sandbox.evernote.com');
           define('NOTESTORE_HOST', 'sandbox.evernote.com');
           define("USER_STORE_HOST", "sandbox.evernote.com");
           break;
        case 'production':
            define('OAUTH_CONSUMER_KEY', 'prod oauth key');
            define('OAUTH_CONSUMER_SECRET', 'prod oauth secret');
            define('EVERNOTE_SERVER', 'https://www.evernote.com');
            define('NOTESTORE_HOST', 'www.evernote.com');
            define("USER_STORE_HOST", "www.evernote.com");

          break;

        default:
            define('OAUTH_CONSUMER_KEY', 'dev oauth key');
            define('OAUTH_CONSUMER_SECRET', 'dev oauth secret');
            define('EVERNOTE_SERVER', 'https://sandbox.evernote.com');
            define('NOTESTORE_HOST', 'sandbox.evernote.com');
            define("USER_STORE_HOST", "sandbox.evernote.com");
            break;
    }
}

define('EVERNOTE_TOKEN_SALT','token salt');

define('NOTESTORE_PORT', '443');
define('NOTESTORE_PROTOCOL', 'https');
define("NOTESTORE_URL", "edam/note/");

define("USER_STORE_PORT", "443"); // 80
define("USER_STORE_PROTO", "https");
define("USER_STORE_URL", "edam/user");

// Evernote server URLs. You should not need to change these values.
define('REQUEST_TOKEN_URL', EVERNOTE_SERVER . '/oauth');
define('ACCESS_TOKEN_URL', EVERNOTE_SERVER . '/oauth');
define('AUTHORIZATION_URL', EVERNOTE_SERVER . '/OAuth.action');

define('EN_NOTE_START_TAG',"<en-note>");
define('EN_NOTE_START_TAG_BEGINNING',"<en-note");
define('EN_NOTE_END_TAG', "</en-note>");
/*define('EN_XML_DEFINITION',"<?xml version=\"1.0\" encoding=\"UTF-8\"?><!DOCTYPE en-note SYSTEM \"http://xml.evernote.com/pub/enml2.dtd\">");*/

define('EN_XML_DEFINITION',"<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?><!DOCTYPE en-note SYSTEM \"http://xml.evernote.com/pub/enml2.dtd\">");

define('ADMIN_KEY', 'admin key');

define('DEVELOPER_EVERNOTE_SANDBOX_USER_ID', 00000);
define('DEVELOPER_EVERNOTE_PRODUCTION_USER_ID', 11111);

define('LOG_TYPE_EVERNOTE', 1);
define('LOG_TYPE_TAG', 2);
define('LOG_TYPE_UNKNOWN', 3);

define('LOG_LEVEL_ERROR',1);
define('LOG_LEVEL_WARN',2);
define('LOG_LEVEL_INFO',3);
define('PRODUCTION_URL', 'https://PRODUCTION.URL.com');
define('DEVELOPMENT_URL', 'http://DEVELOPMENT.URL.com');

/*GOOGLE*/
define('GOOGLE_CLIENT_ID', 'xxx-xxxx1.apps.googleusercontent.com');
define('GOOGLE_SECRET', 'yyyyyyy1');
define('GOOGLE_REDIRECT_URI', PRODUCTION_URL . '/auth/session/google');

define('DEVELOPER_GOOGLE_CLIENT_ID', 'xxx-xxxx2.apps.googleusercontent.com');
define('DEVELOPER_GOOGLE_SECRET', 'yyyyyyy2');
define('DEVELOPER_GOOGLE_REDIRECT_URI', DEVELOPMENT_URL . '/auth/session/google');

/*TWITTER*/
define('TWITTER_KEY', 'twitter key');
define('TWITTER_SECRET', 'twitter secret');
define('TWITTER_CALLBACK_URL', PRODUCTION_URL . '/account/callback/twitter/');
define('TWITTER_CALLBACK_URL_DEV', DEVELOPMENT_URL . '/account/callback/twitter/');

/*FLICKR*/
define('FLICKR_KEY', 'flickr key');
define('FLICKR_SECRET', 'flickr secret');
define('FLICKR_TOKEN_URL', 'http://www.flickr.com/services/oauth/request_token');
define('FLICKR_AUTHORIZE_URL', 'http://www.flickr.com/services/oauth/authorize');
define('FLICKR_UPLOAD_URL', 'http://api.flickr.com/services/upload/');
define('FLICKR_CALLBACK_URL', PRODUCTION_URL . '/account/callback/flickr/');
define('FLICKR_CALLBACK_URL_DEV', DEVELOPMENT_URL . '/account/callback/flickr/');

/*DB*/
define('SERVER_NAME', 'SERVER.COM');
define('DB_USER_NAME', 'USER NAME');
define('DB_USER_NAME_DEV', 'USER NAME DEV');
define('DB_PASSWORD', 'DB PASSWORD');
define('DB_PASSWORD_DEV', 'DB PASSWORD DEV');
define('DB_HOSTNAME', 'DB HOSTNAME');
define('DB_HOSTNAME_DEV', 'DB HOSTNAME DEV');
define('DB_HOSTNAME_TEST', 'DB HOSTNAME TEST');
define('DB_NAME', 'DB NAME');
define('DB_NAME_DEV', 'DB NAME DEV');
define('DB_NAME_TEST', 'DB NAME TEST');

define('ENCRYPTION_KEY', 'encryption key');

/* End of file constants.php */
/* Location: ./application/config/constants.php */
