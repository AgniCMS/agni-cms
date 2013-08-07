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

class category extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('taxonomy_model'));
		
		// load helper
		$this->load->helper(array('category', 'form'));
		
		// load language
		$this->lang->load('category');
		
		// set taxonomy type
		$this->taxonomy_model->tax_type = 'category';
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('category_perm' => array('category_viewall_perm', 'category_add_perm', 'category_edit_perm', 'category_delete_perm', 'category_sort_perm'));
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('category_perm', 'category_add_perm') != true) {redirect('site-admin');}
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
		// list categories for select parent
		$output['list_item'] = $this->taxonomy_model->listTaxTerm();
		
		// save action
		if ($this->input->post()) {
			
			// store data for insert to db.
			$data['parent_id'] = $this->input->post('parent_id');
			$data['t_name'] = htmlspecialchars(trim($this->input->post('t_name')), ENT_QUOTES, config_item('charset'));
			$data['t_description'] = trim($this->input->post('t_description'));
				$data['t_description'] = ($data['t_description'] == null ? null : $data['t_description']);
			$data['t_uri'] = trim($this->input->post('t_uri'));
			$data['meta_title'] = htmlspecialchars(trim($this->input->post('meta_title')), ENT_QUOTES, config_item('charset'));
				$data['meta_title'] = ($data['meta_title'] == null ? null : $data['meta_title']);
			$data['meta_description'] = htmlspecialchars(trim($this->input->post('meta_description')), ENT_QUOTES, config_item('charset'));
				$data['meta_description'] = ($data['meta_description'] == null ? null : $data['meta_description']);
			$data['meta_keywords'] = htmlspecialchars(trim($this->input->post('meta_keywords')), ENT_QUOTES, config_item('charset'));
				$data['meta_keywords'] = ($data['meta_keywords'] == null ? null : $data['meta_keywords']);
			$data['theme_system_name'] = trim($this->input->post('theme_system_name'));
				$data['theme_system_name'] = ($data['theme_system_name'] == null ? null : $data['theme_system_name']);
				
			// load form_validation class
			$this->load->library('form_validation');
			
			// validate form
			$this->form_validation->set_rules("t_name", "lang:category_name", "trim|required");
			$this->form_validation->set_rules("t_uri", "lang:admin_uri", "trim|min_length[3]|required");
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->taxonomy_model->add($data);
				
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
					redirect('site-admin/category');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['parent_id'] = $data['parent_id'];
			$output['t_name'] = $data['t_name'];
			$output['t_description'] = htmlspecialchars($data['t_description'], ENT_QUOTES, config_item('charset'));
			$output['t_uri'] = $data['t_uri'];
			$output['meta_title'] = $data['meta_title'];
			$output['meta_description'] = $data['meta_description'];
			$output['meta_keywords'] = $data['meta_keywords'];
			$output['theme_system_name'] = $data['theme_system_name'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('category_category'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/category/category_ae_view', $output);
	}// add
	
	
	public function ajax_nameuri() 
	{
		if ($this->input->post() && $this->input->is_ajax_request()) {
			
			$t_name = trim($this->input->post('t_name'));
			$nodupedit = trim($this->input->post('nodupedit'));
			$nodupedit = ($nodupedit == 'true' ? true : false);
			$id = intval($this->input->post('id'));
			
			$output['t_uri'] = $this->taxonomy_model->noDupTaxonomyUri($t_name, $nodupedit, $id);
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			
		}
	}// ajax_nameuri
	
	
	public function ajax_sort() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('category_perm', 'category_sort_perm') != true) {return null;}
		
		// method post
		if ($this->input->post() && $this->input->is_ajax_request()) {
			foreach ($this->input->post() as $key => $item) {
				if (is_array($item)) {
					foreach ($item as $key1 => $item1) {
						$item1 = str_replace(array('root', 'null'), '0', $item1);
						
						$this->db->set("parent_id", $item1);
						$this->db->where("tid", $key1);
						$this->db->update('taxonomy_term_data');
						
						// must update parent first, then update uris
						$this->db->set('t_uris', $this->taxonomy_model->showTaxTermUriTree($key1));
						$this->db->where("tid", $key1);
						$this->db->update('taxonomy_term_data');
					}
				}
			}
			unset($key, $key1, $item, $item1);
			
			// rebuild tree.
			$this->taxonomy_model->reBuildTaxTerm();
			
			echo '<div class="txt_success alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>'.$this->lang->line('admin_saved').'</div>';
		}
	}// ajax_sort
	
	
	public function edit($tid = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('category_perm', 'category_edit_perm') != true) {redirect('site-admin');}
		
		// tid not number?
		if (!is_numeric($tid)) {redirect('site-admin');}
		
		$output['tid'] = $tid;
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
		// list categories for select parent
		$output['list_item'] = $this->taxonomy_model->listTaxTerm();
		
		// load data for form
		$data['language'] = $this->taxonomy_model->language;
		$data['t_type'] = $this->taxonomy_model->tax_type;
		$data['tid'] = $tid;
		$tax_term = $this->taxonomy_model->getTaxonomyTermDataDb($data);
		if ($tax_term != null) {
			$row = $tax_term;
			
			$output['row'] = $row;
			
			$output['parent_id'] = $row->parent_id;
			$output['t_name'] = $row->t_name;
			$output['t_description'] = htmlspecialchars($row->t_description, ENT_QUOTES, config_item('charset'));
			$output['t_uri'] = $row->t_uri;
			$output['meta_title'] = $row->meta_title;
			$output['meta_description'] = $row->meta_description;
			$output['meta_keywords'] = $row->meta_keywords;
			$output['theme_system_name'] = $row->theme_system_name;
		} else {
			// not found selected taxonomy term data
			unset($output);
			redirect('site-admin');
		}
		
		// save action
		if ($this->input->post()) {
			
			// store data for taxonomy_term_data table.
			$data['tid'] = $tid;
			$data['parent_id'] = $this->input->post('parent_id');
			$data['t_name'] = htmlspecialchars(trim($this->input->post('t_name')), ENT_QUOTES, config_item('charset'));
			$data['t_description'] = trim($this->input->post('t_description'));
				$data['t_description'] = ($data['t_description'] == null ? null : $data['t_description']);
			$data['t_uri'] = trim($this->input->post('t_uri'));
			$data['meta_title'] = htmlspecialchars(trim($this->input->post('meta_title')), ENT_QUOTES, config_item('charset'));
				$data['meta_title'] = ($data['meta_title'] == null ? null : $data['meta_title']);
			$data['meta_description'] = htmlspecialchars(trim($this->input->post('meta_description')), ENT_QUOTES, config_item('charset'));
				$data['meta_description'] = ($data['meta_description'] == null ? null : $data['meta_description']);
			$data['meta_keywords'] = htmlspecialchars(trim($this->input->post('meta_keywords')), ENT_QUOTES, config_item('charset'));
				$data['meta_keywords'] = ($data['meta_keywords'] == null ? null : $data['meta_keywords']);
			$data['theme_system_name'] = trim($this->input->post('theme_system_name'));
				$data['theme_system_name'] = ($data['theme_system_name'] == null ? null : $data['theme_system_name']);
			
			// store data for url_alias table
			$data_ua['uri'] = $data['t_uri'];
			
			// store data for menu_items table
			$data_mi['link_text'] = $data['t_name'];
				
			// load form_validation class
			$this->load->library('form_validation');
			
			// validate form
			$this->form_validation->set_rules("t_name", "lang:category_name", "trim|required");
			$this->form_validation->set_rules("t_uri", "lang:admin_uri", "trim|min_length[3]|required");
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->taxonomy_model->edit($data, $data_ua, $data_mi);
				
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
					
					redirect('site-admin/category');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['parent_id'] = $data['parent_id'];
			$output['t_name'] = $data['t_name'];
			$output['t_description'] = htmlspecialchars($data['t_description'], ENT_QUOTES, config_item('charset'));
			$output['t_uri'] = $data['t_uri'];
			$output['meta_title'] = $data['meta_title'];
			$output['meta_description'] = $data['meta_description'];
			$output['meta_keywords'] = $data['meta_keywords'];
			$output['theme_system_name'] = $data['theme_system_name'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('category_category'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/category/category_ae_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('category_perm', 'category_viewall_perm') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list categories
		$output['list_item'] = $this->taxonomy_model->listTaxTerm();
		
		// if ajax request, send only table body
		if ($this->input->is_ajax_request()) {
			echo show_category_table_adminpage($output['list_item']);
			return true;
		}
		
		// count total items
		$output['total_item'] = $this->taxonomy_model->listTaxTermTotal();
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('category_category'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/category/category_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/category');}
		$act = trim($this->input->post('act'));
		
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('category_perm', 'category_delete_perm') != true) {redirect('site-admin');}
			foreach ($id as $an_id) {
				$this->taxonomy_model->delete($an_id);
			}
			$this->taxonomy_model->reBuildTaxTerm();
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/category');
		}
	}// process_bulk
	

}

