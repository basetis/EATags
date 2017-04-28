<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['twitter_consumer_key']     = TWITTER_KEY;
$config['twitter_consumer_secret']  = TWITTER_SECRET;

if (ENVIRONMENT == 'production') {
    $config['twitter_callback_URL'] = TWITTER_CALLBACK_URL;
} else {
    // Pending better control over environments
    $config['twitter_callback_URL'] = TWITTER_CALLBACK_URL_DEV;
}