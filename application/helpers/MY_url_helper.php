<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */


/**
 * anchor_path
 * same as CI anchor(), but return only path. domain is not included
 * @param string $uri
 * @param string $title
 * @param array $attributes
 * @return string
 */
if (!function_exists('anchor_path')) {
	function anchor_path($uri = '', $title = '', $attributes = '') {
		$anchor =  anchor($uri, $title, $attributes);

		$domain = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];

		return preg_replace('#href="'.$domain.'(.*)"#', 'href="$1"', $anchor);
	}// anchor_path
}


/**
 * get base path without domain. works like Codeigniter's base_url().
 * @param string $uri
 * @return string
 */
function base_path($uri = '') {
	$base_url = base_url($uri);
	
	$domain = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
	
	return str_replace($domain, '', $base_url);
}// base_path


/**
 * current_protocol
 * check current protocol and return.
 * @return string
 */
if (!function_exists('current_protocol')) {
	function current_protocol() {
		$CI =& get_instance();
		
		$server_port = $CI->input->server('SERVER_PORT');
		
		if ($server_port == '443') {
			return 'https://';
		} else {
			return 'http://';
		}
	}// current_protocol
}


if (!function_exists('language_switch')) {
	function language_switch() {
		$CI =& get_instance();
		
		if ($CI->config->item('lang_ignore')) {
			$user_lang = $_COOKIE[$CI->config->item('cookie_prefix').'user_lang'];
		} else {
			$user_lang = $CI->config->item('language_abbr');
		}
		$langs = $CI->config->item('lang_uri_abbr');
		
		//
		//$querystring = http_build_query($_GET, '', '&amp;');// because of the line below, we will not use any querystring. just change langauge and go to root web.
		$querystring = null;
		$output = $langs[$user_lang];
		$output .= '<ul class="lang-switch">';
		
		foreach ($langs as $key => $item) {
			if ($key != $user_lang) {
				//$switch_link = site_url($key.$CI->uri->uri_string());// this one will change language with current url (http://localhost/post/post-name to http://localhost/en/post/post-name) which cause 404 error in many pages.
				$switch_link = site_url($key);
				if ($CI->config->item('lang_ignore') == false) {
					$switch_link = preg_replace("/(.*)\/(\w{2})\/(\w{2})(\/.*|$)/", '$1/$3$4', $switch_link);
				}
				$output .= '<li class="language-item language-'.$item.' lang-'.$key.'">'.anchor($switch_link.($querystring != null ? '?' : '').$querystring, $item).'</li>';
			}
		}
		
		$output .= '</ul>';
		
		// clear unuse items
		unset($user_lang, $langs, $switch_link, $key, $item, $CI);
		return $output;
	}// language_switch
}


if (!function_exists('language_switch_admin')) {
	function language_switch_admin() {
		$CI =& get_instance();
		
		if ($CI->config->item('lang_ignore')) {
			$user_lang = $_COOKIE[$CI->config->item('cookie_prefix').'user_lang'];
		} else {
			$user_lang = $CI->config->item('language_abbr');
		}
		$langs = $CI->config->item('lang_uri_abbr');
		
		//
		//$querystring = http_build_query($_GET, '', '&amp;');// because of the line below, we will not use any querystring. just change langauge and go to root web.
		$querystring = null;
		$output = $langs[$user_lang];
		$output .= '<ul class="lang-switch">';
		
		foreach ($langs as $key => $item) {
			if ($key != $user_lang) {
				//$switch_link = site_url($key.$CI->uri->uri_string());// this one will change language with current url (http://localhost/post/post-name to http://localhost/en/post/post-name) which cause 404 error in many pages.
				$switch_link = site_url($key.'/'.$CI->uri->segment(1));
				if ($CI->config->item('lang_ignore') == false) {
					$switch_link = preg_replace("/(.*)\/(\w{2})\/(\w{2})(\/.*|$)/", '$1/$3$4', $switch_link);
				}
				$output .= '<li class="language-item language-'.$item.' lang-'.$key.'">'.anchor($switch_link.($querystring != null ? '?' : '').$querystring, $item).'</li>';
			}
		}
		
		$output .= '</ul>';
		
		// clear unuse items
		unset($user_lang, $langs, $switch_link, $key, $item, $CI);
		return $output;
	}// language_switch_admin
}


