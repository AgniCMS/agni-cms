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
 
class block extends admin_controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('themes_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('block');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('block_perm' => array('block_viewall_perm', 'block_add_perm', 'block_edit_perm', 'block_delete_perm', 'block_sort_perm'));
	}// _define_permission
	
	
	public function ajax_add($theme_system_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_add_perm') != true) {return null;}
		
		if ($this->input->is_ajax_request() && !empty($theme_system_name)) {
			
			// set data for insert to db.
			$data['theme_system_name'] = $theme_system_name;
			$data['area_name'] = trim($this->input->post('area_name'));
			$block = trim($this->input->post('block_name'));
			$block = explode('[::]', $block);
			if (count($block) < 2) {return false;}
			$data['block_name'] = $block[0];
			$data['block_file'] = $block[1];
			$data['block_status'] = '1';
			
			// inserting data.
			$result = $this->blocks_model->addToArea($data);
			if (isset($result['result']) && $result['result'] == true) {
				$output['form_status'] = '';
				$output['form_status_message'] = '';
				$output['result'] = true;
				$output['block_id'] = $result['id'];
			} else {
				$output['result'] = false;
				$output['form_status'] = 'error';
				$output['form_status_message'] = $result;
			}
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			
		}
	}// ajax_add
	
	
	public function ajax_change_status($block_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_edit_perm') != true) {return null;}
		
		if ($this->input->is_ajax_request() && $block_id != null) {
			
			// set data for insert to db.
			$data['block_id'] = $block_id;
			$data['block_status'] = trim($this->input->post('block_status'));
				if ($data['block_status'] != '1') {$data['block_status'] = '0';}
			
			// inserting data
			$this->blocks_model->edit($data);
			$output['result'] = true;
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			
		}
		
	}// ajax_change_status
	
	
	public function ajax_delete($block_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_delete_perm') != true) {return null;}
		
		if ($this->input->is_ajax_request() && $block_id != null) {
			
			// delete block
			$this->db->where('block_id', $block_id)->delete('blocks');
			$output['result'] = true;
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			
		}
	}// ajax_delete
	
	
	public function ajax_load_area($theme_system_name = '', $area_name = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_viewall_perm') != true) {return null;}
		
		if ($this->input->is_ajax_request() && !empty($theme_system_name)) {
			
			$list_block_in_area = $this->blocks_model->listBlocksInAreas($theme_system_name);
			$output = null;
			if (isset($list_block_in_area[$area_name])) {
				foreach($list_block_in_area[$area_name] as $block) {
					// set block variable for views
					$data['block'] = $block;
					
					// load views.
					$output .= $this->load->view('site-admin/templates/block/block_each', $data);
				}
			}
			
			// done.
			return $output;
			
		}
	}// ajax_load_area
	
	
	public function ajax_sort() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_sort_perm') != true) {return null;}
		
		if ($this->input->is_ajax_request()) {
			
			$listitem = $this->input->get('listitem');
			
			if (is_array($listitem)) {
				
				$i = 1;
				
				foreach ($listitem as $key => $item) {
					$this->db->where('block_id', $item);
					$this->db->set('position', $i);
					$this->db->update('blocks');
					$i++;
				}
				
				echo '<div class="txt_success alert alert-success">'.lang('admin_saved').'</div>';
			}
			
		}
	}// ajax_sort
	
	
	public function edit($block_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_edit_perm') != true) {redirect('site-admin');}
		
		// load helper
		$this->load->helper(array('widget'));
		
		// load data for edit
		$data['block_id'] = $block_id;
		$data['language'] = $this->lang->get_current_lang();
		$block = $this->blocks_model->getBlockDb($data);
		
		// if selected block is not exists.
		if ($block == null) {$query->free_result(); redirect('site-admin/block');}
		
		$row = $block;
		unset($blocl);
		
		$output['row'] = $row;
		$output['block_values'] = unserialize($row->block_values);
		$output['block_status'] = $row->block_status;
		$output['block_except_uri'] = $row->block_except_uri;
		$output['block_only_uri'] = $row->block_only_uri;
		
		// save action
		if ($this->input->post()) {
			
			// store data for update to db
			$data['block_id'] = $block_id;
			$data['block_status'] = $this->input->post('block_status');
				if ($data['block_status'] != '1') {$data['block_status'] = '0';}
			$data['block_except_uri'] = strip_tags($this->input->post('block_except_uri'));
			$data['block_except_uri'] = str_replace(array("\r\n", "\r"), "\n", $data['block_except_uri']);
				if ($data['block_except_uri'] == null) {$data['block_except_uri'] = null;}
			$data['block_only_uri'] = strip_tags($this->input->post('block_only_uri'));
			$data['block_only_uri'] = str_replace(array("\r\n", "\r"), "\n", $data['block_only_uri']);
				if ($data['block_only_uri'] == null) {$data['block_only_uri'] = null;}
				
			// updating to db.
			$result = $this->blocks_model->edit($data);
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
				
				// done, redirect to manage block page.
				redirect('site-admin/block?theme_system_name='.$row->theme_system_name);
			} else {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $result;
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('block_blocks'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/block/block_edit_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('block_perm', 'block_viewall_perm') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list enabled themes
		$output['list_themes'] = $this->themes_model->listEnabledThemes();
		
		// set current selected theme (from select box or whatever.) value.
		$output['current_selected_theme'] = strip_tags(trim($this->input->get('theme_system_name', true)));
		
		// theme selected, list areas and blocks.
		if ($output['current_selected_theme'] != null) {
			
			// list areas
			$output['list_areas'] = $this->themes_model->listAreas($output['current_selected_theme']);
			
			// list current block in areas
			$output['list_block_in_area'] = $this->blocks_model->listBlocksInAreas($output['current_selected_theme']);
			
		}
		
		// list available widgets or blocks
		$output['list_available_blocks'] = $this->modules_model->listAllBlocks();
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('block_blocks'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->generate_page('site-admin/templates/block/block_view', $output);
	}// index
	
	
}

// EOF