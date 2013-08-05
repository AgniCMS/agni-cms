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

class blog extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('blog_model'));
		
		// load helper
		$this->load->helper(array('language'));
	}
	
	
	public function index() 
	{
		// use different theme
		$this->theme_path = base_url().config_item('agni_theme_path').'quick-start/';// for use in css
		$this->theme_system_name = 'quick-start';
		
		// list posts
		$output['list_item'] = $this->blog_model->list_item();
		if (is_array($output['list_item'])) {
			$this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('blog_blog'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('blog_view', $output);
	}// index
	

}

