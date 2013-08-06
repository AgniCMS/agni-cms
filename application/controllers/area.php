<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class area extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// check admin login!!!
		if (! $this->account_model->isAdminLogin()) {
			redirect('site-admin/login?rdr='.urlencode(current_url()));
		}
		
		// load helper
		$this->load->helper(array('language'));
	}// __construct
	
	
	/**
	 * show area demo for theme area/blocks manager. (access by http://localhost/area/demo/theme_system_name)
	 * @param string $theme_system_name 
	 */
	public function demo($theme_system_name = '') 
	{
		if (!empty($theme_system_name)) {
			$this->theme_path = base_url().config_item('agni_theme_path').$theme_system_name.'/';// for use in css
			$this->theme_system_name = $theme_system_name;// for template file.
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title();
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/area/demo_view', $output);
	}// demo
	
	
	public function index() 
	{
		
	}// index
	
	
}

// EOF