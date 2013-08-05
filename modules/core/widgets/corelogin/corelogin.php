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

class corelogin extends widget 
{
	
	
	public $title;
	public $description;
	
	
	public function __construct() 
	{
		$this->lang->load('core/coremd');
		$this->title = $this->lang->line('coremd_login_title');
		$this->description = $this->lang->line('coremd_login_desc');
	}// __construct
	
	
	public function block_show_form($row = '') 
	{
		// this is method for show form edit in admin page.
		$values = unserialize($row->block_values);
		include dirname(__FILE__).'/views/form.php';
	}// block_show_form
	
	
	public function run() 
	{
		// load helper
		$this->load->helper('form');
		
		// get arguments
		$args = func_get_args();
		$values = (isset($args[1]) ? unserialize($args[1]) : '');
		
		include dirname(__FILE__).'/views/display.php';
	}// run
	
	
}
