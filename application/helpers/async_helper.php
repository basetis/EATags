<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
// ------------------------------------------------------------------------

// ------------------------------------------------------------------------

/**
 * Execute an async post
 *
 *
 *
 * @access	public
 * @param	string, string
 * @return	none
 */
if ( ! function_exists('curl_post_async'))
{
	function curl_post_async($url, $params)
	{
        foreach ($params as $key => &$val) {
          if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        switch ($parts['scheme']) {
            case 'https':
                $scheme = 'ssl://';
                $port = 443;
                break;
            case 'http':
            default:
                $scheme = '';
                $port = 80;
        }

        $fp = @fsockopen($scheme . $parts['host'], $port, $errno, $errstr, 30);

        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
	}
}

// ------------------------------------------------------------------------




/* End of file async_helper.php */
/* Location: ./system/helpers/async_helper.php */