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

class corecategories extends widget 
{
	
	
	public $title;
	public $description;
	
	
	public function __construct() 
	{
		$this->lang->load('core/coremd');
		$this->title = $this->lang->line('coremd_category_title');
		$this->description = $this->lang->line('coremd_category_desc');
	}// __construct
	
	
	public function block_show_form($row = '') 
	{
		$values = unserialize($row->block_values);
		include dirname(__FILE__).'/views/form.php';
	}// block_show_form
	
	
	public function run() 
	{
		$this->load->model('taxonomy_model');
		$this->taxonomy_model->tax_type = 'category';
		$this->load->helper('category');
		// get arguments
		$args = func_get_args();
		$values = (isset($args[1]) ? unserialize($args[1]) : '');
		
		if (isset($values['block_title']) && $values['block_title'] != null) {
			echo '<h3>'.$values['block_title'].'</h3>';
		}
		
		include dirname(__FILE__).'/views/display.php';
		$nohome = false;
		
		if (isset($values['block_nohome']) && $values['block_nohome'] == '1') {
			$nohome = true;
		}
		
		echo show_category_nested_block($this->taxonomy_model->listTaxTerm(), $nohome);
	}// run
	
	
}
