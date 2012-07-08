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


if ( !function_exists( 'language_switch' ) ) {
	function language_switch() {
		$CI =& get_instance();
		if ( $CI->config->item( 'lang_ignore' ) ) {
			$user_lang = $_COOKIE[$CI->config->item( 'cookie_prefix' ).'user_lang'];
		} else {
			$user_lang = $CI->config->item( 'language_abbr' );
		}
		$langs = $CI->config->item( 'lang_uri_abbr' );
		//
		//$querystring = http_build_query( $_GET, '', '&amp;' );// because of the line below, we will not use any querystring. just change langauge and go to root web.
		$querystring = null;
		$output = $langs[$user_lang];
		$output .= '<ul class="lang-switch">';
		foreach ( $langs as $key => $item ) {
			if ( $key != $user_lang ) {
				//$switch_link = site_url( $key.$CI->uri->uri_string() );// this one will change language with current url (http://localhost/post/post-name to http://localhost/en/post/post-name) which cause 404 error in many pages.
				$switch_link = site_url( $key );
				if ( $CI->config->item( 'lang_ignore' ) == false ) {
					$switch_link = preg_replace( "/(.*)\/(\w{2})\/(\w{2})(\/.*|$)/", '$1/$3$4', $switch_link );
				}
				$output .= '<li class="language-item language-'.$item.' lang-'.$key.'">'.anchor( $switch_link.($querystring != null ? '?' : '').$querystring, $item ).'</li>';
			}
		}
		$output .= '</ul>';
		// clear unuse items
		unset( $user_lang, $langs, $switch_link, $key, $item, $CI );
		return $output;
	}// language_switch
}


if ( !function_exists( 'language_switch_admin' ) ) {
	function language_switch_admin() {
		$CI =& get_instance();
		if ( $CI->config->item( 'lang_ignore' ) ) {
			$user_lang = $_COOKIE[$CI->config->item( 'cookie_prefix' ).'user_lang'];
		} else {
			$user_lang = $CI->config->item( 'language_abbr' );
		}
		$langs = $CI->config->item( 'lang_uri_abbr' );
		//
		//$querystring = http_build_query( $_GET, '', '&amp;' );// because of the line below, we will not use any querystring. just change langauge and go to root web.
		$querystring = null;
		$output = $langs[$user_lang];
		$output .= '<ul class="lang-switch">';
		foreach ( $langs as $key => $item ) {
			if ( $key != $user_lang ) {
				//$switch_link = site_url( $key.$CI->uri->uri_string() );// this one will change language with current url (http://localhost/post/post-name to http://localhost/en/post/post-name) which cause 404 error in many pages.
				$switch_link = site_url( $key.'/'.$CI->uri->segment(1) );
				if ( $CI->config->item( 'lang_ignore' ) == false ) {
					$switch_link = preg_replace( "/(.*)\/(\w{2})\/(\w{2})(\/.*|$)/", '$1/$3$4', $switch_link );
				}
				$output .= '<li class="language-item language-'.$item.' lang-'.$key.'">'.anchor( $switch_link.($querystring != null ? '?' : '').$querystring, $item ).'</li>';
			}
		}
		$output .= '</ul>';
		// clear unuse items
		unset( $user_lang, $langs, $switch_link, $key, $item, $CI );
		return $output;
	}// language_switch_admin
}


function language_switch_select() {
	$CI =& get_instance();
	// get current language
	$CI->load->helper( 'cookie' );
	if ( get_cookie( 'agni_install_lang' ) != null ) {
		$user_lang = get_cookie( 'agni_install_lang' );
	} else {
		$user_lang = $CI->config->item( 'language_abbr' );
	}
	// list languages
	$langs = $CI->config->item( 'lang_uri_abbr' );
	$output = '<select name="languages" class="language-selector" onchange="window.location=$(this).val();">';
	foreach ( $langs as $key => $item ) {
		$switch_link = site_url( 'setlang?lang='.$key );
		$output .= '<option value="'.$switch_link.'"'.($key == $user_lang ? ' selected="selected"' : '' ).'>'.$item.'</option>';
	}
	$output .= '</select>';
	return $output;
}// language_switch_select


if ( !function_exists( 'url_title' ) ) {
	function url_title( $str, $separator = 'dash', $lowercase = false ) {
		if ( $separator == 'dash' ) {
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
		$str = strip_tags( $str );
		foreach ($trans as $key => $val) {
			$str = preg_replace("#".$key."#ui", $val, $str);
		}
		if ( $lowercase === true ) {
			$str = mb_strtolower( $str );
		}
		// remove unuse var
		unset( $search, $replace, $trans );
		return trim( stripslashes( $str ) );
	}// url_title
}


if ( !function_exists( 'urlencode_except_slash' ) ) {
	function urlencode_except_slash( $url = '' ) {
		if ( $url == null ) {return null;}
		//
		$url_raw = explode( '/', $url );
		if ( !is_array( $url_raw ) ) {return $url;}
		//
		$output = '';
		//
		foreach ( $url_raw as $uri ) {
			if ( mb_strpos( $uri, 'http:' ) !== false || mb_strpos( $uri, 'https:' ) !== false || mb_strpos( $uri, 'ftp:' ) !== false || mb_strpos( $uri, ':' ) !== false ) {
				// contain protocol (http://), do not encode this part
				$output .= $uri;
			} else {
				$output .= urlencode( $uri );
			}
			//
			if ( $uri != end( $url_raw ) ) {
				$output .= '/';
			}
		}
		return $output;
	}// urlencode_except_slash
}