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

class account_level extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('account_lv_perm' => array('account_lv_manage_perm', 'account_lv_add_perm', 'account_lv_edit_perm', 'account_lv_delete_perm', 'account_lv_sort_perm'));
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('account_lv_perm', 'account_lv_add_perm') != true) {redirect('site-admin');}
		
		// save action
		if ($this->input->post()) {
			$data['level_name'] = strip_tags(trim($this->input->post('level_name', true)));
			$data['level_description'] = htmlspecialchars(trim($this->input->post('level_description')), ENT_QUOTES, config_item('charset'));
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('level_name', 'lang:account_level', 'trim|strip_tags|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->account_model->addLevelGroup($data);
				
				if ($result === true) {
					// load session library
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					redirect('site-admin/account-level');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['level_name'] = $data['level_name'];
			$output['level_description'] = $data['level_description'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_level'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/account/account_level_ae_view', $output);
	}// add
	
	
	public function ajaxsort() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('account_lv_perm', 'account_lv_sort_perm') != true) {redirect('site-admin');}
		
		// check ajax request
		if (!$this->input->is_ajax_request()) {redirect('site-admin');}
		
		// sort items
		$listItem = $this->input->post('listItem');
		if (is_array($listItem)) {
			$priority = 3;// start at 3 because 1 is super admin and 2 is admin.
			foreach ($listItem as $position => $item) {
				if (is_numeric($item) && $item != '1' && $item != '2' && $item != '3' && $item != '4') {
					$this->db->set('level_priority', $priority);
					$this->db->where('level_group_id', $item);
					$this->db->update('account_level_group');
				}
				$priority++;
			}
		}
		
		// delete cache
		$this->config_model->deleteCache('alg_');
		
		// done
		$output['form_status'] = 'success';
		$output['form_status_message'] = $this->lang->line('admin_saved');
		
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajaxsort
	
	
	public function edit($level_group_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('account_lv_perm', 'account_lv_edit_perm') != true) {redirect('site-admin');}
		
		// no level_group_id? get out.
		if (!is_numeric($level_group_id)) {redirect('site-admin/account-level');}
		
		// load data for form
		$row = $this->account_model->getAccountLevelGroupData(array('level_group_id' => $level_group_id));
		if ($row != null) {
			$output['level_name'] = $row->level_name;
			$output['level_description'] = $row->level_description;
		} else {
			redirect('site-admin');
		}
		
		// save action
		if ($this->input->post()) {
			$data['level_group_id'] = $level_group_id;
			$data['level_name'] = strip_tags(trim($this->input->post('level_name', true)));
			$data['level_description'] = htmlspecialchars(trim($this->input->post('level_description')), ENT_QUOTES, config_item('charset'));
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('level_name', 'lang:account_level', 'trim|strip_tags|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->account_model->editLevelGroup($data);
				
				if ($result === true) {
					// load session library
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					redirect('site-admin/account-level');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['level_name'] = $data['level_name'];
			$output['level_description'] = $data['level_description'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_level'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/account/account_level_ae_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('account_lv_perm', 'account_lv_manage_perm') != true) {redirect('site-admin');}
		
		// list item
		$output['list_item'] = $this->account_model->listLevelGroup(false);
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_level'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/account/account_level_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		$id = $this->input->post('id');
		$act = trim($this->input->post('act'));
		
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('account_lv_perm', 'account_lv_delete_perm') != true) {redirect('site-admin');}
			
			if (is_array($id)) {
				foreach ($id as $an_id) {
					$this->account_model->deleteLevelGroup($an_id);
				}
			}
		}
		
		// go back
		redirect('site-admin/account-level');
	}// process_bulk
	

}

