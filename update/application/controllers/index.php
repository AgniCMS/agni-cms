<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class index extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load
		$this->load->model(array());
		
		// load helper
		$this->load->helper(array('cookie', 'date', 'form'));
	}// __construct
	
	
	public function index() 
	{
		// get configured from files
		include_once('../application/config/database.php');
		
		// reformat config for manual connect db
		foreach ($db['default'] as $key => $item) {
			$db[$key] = $item;
		}
		
		// this step connected to db. if fail or wrong settings, it should throw error.
		$this->load->database($db);
		
		// get all available sites.
		$query = $this->db->get('sites');
		
		// loop each site
		foreach ($query->result() as $row) {
			if ($row->site_id == '1') {
				$db_site = '';
			} else {
				$db_site = $row->site_id . '_';
			}
			
			// update version to 1.3
			$this->db->where('config_name', 'agni_version')
					->set('config_value', '1.3')
					->update($this->db->dbprefix($db_site . 'config'));
			
			unset($db_site);
		}
		$query->free_result();
		unset($row, $query);
		
		// head tags output ##############################
		$output['page_title'] = $this->lang->line('agni_agnicms').' &gt; '.$this->lang->line('agni_update');
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('template/index_view', $output);
	}// index
	
	
}

// EOF