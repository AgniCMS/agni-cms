<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
	
	
	protected $_module;
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * Database Loader
	 * 
	 * load custom db modified by vee w.
	 * @link http://mineth.net/blog/extending-codeigniter-active-record-the-non-hacky-way/ add supported extend activerecord
	 *
	 * @param	string	the DB credentials
	 * @param	bool	whether to return the DB object
	 * @param	bool	whether to enable active record (this allows us to override the config setting)
	 * @return	object
	 */
	public function database($params = '', $return = FALSE, $active_record = NULL)
	{
		// Grab the super object
		$CI =& get_instance();

		// Do we even need to load the database class?
		if (class_exists('CI_DB') AND $return == FALSE AND $active_record == NULL AND isset($CI->db) AND is_object($CI->db))
		{
			return FALSE;
		}

		// Check if "custom DB file" exists, else include core one
		if (file_exists(APPPATH.'core/'.config_item('subclass_prefix').'DB.php'))
		{
			require_once(APPPATH.'core/'.config_item('subclass_prefix').'DB.php');
		}
		else
		{
			// original CI require DB.php
			require_once(BASEPATH.'database/DB.php');
		}
		

		if ($return === TRUE)
		{
			return DB($params, $active_record);
		}

		// Initialize the db variable.  Needed to prevent
		// reference errors with some configurations
		$CI->db = '';

		// Load the DB class
		$CI->db =& DB($params, $active_record);
		
		// set site table prefix (Agni cms multi site)
		set_site_table();
	}
	
	
	/**
	 * load views
	 * @param string $view
	 * @param array $vars
	 * @param boolean $return
	 * @param string $use_theme
	 * @return mixed
	 */
	public function view($view, $vars = array(), $return = false, $use_theme = '') {
		$this->config->load('agni');
		
		// set theme path
		$view_path = config_item('agni_theme_path');
		
		// if use theme variable is null
		if ($use_theme == null) {
			$use_theme = $this->theme_system_name;// get theme system name from MY_Controller, admin_controller .
		}
		
		$default_theme = 'system';// no change.
		
		$this->_ci_view_paths = array($view_path => true);
		$ci_view = $view;
		
		// load library
		$this->load->library('user_agent');
			
		if (file_exists($view_path.$use_theme.'/'.$view.'.php')) {
			// found in public/themes/theme_name/view_name.php
			$ci_view = $use_theme.'/'.$view;
		} elseif (file_exists($view_path.$default_theme.'/'.$view.'.php')) {
			// found in public/themes/system/view_name.php
			$this->_ci_view_paths = array($view_path.$default_theme.'/' => true);
			$ci_view = $view;
		} elseif (file_exists($view_path.$use_theme.'/modules/'.$this->_module.'/'.$view.'.php')) {
			// found in public/themes/theme_name/modules/module_name/view_name.php
			$this->_ci_view_paths = array($view_path.$use_theme.'/modules/'.$this->_module.'/' => true);
			$ci_view = $view;
		} else {
			// found in modules
			list($path, $view) = Modules::find($view, $this->_module, 'views/');
			$this->_ci_view_paths = array($path => true) + $this->_ci_view_paths;
			$ci_view = $view;
		}
		
		// add mobile theme support -------------------------------------------------------------------------------------------------------
		if ($this->agent->is_mobile() === true) {
			if (file_exists($view_path.$use_theme.'/mobile/'.$view.'.php')) {
				// found in public/themes/theme_name/mobile/view_name.php
				$ci_view = $use_theme.'/mobile/'.$view;
			} elseif (file_exists($view_path.$use_theme.'/mobile/modules/'.$this->_module.'/'.$view.'.php')) {
				// found in public/themes/theme_name/mobile/modules/module_name/view_name.php
				$this->_ci_view_paths = array($view_path.$use_theme.'/mobile/modules/'.$this->_module.'/' => true);
				$ci_view = $view;
			}
		}
		// end add mobile theme support -------------------------------------------------------------------------------------------------
		
		unset($view_path, $use_theme, $default_theme);
		
		return $this->_ci_load(array('_ci_view' => $ci_view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}// view
	
	
}