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
		if ( file_exists( APPPATH.'core/'.config_item( 'subclass_prefix' ).'DB.php' ) )
		{
			require_once( APPPATH.'core/'.config_item('subclass_prefix').'DB.php' );
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
	}
	
	
	/**
	 * load views
	 * @param string $view
	 * @param array $vars
	 * @param boolean $return
	 * @param string $use_theme
	 * @return mixed
	 */
	public function view($view, $vars = array(), $return = FALSE, $use_theme = '') {
		$this->config->load( 'agni' );
		$view_path = config_item( 'agni_theme_path' );
		if ( $use_theme == null )
			$use_theme = $this->theme_system_name;// ดึงจาก MY_Controller, admin_controller .
		$default_theme = 'system';// ห้ามแก้.
		
		$this->_ci_view_paths = array($view_path => TRUE);
		$ci_view = $view;
			
		if ( file_exists( $view_path.$use_theme.'/'.$view.'.php' ) ) {
			// มองหาใน public/themes/theme_name/view_name.php แล้วเจอ
			$ci_view = $use_theme.'/'.$view;
		} elseif ( file_exists( $view_path.$default_theme.'/'.$view.'.php' ) ) {
			// มองหาใน public/themes/system/view_name.php แล้วเจอ
			$this->_ci_view_paths = array($view_path.$default_theme.'/' => TRUE);
			$ci_view = $view;
		} elseif ( file_exists( $view_path.$use_theme.'/modules/'.$this->_module.'/'.$view.'.php' ) ) {
			// มองหาใน public/themes/theme_name/modules/module_name/view_name.php แล้วเจอ
			$this->_ci_view_paths = array( $view_path.$use_theme.'/modules/'.$this->_module.'/' => TRUE );
			$ci_view = $view;
		} else {
			// มองหาใน modules แล้วใช้จากตรงนั้นแทน
			list( $path, $view ) = Modules::find( $view, $this->_module, 'views/' );
			$this->_ci_view_paths = array( $path => TRUE ) + $this->_ci_view_paths;
			$ci_view = $view;
		}
		
		unset( $view_path, $use_theme, $default_theme );
		return $this->_ci_load(array('_ci_view' => $ci_view, '_ci_vars' => $this->_ci_object_to_array($vars), '_ci_return' => $return));
	}// view
	
	
}