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
 
class akismet_uninstall extends admin_controller 
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
		
		// delete config name -----------------------------------------------------------------------------------------------------------------
		$this->db->where('config_name', 'akismet_api');
		$this->db->delete($this->db->dbprefix($site_id . 'config'));
		// delete config name -----------------------------------------------------------------------------------------------------------------
		
		// disable module is the last step and required.
		$this->load->model('modules_model');
		$this->modules_model->doDeactivate($this->module_system_name, $this->input->get('site_id'));
		
		// done
		$this->load->library('session');
		$this->session->set_flashdata(
			'form_status',
			array(
				'form_status' => 'success',
				'form_status_message' => $this->lang->line('akismet_uninstall_completed')
			)
		);
		
		redirect('site-admin/module');
	}// index
	
	
}

// EOF