/**
 * generate querystring except values in parameter array
 * @uses generate_querystring_except(array('per_page', 'keyword')) will generate querystring like this.. => querystring1=value1&get2=value2&get3=value3
 * @param array $param
 * @param boolean $url_decode
 * @return string
 */
if (!function_exists('generate_querystring_except')) {
	function generate_querystring_except($param = array(), $url_decode = false) 
	{
		if (!is_array($param)) {
			$param = array($param);
		}
		
		$querystrings = $_SERVER['QUERY_STRING'];
		$querystrings_exp = explode('&', $querystrings);

		$output = '';
		
		if (is_array($querystrings_exp)) {
			$output = '';

			foreach ($querystrings_exp as $item) {
				if ($item != null) {
					$item_exp = explode('=', $item);

					if (isset($item_exp[0]) && !in_array($item_exp[0], $param)) {
						if ($url_decode === true) {
							$output .= htmlspecialchars(urldecode($item));
						} else {
							$output .= $item;
						}

						if (end($querystrings_exp) != $item) {
							$output .= '&amp;';
						}
					}
				}
			}// endforeach;
		}

		unset($item, $item_exp, $querystrings, $querystrings_exp);

		// clear &amp; trail.
		$output = rtrim($output, 'amp;');
		$output = rtrim($output, '&');
		
		return $output;
	}
}


/**
 * get site path without domain. works like Codeigniter's site_url().
 * @param type $uri
 * @return type
 */
function site_path($uri = '') {
	$site_url = site_url($uri);
	
	$domain = (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://').$_SERVER['HTTP_HOST'];
	
	return str_replace($domain, '', $site_url);
}// site_path


if (!function_exists('url_title')) {
	function url_title($str, $separator = 'dash', $lowercase = false) {
		if ($separator == 'dash') {
			$search = '_';
			$replace = '-';
		} else {
			$search = '-';
			$replace = '_';
		}
		
		$trans = array(
				'&\#\d+?;'				=> '',
				'&\S+?;'				=> '',
				'\s+'					=> $replace,
				'[^a-z0-9\-\._ก-๙]'		=> '',
				$replace.'+'			=> $replace,
				$replace.'$'			=> $replace,
				'^'.$replace			=> $replace,
				'\.+$'					=> ''
			);
		$str = strip_tags($str);
		
		foreach ($trans as $key => $val) {
			$str = preg_replace("#".$key."#ui", $val, $str);
		}
		
		if ($lowercase === true) {
			$str = mb_strtolower($str);
		}
		
		// remove unuse var
		unset($search, $replace, $trans);
		return trim(stripslashes($str));
	}// url_title
}


if (!function_exists('urlencode_except_slash')) {
	function urlencode_except_slash($url = '') {
		if ($url == null) {return null;}
		
		//
		$url_raw = explode('/', $url);
		if (!is_array($url_raw)) {return $url;}
		
		//
		$output = '';
		
		//
		foreach ($url_raw as $uri) {
			if (mb_strpos($uri, 'http:') !== false || mb_strpos($uri, 'https:') !== false || mb_strpos($uri, 'ftp:') !== false || mb_strpos($uri, ':') !== false) {
				// contain protocol (http://), do not encode this part
				$output .= $uri;
			} else {
				$output .= urlencode($uri);
			}
			//
			if ($uri != end($url_raw)) {
				$output .= '/';
			}
		}
		
		return $output;
	}// urlencode_except_slash
}