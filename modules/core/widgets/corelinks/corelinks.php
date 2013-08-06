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

class corelinks extends widget 
{
	
	
	public $title;
	public $description;
	
	
	public function __construct() 
	{
		$this->lang->load('core/coremd');
		$this->title = $this->lang->line('coremd_link_title');
		$this->description = $this->lang->line('coremd_link_desc');
	}// __construct
	
	
	public function block_show_form($row = '') 
	{
		$values = unserialize($row->block_values);
		include dirname(__FILE__).'/views/form.php';
	}// block_show_form
	
	
	public function run() 
	{
		// load helper
		$this->load->helper('menu');
		// get arguments
		$args = func_get_args();
		$values = (isset($args[1]) ? unserialize($args[1]) : '');
		
		// block title
		if (isset($values['block_title']) && $values['block_title'] != null) {
			echo '<h3>'.$values['block_title'].'</h3>';
		}
		
		if (isset($values['mg_id'])) {
			$this->load->model('menu_model');
			$list_item = $this->menu_model->listMenuItem($values['mg_id']);
			echo show_menuitem_nested($list_item);
		}
	}// run
	
	
}
