<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

/* load the MX_Loader class */
require APPPATH."third_party/MX/Loader.php";

class MY_Loader extends MX_Loader {
	
	
	protected $_module;
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
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
		} elseif ( file_exists( $view_path.$use_theme.'/'.$this->_module.'/'.$view.'.php' ) ) {
			// มองหาใน public/themes/theme_name/module_name/view_name.php แล้วเจอ
			$this->_ci_view_paths = array( $view_path.$use_theme.'/'.$this->_module.'/' => TRUE );
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