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

class themes extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('themes_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load lang
		$this->lang->load('themes');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('themes_manage_perm' => array('themes_viewall_perm', 'themes_add_perm', 'themes_enable_disable_perm', 'themes_set_default_perm', 'themes_delete_perm'));
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_add_perm') != true) {redirect('site-admin');}
		
		// save action.
		if ($this->input->post()) {
			$result = $this->themes_model->addTheme();
			
			if ($result === true) {
				// load session
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('themes_added')
					)
				);
				
				redirect('site-admin/themes');
			} else {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $result;
			}
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('themes_manager'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		$this->generate_page('site-admin/templates/themes/themes_add_view', $output);
	}// add
	
	
	public function defaultadmin() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_set_default_perm') != true) {redirect('site-admin');}
		
		$theme_system_name = trim($this->input->post('theme_system_name'));
		
		// set default
		$result = $this->themes_model->setDefaultTheme($theme_system_name, 'admin');
		
		// read theme data
		$pdata = $this->themes_model->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
		
		// load session
		$this->load->library('session');
		if ($result == true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => sprintf(lang('themes_default_done'), ($pdata['name'] != null ? $pdata['name'] : $theme_system_name))
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('themes_default_fail')
				)
			);
		}
		
		redirect('site-admin/themes');
	}// defaultadmin
	
	
	public function defaults($theme_system_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_set_default_perm') != true) {redirect('site-admin');}
		
		$result = $this->themes_model->setDefaultTheme($theme_system_name);
		
		// read theme data
		$pdata = $this->themes_model->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
		
		// load session
		$this->load->library('session');
		if ($result == true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => sprintf(lang('themes_default_done'), ($pdata['name'] != null ? $pdata['name'] : $theme_system_name))
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('themes_default_fail')
				)
			);
		}
		
		redirect('site-admin/themes');
	}// defaults
	
	
	public function delete($theme_system_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_delete_perm') != true) {redirect('site-admin');}
		
		// read theme data
		$pdata = $this->themes_model->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
		
		$output['theme_name'] = ($pdata['name'] != null ? $pdata['name'] : $theme_system_name);
		
		// list used theme in sites.
		$output['theme_use_in_site'] = $this->themes_model->listThemeUseInSites($theme_system_name);
		
		// delete action
		if ($this->input->post()) {
			if ($this->input->post('confirm') == 'yes') {
				$result = $this->themes_model->deleteTheme($theme_system_name);
				
				if ($result === true) {
					// load session
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => sprintf(lang('themes_deleted'), $output['theme_name'])
						)
					);
					
					redirect('site-admin/themes');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('themes_manager'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/themes/themes_del_view', $output);
	}// delete
	
	
	public function disable($theme_system_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_enable_disable_perm') != true) {redirect('site-admin');}
		
		$result = $this->themes_model->doDisableTheme($theme_system_name);
		
		$pdata = $this->themes_model->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
		
		// load session
		$this->load->library('session');
		if ($result == true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => sprintf(lang('themes_disabled'), ($pdata['name'] != null ? $pdata['name'] : $theme_system_name))
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('themes_disabled_fail')
				)
			);
		}
		
		redirect('site-admin/themes');
	}// disable
	
	
	public function enable($theme_system_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_enable_disable_perm') != true) {redirect('site-admin');}
		
		$result = $this->themes_model->doEnableTheme($theme_system_name);
		
		// read theme data
		$pdata = $this->themes_model->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
		
		// load session
		$this->load->library('session');
		if ($result == true) {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'success',
					'form_status_message' => sprintf(lang('themes_enabled'), ($pdata['name'] != null ? $pdata['name'] : $theme_system_name))
				)
			);
		} else {
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('themes_enabled_fail')
				)
			);
		}
		
		redirect('site-admin/themes');
	}// enable
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('themes_manage_perm', 'themes_viewall_perm') != true) {redirect('site-admin');}
		
		// load session for show last flashed session
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list enabled themes
		$output['list_enabled'] = $this->themes_model->listEnabledThemes();
		
		// list themes
		$output['list_item'] = $this->themes_model->listAllThemes();
		
		// default admin theme is...
		$output['theme_admin_name'] = '';
		$theme_system_name = $this->themes_model->getDefaultTheme('admin');
		if ($theme_system_name != null) {
			$output['theme_admin_name'] = $theme_system_name;
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('themes_manager'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/themes/themes_view', $output);
	}// index
	

}

