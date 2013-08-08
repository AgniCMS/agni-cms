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

class module extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('modules_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('modules');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('modules_manage_perm' => array('modules_viewall_perm', 'modules_add_perm', 'modules_activate_deactivate_perm', 'modules_uninstall_perm', 'modules_delete_perm'));
	}// _define_permission
	
	
	public function activate($module_system_name = '', $site_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_activate_deactivate_perm') != true) {redirect('site-admin');}
		
		// do activate
		$result = $this->modules_model->doActivate($module_system_name, $site_id);
		
		// load session
		$this->load->library('session');
		if ($result === true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => $this->lang->line('modules_activated')
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('modules_activated_fail')
				)
			);
		}
		
		redirect('site-admin/module');
	}// activate
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_add_perm') != true) {redirect('site-admin');}
		
		// save action.
		if ($this->input->post()) {
			$result = $this->modules_model->addModule();
			
			if ($result === true) {
				// load session
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('modules_added')
					)
				);
				
				redirect('site-admin/module');
			} else {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $result;
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('modules_modules'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/modules/modules_add_view', $output);
	}// add
	
	
	public function deactivate($module_system_name = '', $site_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_activate_deactivate_perm') != true) {redirect('site-admin');}
		
		// do deactivate
		$result = $this->modules_model->doDeactivate($module_system_name, $site_id);
		
		// load session
		$this->load->library('session');
		if ($result === true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => $this->lang->line('modules_deactivated')
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('modules_deactivated_fail')
				)
			);
		}
		
		redirect('site-admin/module');
	}// deactivate
	
	
	public function delete() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_delete_perm') != true) {redirect('site-admin');}
		
		// get module sys name
		$module_system_name = trim($this->input->post('id'));
		$result = $this->modules_model->deleteAModule($module_system_name);
		
		// load session
		$this->load->library('session');
		if ($result === true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => $this->lang->line('modules_deleted')
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('modules_deleted_fail')
				)
			);
		}
		
		redirect('site-admin/module');
	}// delete
	
	
	public function index() 
	{
		// check permission 
		// special! to allow admin go to manage module's permission, we need to check at least 1 of these 2 permission.
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_viewall_perm') != true
			   && $this->account_model->checkAdminPermission('account_permission_perm', 'account_permission_manage_perm') != true) {redirect('site-admin');}
		
		// load permission model for check module has permission.
		$this->load->model('permission_model');
		
		// load session for show last flashed session
		$this->load->library('session');
		if ($this->input->is_ajax_request()) {
			$this->session->keep_flashdata('form_status');
		}
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list modules
		$output['list_item'] = $this->modules_model->listAllModules(array('list_for' => 'admin'));
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// list sites
		$this->load->model('siteman_model');
		$temp_get_orders = $this->input->get('orders');
		$_GET['orders'] = 'site_id';
		$output['sites'] = $this->siteman_model->listWebsitesAll();
		$_GET['orders'] = $temp_get_orders;
		unset($temp_get_orders);
		
		$output['current_site_id'] = $this->siteman_model->getSiteId();
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('modules_modules'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/modules/modules_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/module');}
		$act = trim($this->input->post('act'));
		$site_id = $this->siteman_model->getSiteId();
		
		// load library
		$this->load->library('session');
		
		if ($act == 'activate') {
			// check permission
			if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_activate_deactivate_perm') != true) {redirect('site-admin');}
			
			foreach ($id as $an_id) {
				$result = $this->modules_model->doActivate($an_id, $site_id);
				if ($result === false) {
					$fail_activate = true;
				}
			}
			
			if (isset($fail_activate) && $fail_activate == true) {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('modules_activated_fail_some')
					)
				);
			} else {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('modules_activated')
					)
				);
			}
			
			unset($fail_activate, $result);
		} elseif ($act == 'deactivate') {
			// check permission
			if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_activate_deactivate_perm') != true) {redirect('site-admin');}
			
			foreach ($id as $an_id) {
				$result = $this->modules_model->doDeactivate($an_id, $site_id);
				if ($result === false) {
					$fail_deactivate = true;
				}
			}
			
			if (isset($fail_activate) && $fail_activate == true) {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('modules_deactivated_fail_some')
					)
				);
			} else {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('modules_deactivated')
					)
				);
			}
			
			unset($fail_deactivate, $result);
		} elseif ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_delete_perm') != true) {redirect('site-admin');}
			
			$delete_fail = false;
			foreach ($id as $an_id) {
				$result = $this->modules_model->deleteAModule($an_id);
				if ($result === false) {
					$delete_fail = true;
				}
			}
			
			if ($delete_fail == true) {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('modules_delete_fail_some')
					)
				);
			} else {
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('modules_deleted')
					)
				);
			}
			
			unset($delete_fail, $result);
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/module');
		}
	}// process_bulk
	
	
	public function uninstall($module_system_name = '', $site_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('modules_manage_perm', 'modules_uninstall_perm') != true) {redirect('site-admin');}
		
		if (strtolower($this->input->server('REQUEST_METHOD')) != 'post') {
			redirect('site-admin');
		}
		
		// uninstall
		$result = $this->modules_model->doUninstall($module_system_name, $site_id);
		
		// load session
		$this->load->library('session');
		if ($result === true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => $this->lang->line('modules_uninstalled')
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('modules_uninstall_fail')
				)
			);
		}
		
		redirect('site-admin/module');
	}// uninstall
	

}

