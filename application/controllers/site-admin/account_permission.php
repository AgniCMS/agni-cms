<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class account_permission extends admin_controller {

	
	function __construct() {
		parent::__construct();
		
		// load model
		$this->load->model(array('permission_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	function _define_permission() {
		return array('account_permission_perm' => array('account_permission_manage_perm'));
	}// _define_permission
	
	
	function index() {
		// check permission
		if ($this->account_model->check_admin_permission('account_permission_perm', 'account_permission_manage_perm') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		$output['list_permissions'] = $this->permission_model->fetch_permissions();
		$output['list_permissions_check'] = $this->permission_model->list_permissions_check();
		$output['list_level_group'] = $this->account_model->list_level_group(false);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_permission'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->generate_page('site-admin/templates/account/account_permission_view', $output);
		unset($output);
	}// index
	
	
	function module($module_system_name = '') {
		// check permission
		if ($this->account_model->check_admin_permission('account_permission_perm', 'account_permission_manage_perm') != true) {redirect('site-admin/modules');}
		
		// check if module really has permission
		if (!$this->permission_model->has_permission($module_system_name)) {redirect('site-admin/modules');}
		
		// load module model for get module data
		$this->load->model('modules_model');
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get module data -----------------------------------------------------------------------------------------------------------------
		$data['module_system_name'] = $module_system_name;
		$module = $this->modules_model->get_modules_data($data);
		unset($data);
		
		if ($module == null) {
			redirect('site-admin/modules');
		}
		
		$output['module'] = $module;
		// get module data -----------------------------------------------------------------------------------------------------------------
		
		$output['list_permissions'] = $this->permission_model->fetch_permissions_module($module_system_name);
		
		// get permission page from module's permission
		$permission_page = key($output['list_permissions']);
		
		$data['permission_page'] = $permission_page;
		$output['list_permissions_check'] = $this->permission_model->list_permissions_check($data);
		unset($data);
		
		$output['list_level_group'] = $this->account_model->list_level_group(false);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title(sprintf($this->lang->line('account_permission_module'), $module->module_name));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->generate_page('site-admin/templates/account/account_permission_module_view', $output);
		unset($output);
	}// module
	
	
	function reset() {
		// filter method post only.
		if (strtolower($this->input->server('REQUEST_METHOD')) != 'post') {redirect('site-admin/account-permission');}
		
		// check permission
		if ($this->account_model->check_admin_permission('account_permission_perm', 'account_permission_manage_perm') != true) {redirect('site-admin');}
		
		$this->permission_model->reset_permissions();
	}// reset
	
	
	function save() {
		// check permission
		if ($this->account_model->check_admin_permission('account_permission_perm', 'account_permission_manage_perm') != true) {redirect('site-admin');}
		
		// save action
		if ($this->input->post()) {
			
			// preset array post permissions.
			$data['level_group_id'] = $this->input->post('level_group_id');
			$data['permission_page'] = $this->input->post('permission_page');
			$data['permission_action'] = $this->input->post('permission_action');
			
			$this->permission_model->save_permissions($data);
		}
		
		// set success msg and send back
		$this->load->library('session');
		$this->session->set_flashdata(
			'form_status',
			array(
				'form_status' => 'success',
				'form_status_message' => $this->lang->line('admin_saved')
			)
		);
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/account-permission');
		}
	}// save
	

}

