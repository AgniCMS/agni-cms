<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class MY_Controller extends CI_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('language', 'url'));
		
		// load language
		$this->lang->load('agni');
	}// __construct
	
	
	/**
	 * generate page template+content
	 * @param string $page
	 * @param string $output 
	 */
	public function generate_page($page = '', $output = '') 
	{
		$output['page_content'] = $this->load->view($page, $output, true);
		$this->load->view('template', $output);
	}// generate_page
	
	
}

// EOF