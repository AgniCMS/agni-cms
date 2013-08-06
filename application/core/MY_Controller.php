<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/* load mx_controller class */
require APPPATH."third_party/MX/Controller.php";

class MY_Controller extends MX_Controller {
	
	
	public $base_url;
	public $modules_path;
	public $plugins_path;
	public $theme_path;
	
	
	public $theme_system_name;

	
	function __construct() {
		parent::__construct();
		
		//if (!$this->input->is_ajax_request())
		//	$this->output->enable_profiler(TRUE);
		
		// load config
		$this->config->load('agni');
		
		// set paths
		$this->base_url = config_item('base_url');
		$this->modules_path = $this->base_url.config_item('modules_uri');
		$this->plugins_path = $this->base_url.config_item('agni_plugins_path');
		
		// get default theme name
		$this->load->model('themes_model');
		$theme_system_name = $this->themes_model->getDefaultTheme();
		
		// set theme path and theme name.
		$this->theme_path = $this->base_url.config_item('agni_theme_path').$theme_system_name.'/';
		$this->theme_system_name = $theme_system_name;
		unset($theme_system_name);
		
		// load model
		$this->load->model(array('account_model', 'blocks_model', 'config_model', 'html_model'));
		
		// load helper
		$this->load->helper(array('block'));
		
		// module plug working at start point.
		$this->modules_plug->do_action('front_core_controller_constructor', $this);
	}// __construct
	
	
	/**
	 * generate page template+content
	 * @param string $page
	 * @param string $output 
	 */
	function generate_page($page = '', $output = '', $theme = '') {
		// re-set theme system name (some content use different theme.)
		$this->themes_model->theme_system_name = $this->theme_system_name;
		
		//
		$output['page_content'] = $this->load->view($page, $output, true, $theme);
		$this->load->view('front/template', $output, false, $theme);
	}// generate_page
	

}

