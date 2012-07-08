<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class MY_Lang extends CI_Lang {
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	// --------------------------------------------------------------------

	/**
	 * Load a language file
	 *
	 * @access	public
	 * @param	mixed	the name of the language file to be loaded. Can be an array
	 * @param	string	the language (english, etc.)
	 * @param	bool	return loaded array of translations
	 * @param 	bool	add suffix to $langfile
	 * @param 	string	alternative path to look for language file
	 * @return	mixed
	 */
	function load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
	{
		$langfile = str_replace('.php', '', $langfile);

		if ($add_suffix == TRUE)
		{
			$langfile = str_replace('_lang.', '', $langfile).'_lang';
		}

		$langfile .= '.php';

		if (in_array($langfile, $this->is_loaded, TRUE))
		{
			return;
		}

		$config =& get_config();

		if ($idiom == '')
		{
			// agnicms installation multilanguage detect
			$ci =& get_instance();
			$ci->load->helper( 'cookie' );
			if ( get_cookie( 'agni_install_lang' ) != null ) {
				$deft_lang = get_cookie( 'agni_install_lang' );
				$langs = $ci->config->item( 'lang_uri_abbr' );
				if ( isset( $langs[$deft_lang] ) ) {
					$idiom = $langs[$deft_lang];
				}
			}
			// check again if above line fail.
			if ( $idiom == '' ) {
				$deft_lang = ( ! isset($config['language'])) ? 'english' : $config['language'];
				$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
			}
		}

		// Determine where the language file is and load it
		if ($alt_path != '' && file_exists($alt_path.'language/'.$idiom.'/'.$langfile))
		{
			include($alt_path.'language/'.$idiom.'/'.$langfile);
		}
		else
		{
			$found = FALSE;

			foreach (get_instance()->load->get_package_paths(TRUE) as $package_path)
			{
				if (file_exists($package_path.'language/'.$idiom.'/'.$langfile))
				{
					include($package_path.'language/'.$idiom.'/'.$langfile);
					$found = TRUE;
					break;
				}
			}

			if ($found !== TRUE)
			{
				show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
			}
		}


		if ( ! isset($lang))
		{
			log_message('error', 'Language file contains no data: language/'.$idiom.'/'.$langfile);
			return;
		}

		if ($return == TRUE)
		{
			return $lang;
		}

		$this->is_loaded[] = $langfile;
		$this->language = array_merge($this->language, $lang);
		unset($lang);

		log_message('debug', 'Language file loaded: language/'.$idiom.'/'.$langfile);
		return TRUE;
	}
	
	
	/**
	 * agni install get current language
	 * @return string 
	 */
	function get_current_lang( $return_full = true ) {
		$ci =& get_instance();
		$ci->load->helper( 'cookie' );
		if ( get_cookie( 'agni_install_lang' ) != null ) {
			$deft_lang = get_cookie( 'agni_install_lang' );
			$langs = $ci->config->item( 'lang_uri_abbr' );
			if ( isset( $langs[$deft_lang] ) ) {
				if ( $return_full === true ) {
					return $langs[$deft_lang];
				} else {
					return $deft_lang;
				}
			}
		}
		// no language set
		$config =& get_config();
		$deft_lang = ( ! isset( $config['language'] ) ) ? 'english' : $config['language'];
		$abbr = ( !isset( $config['language_abbr'] ) ) ? 'en' : $config['language_abbr'];
		$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
		if ( $return_full === true ) {
			return $idiom;
		} else {
			return $abbr;
		}
	}// get_current_lang
	
	
}

// EOF