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
 
class akismet_install extends admin_controller 
{
	
	
	public $module_system_name = 'akismet';
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function index() 
	{
		$site_id = trim($this->input->get('site_id'));
		if ($site_id == '1') {
			$site_id = '';
		} else {
			$site_id .= '_';
		}
		
		// install config name ------------------------------------------------------------------------------------------------------------------
		$this->db->where('config_name', 'akismet_api');
		$query = $this->db->get($this->db->dbprefix($site_id . 'config'));
		if ($query->num_rows() <= 0) {
			$this->db->set('config_name', 'akismet_api');
			$this->db->set('config_value', null);
			$this->db->set('config_description', 'Store akismet api key');
			$this->db->insert($this->db->dbprefix($site_id . 'config'));
		}
		$query->free_result();
		// install config name ------------------------------------------------------------------------------------------------------------------
		
		// done
		$this->load->library('session');
		$this->session->set_flashdata(
			'form_status',
			array(
				'form_status' => 'success',
				'form_status_message' => $this->lang->line('akismet_install_completed')
			)
		);
		
		// go back
		redirect('site-admin/module');
	}// index
	
	
}

// EOF