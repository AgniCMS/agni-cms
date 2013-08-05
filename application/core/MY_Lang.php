<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class MY_Lang extends MX_Lang {
	
	
	function __construct() {
		parent::__construct();
		
		/**
		 * @name multi language by url
		 * @author wiredesignz
		 * @copyright     Copyright (c) 2011 Wiredesignz
		 * @version         0.29
		 * @link http://codeigniter.com/wiki/URI_Language_Identifier/
		 * @uses http://domain.tld/en/controller/method, http://domain.tld/es/controller/method
		 */
		global $URI, $CFG, $IN;
		$config = & $CFG->config;
		$index_page = $config['index_page'];
		$lang_ignore = $config['lang_ignore'];
		$default_abbr = $config['language_abbr'];
		$lang_uri_abbr = $config['lang_uri_abbr'];
		/* get the language abbreviation from uri */
		$uri_abbr = $URI->segment(1);
		/* adjust the uri string leading slash */
		$URI->uri_string = preg_replace("|^\/?|", '/', $URI->uri_string);
		if ($lang_ignore) {
			if (isset($lang_uri_abbr[$uri_abbr])) {
				/* set the language_abbreviation cookie */
				$IN->set_cookie('user_lang', $uri_abbr, $config['sess_expiration']);
			} else {
				/* get the language_abbreviation from cookie */
				$lang_abbr = $IN->cookie($config['cookie_prefix'] . 'user_lang');
			}
			if (strlen($uri_abbr) == 2) {
				/* reset the uri identifier */
				$index_page .= empty($index_page) ? '' : '/';
				/* remove the invalid abbreviation */
				$URI->uri_string = preg_replace("|^\/?$uri_abbr\/?|", '', $URI->uri_string);
				/* redirect */
				header('Location: ' . $config['base_url'] . $index_page . $URI->uri_string);
				exit;
			}
		} else {
			/* set the language abbreviation */
			$lang_abbr = $uri_abbr;
		}
		/* check validity against config array */
		if (isset($lang_uri_abbr[$lang_abbr])) {
			/**
			 * add if (!$lan_ignore) to fix $this->uri->segment(n);
			 * @author vee w.
			 */
			if (!$lang_ignore) {
				/* reset uri segments and uri string */
				$URI->_reindex_segments(array_shift($URI->segments));
			}
			$URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);
			/* set config language values to match the user language */
			$config['language'] = $lang_uri_abbr[$lang_abbr];
			$config['language_abbr'] = $lang_abbr;
			/* if abbreviation is not ignored */
			if (!$lang_ignore) {
				/**
				 * hide lang abbr in url if ignore default is true and lang abbr = default abbr
				 * @author vee w. 
				 */
				if ($config['lang_ignore_default'] == false || ($config['lang_ignore_default'] == true && $lang_abbr != $default_abbr)) {
					/* check and set the uri identifier */
					$index_page .= empty($index_page) ? $lang_abbr : "/$lang_abbr";
					/* reset the index_page value */
					$config['index_page'] = $index_page;
				}
			}
			/* set the language_abbreviation cookie */
			$IN->set_cookie('user_lang', $lang_abbr, $config['sess_expiration']);
		} else {
			/* if abbreviation is not ignored */
			if (!$lang_ignore) {
				/**
				 * hide lang abbr in url if ignore default is true and lang abbr = default abbr
				 * @author vee w. 
				 */
				if ($config['lang_ignore_default'] == false) {
					/* check and set the uri identifier to the default value */
					$index_page .= empty($index_page) ? $default_abbr : "/$default_abbr";
					if (strlen($lang_abbr) == 2) {
						/* remove invalid abbreviation */
						$URI->uri_string = preg_replace("|^\/?$lang_abbr|", '', $URI->uri_string);
					}
					/* redirect */
					header('Location: ' . $config['base_url'] . $index_page . $URI->uri_string);
					exit;
				}
			}
			/* set the language_abbreviation cookie */
			$IN->set_cookie('user_lang', $default_abbr, $config['sess_expiration']);
		}
		log_message('debug', "Language_Identifier Class Initialized");
	}// __construct
	
	
	/**
	 * get current language
	 * @author vee w.
	 * @global object $CFG
	 * @param boolean $return_abbr
	 * @return string 
	 */
	function get_current_lang($return_abbr = true) {
		global $CFG;
		$config = & $CFG->config;
		$lang_uri_abbr = $config['lang_uri_abbr'];
		if ($return_abbr) {
			return array_search($config['language'], $lang_uri_abbr);
		} else {
			return $config['language'];
		}
	}// get_current_lang
	
	
	/**
	 * line
	 * @param string $line
	 * @return string 
	 */
	function line($line = '') {
		$value = ($line == '' OR ! isset($this->language[$line])) ? FALSE : $this->language[$line];

		// Because killer robots like unicorns!
		if ($value === FALSE)
		{
			log_message('error', 'Could not find the language line "'.$line.'"');
			$value = $line;
		}

		return $value;
	}// line
	
	
	/**
	 * this copy from MX lang load.
	 * 
	 * modify: not show error when language file not found by use $this->ci_load instead of parent::load which call load method in CI_Lang.
	 */
	public function load($langfile, $lang = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '', $_module = '')	{
		
		if (is_array($langfile)) {
			foreach($langfile as $_lang) $this->load($_lang);
			return $this->language;
		}
			
		$deft_lang = CI::$APP->config->item('language');
		$idiom = ($lang == '') ? $deft_lang : $lang;
	
		if (in_array($langfile.'_lang'.EXT, $this->is_loaded, TRUE))
			return $this->language;

		$_module OR $_module = CI::$APP->router->fetch_module();
		list($path, $_langfile) = Modules::find($langfile.'_lang', $_module, 'language/'.$idiom.'/');

		if ($path === FALSE) {
			
			if ($lang = $this->ci_load($langfile, $lang, $return, $add_suffix, $alt_path)) return $lang;
		
		} else {

			if($lang = Modules::load_file($_langfile, $path, 'lang')) {
				if ($return) return $lang;
				$this->language = array_merge($this->language, $lang);
				$this->is_loaded[] = $langfile.'_lang'.EXT;
				unset($lang);
			}
		}
		
		return $this->language;
	}// load
	
	
	/**
	 * ci_load
	 * this method copy from CI_Lang.
	 * 
	 * modify: not show error when language file not found by comment 'show_error' line out.
	 */
	function ci_load($langfile = '', $idiom = '', $return = FALSE, $add_suffix = TRUE, $alt_path = '')
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
			$deft_lang = (! isset($config['language'])) ? 'english' : $config['language'];
			$idiom = ($deft_lang == '') ? 'english' : $deft_lang;
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
				//show_error('Unable to load the requested language file: language/'.$idiom.'/'.$langfile);
			}
		}


		if (! isset($lang))
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
	}// ci_load
	
	
}
