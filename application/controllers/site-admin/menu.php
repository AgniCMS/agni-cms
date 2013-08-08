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
 
class menu extends admin_controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('menu_model', 'posts_model', 'taxonomy_model'));
		
		// load helper
		$this->load->helper(array('category', 'form', 'menu'));
		
		// load language
		$this->lang->load('menu');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('menu_perm' => 
					array(
						'menu_viewall_group_perm', 
						'menu_add_group_perm', 
						'menu_edit_group_perm', 
						'menu_delete_group_perm', 
						'menu_viewall_menu_perm', 
						'menu_add_perm', 
						'menu_edit_perm', 
						'menu_delete_perm', 
						'menu_sort_perm' 
					) 
				);
	}// _define_permission
	
	
	public function addgroup() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_add_group_perm') != true) {redirect('site-admin');}
		
		$output['mg_name'] = '';
		$output['mg_description'] = '';
		
		// save action
		if ($this->input->post()) {
			$data['mg_name'] = htmlspecialchars(trim($this->input->post('mg_name')), ENT_QUOTES, config_item('charset'));
			$data['mg_description'] = htmlspecialchars(trim($this->input->post('mg_description')), ENT_QUOTES, config_item('charset'));
				if ($data['mg_description'] == null) {$data['mg_description'] = null;}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('mg_name', 'lang:menu_group_name', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->menu_model->addMenuGroup($data);
				
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
					
					redirect('site-admin/menu');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['mg_name'] = $data['mg_name'];
			$output['mg_description'] = $data['mg_description'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('menu_menu'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/menu/menu_aegroup_view', $output);
	}// addgroup
	
	
	public function ajax_additem() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_add_perm') != true) {exit;}
		
		if ($this->input->is_ajax_request()) {
			$data['mg_id'] = trim($this->input->post('mg_id'));
			$data['mi_type'] = trim($this->input->post('mi_type'));
			$data['type_id'] = $this->input->post('type_id');
				if (!is_array($data['type_id'])) {$data['type_id'] = trim($data['type_id']);}
				if ($data['type_id'] == null) {$data['type_id'] = null;}
			$data['link_url'] = strip_tags(trim($this->input->post('link_url')));
				if ($data['mi_type'] == 'link' && strpos($data['link_url'], 'www.') !== false) {$data['link_url'] = prep_url($data['link_url']);}
				if ($data['link_url'] == null) {$data['link_url'] = null;}
			$data['link_text'] = trim($this->input->post('link_text'));
				if ($data['link_text'] == null) {$data['link_text'] = null;}
			$data['custom_link'] = trim($this->input->post('custom_link'));
			
			// load form validation
			$this->load->library('form_validation');
			
			if ($data['mi_type'] == 'custom_link') {
				$this->form_validation->set_rules('custom_link', 'lang:menu_custom_link', 'trim|required');
			} elseif ($data['mi_type'] == 'link') {
				$this->form_validation->set_rules('link_text', 'lang:menu_link_text', 'trim|required');
				$this->form_validation->set_rules('link_url', 'lang:menu_link_url', 'trim|required');
			} else {
				// this rule is just for making form_validation to work. without a single rule it will not work.
				$this->form_validation->set_rules('link_text', 'lang:menu_link_text', 'trim');
			}
			
			if ($this->form_validation->run() == false) {
				$output['result'] = false;
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
				
				// log error in ajax help developer found the problem easier.
				log_message('error', $output['form_status_message']);
			} else {
				// 
				$result = $this->menu_model->addMenuItem($data);
				
				if ($result === true) {
					$output['result'] = true;
				} else {
					$output['result'] = false;
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// ajax_additem
	
	
	public function ajax_deleteitem() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_delete_perm') != true) {exit;}
		
		if ($this->input->is_ajax_request()) {
			$mi_id = $this->input->post('mi_id');
			
			if (!is_numeric($mi_id)) {exit;}
			
			$this->menu_model->deleteMenuItem($mi_id);
			$this->menu_model->reBuildMenu();
			
			//
			$output['result'] = true;
			
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// ajax_delete
	
	
	public function ajax_edititem($mi_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_edit_perm') != true) {exit;}
		
		if ($this->input->is_ajax_request()) {
			// get data for edit
			$data['mi_id'] = $mi_id;
			$row = $this->menu_model->getMiDataDb($data);
			unset($data);
			
			if ($row == null) {exit;}
			
			// store data for form
			$output['mi_id'] = $mi_id;
			$output['link_text'] = htmlspecialchars($row->link_text, ENT_QUOTES, config_item('charset'));
			$output['link_url'] = urldecode($row->link_url);
			$output['custom_link'] = htmlspecialchars($row->custom_link, ENT_QUOTES, config_item('charset'));
			
			// save action
			if ($this->input->post()) {
				
				// data for update table
				$data['mi_id'] = $mi_id;
				$data['link_url'] = strip_tags(trim($this->input->post('link_url')));
					if ($row->mi_type == 'link' && strpos($data['link_url'], 'www.') !== false) {$data['link_url'] = prep_url($data['link_url']);}
					if ($data['link_url'] == null) {$data['link_url'] = null;}
					// menu item type is not link, url encode it.
					if ($row->mi_type != 'link') {$data['link_url'] = urlencode($data['link_url']);}
				if ($row->mi_type == 'custom_link') {
					$data['custom_link'] = trim($this->input->post('custom_link'));
				} else {
					$data['link_text'] = trim($this->input->post('link_text'));
				}
				
				// load form validation
				$this->load->library('form_validation');
				
				// validate form
				if ($row->mi_type == 'custom_link') {
					$this->form_validation->set_rules('custom_link', 'lang:menu_custom_link', 'trim|required');
				} else {
					$this->form_validation->set_rules('link_text', 'lang:menu_link_text', 'trim|required');
					$this->form_validation->set_rules('link_url', 'lang:menu_link_url', 'trim|required');
				}
				
				if ($this->form_validation->run() == false) {
					echo '<div class="txt_error alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button><ul>'.validation_errors('<li>', '</li>').'</ul></div>';
					exit;
				} else {
					// update menu item
					$this->menu_model->editMenuItem($data);
					echo 'true';
					exit;
				}
			}
			
			// output
			if ($row->mi_type == 'custom_link') {
				$this->load->view('site-admin/templates/menu/menu_ajax_inlineedit_customlink', $output);
			} else {
				$this->load->view('site-admin/templates/menu/menu_ajax_inlineedit_link', $output);
			}
		}
	}// ajax_edititem
	
	
	public function ajax_searchpost($post_type = '') 
	{
		// load language
		$this->lang->load('post');
		
		// search value
		$_GET['q'] = trim($this->input->get('term'));
		
		// set post type in post_model
		$this->posts_model->post_type = $post_type;
		
		// list posts
		$list_posts = $this->posts_model->listPost('admin');
		
		$output = '';
		
		if (isset($list_posts['items']) && is_array($list_posts['items'])) {
			$i = 0;
			foreach ($list_posts['items'] as $row) {
				$output[$i]['id'] = $row->post_id;
				$output[$i]['value'] = $row->post_name;
				$output[$i]['status'] = ($row->post_status == '1' ? lang('post_published') : lang('post_draft'));
				$i++;
			}
		}
		
		// clear unused items
		unset($list_posts, $i, $row);
		
		// output
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_searchpost
	
	
	public function ajax_searchtag() 
	{
		// search value
		$_GET['q'] = trim($this->input->get('term'));
		
		// set taxterm type in taxonomy_model
		$this->taxonomy_model->tax_type = 'tag';
		
		// list tags
		$list_tags = $this->taxonomy_model->listTags(array('list_for' => 'admin'));
		
		$output = '';
		
		if (isset($list_tags['items']) && is_array($list_tags['items'])) {
			$i = 0;// important. can't use other number in array key. because jqueryui autocomplete count from 0 and +1 for each array
			foreach ($list_tags['items'] as $row) {
				$output[$i]['id'] = $row->tid;
				$output[$i]['value'] = $row->t_name;
				$i++;
			}
		}
		
		// clear unused items
		unset($list_tags, $i, $row);
		
		// output
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_searchtag
	
	
	public function ajax_sortitem($mg_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_sort_perm') != true) {exit;}
		
		if ($this->input->is_ajax_request()) {
			foreach ($this->input->post() as $key => $item) {
				if (is_array($item)) {
					$position = 1;
					
					foreach ($item as $key1 => $item1) {
						$item1 = str_replace(array('root', 'null'), '0', $item1);
						$this->db->set("parent_id", $item1);
						$this->db->set('position', $position);
						$this->db->where('mi_id', $key1);
						$this->db->where('mg_id', $mg_id);
						$this->db->update('menu_items');
						$position++;
					}
				}
			}
			
			unset($key, $key1, $item, $item1);
			
			$this->menu_model->reBuildMenu();
			
			echo '<div class="txt_success alert alert-success">'.$this->lang->line('admin_saved').'</div>';
		}
	}// ajax_sortitem
	
	
	public function editgroup($mg_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_edit_group_perm') != true) {redirect('site-admin');}
		
		if (!is_numeric($mg_id)) {redirect('site-admin/menu');}
		
		// open menu groups table for edit
		$data['mg_id'] = $mg_id;
		$data['language'] = $this->menu_model->language;
		$row = $this->menu_model->getMgDataDb($data);
		unset($data);
		
		// not found
		if ($row == null) {redirect('site-admin/menu');}
		
		// store data for form
		$output['mg_name'] = $row->mg_name;
		$output['mg_description'] = $row->mg_description;
		$output['row'] = $row;
		
		// save action
		if ($this->input->post()) {
			$data['mg_id'] = $mg_id;
			$data['mg_name'] = htmlspecialchars(trim($this->input->post('mg_name')), ENT_QUOTES, config_item('charset'));
			$data['mg_description'] = htmlspecialchars(trim($this->input->post('mg_description')), ENT_QUOTES, config_item('charset'));
				if ($data['mg_description'] == null) {$data['mg_description'] = null;}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('mg_name', 'lang:menu_group_name', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->menu_model->editMenuGroup($data);
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
					redirect('site-admin/menu');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['mg_name'] = $data['mg_name'];
			$output['mg_description'] = $data['mg_description'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('menu_menu'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/menu/menu_aegroup_view', $output);
	}// editgroup
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_viewall_group_perm') != true) {redirect('site-admin');}
		
		// orders, sort for link
		$output['orders'] = strip_tags(trim($this->input->get('orders')));
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'asc' ? 'desc' : 'asc');
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list menu group
		$output['list_group'] = $this->menu_model->listMenuGroup(true, array('list_for' => 'admin'));
		if (is_array($output['list_group'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('menu_menu'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/menu/menu_allgroup_view', $output);
	}// index
	
	
	public function item($mg_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('menu_perm', 'menu_viewall_menu_perm') != true) {redirect('site-admin');}
		
		if (!is_numeric($mg_id)) {redirect('site-admin/menu');}
		
		$output['mg_id'] = $mg_id;
		
		// query menu_groups for display info.
		$data['mg_id'] = $mg_id;
		$data['language'] = $this->lang->get_current_lang();
		$row = $this->menu_model->getMgDataDb($data);
		unset($data);
		
		if ($row == null) {redirect('site-admin/menu');}
		
		$output['mg'] = $row;
		
		// categories for add
		$this->taxonomy_model->tax_type = 'category';
		$output['list_category'] = $this->taxonomy_model->listTaxTerm();
		
		// pages for add
		$this->posts_model->post_type = 'page';
		$output['list_page'] = $this->posts_model->listPost('admin');
		if (is_array($output['list_page'])) {
			$output['page_pagination'] = $this->pagination->create_links();
		}
		
		// list menu_items
		$output['list_item'] = $this->menu_model->listMenuItem($mg_id);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('menu_menu'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->generate_page('site-admin/templates/menu/menu_allitem_view', $output);
	}// item
	
	
	public function process_group() 
	{
		$id = $this->input->post('id');
		$act = trim($this->input->post('act'));
		
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('menu_perm', 'menu_delete_group_perm') != true) {redirect('site-admin');}
			if (is_array($id)) {
				foreach ($id as $an_id) {
					$this->menu_model->deleteMenuGroup($an_id);
				}
			}
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/account');
		}
	}// process_group
	
	
}

// EOF