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

class tag extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('taxonomy_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('tag');
		
		// set taxonomy type
		$this->taxonomy_model->tax_type = 'tag';
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('tag_perm' => array('tag_viewall_perm', 'tag_add_perm', 'tag_edit_perm', 'tag_delete_perm'));
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('tag_perm', 'tag_add_perm') != true) {redirect('site-admin');}
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
		// save action
		if ($this->input->post()) {
			
			// store data for taxonomy_term_data table
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
			$this->form_validation->set_rules("t_name", "lang:tag_name", "trim|required");
			$this->form_validation->set_rules("t_uri", "lang:admin_uri", "trim|min_length[3]|required");
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} elseif ($this->taxonomy_model->showTaxTermInfo($data['t_name'], 't_name', 'tid') != null) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $this->lang->line('tag_name_exists');
			} else {
				$result = $this->taxonomy_model->add($data);
				
				if ($result === true) {
					
					if ($this->input->is_ajax_request()) {
						$output['tid'] = $this->taxonomy_model->showTaxTermInfo($data['t_name'], 't_name', 'tid');
						// output
						$this->output->set_content_type('application/json');
						$this->output->set_output(json_encode($output));
						return true;
					} else {
						// load session library
						$this->load->library('session');
						$this->session->set_flashdata(
							'form_status',
							array(
								'form_status' => 'success',
								'form_status_message' => $this->lang->line('admin_saved')
							)
						);
						redirect('site-admin/tag');
					}
					
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
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('tag_tag'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/tag/tag_ae_view', $output);
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
	
	
	public function edit($tid = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('tag_perm', 'tag_edit_perm') != true) {redirect('site-admin');}
		
		// tid not number?
		if (!is_numeric($tid)) {redirect('site-admin');}
		
		$output['tid'] = $tid;
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
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
			$query->free_result();
			unset($output);
			redirect('site-admin');
		}
		
		// save action
		if ($this->input->post()) {
			
			// store data for taxonomy table.
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
			$this->form_validation->set_rules("t_name", "lang:tag_name", "trim|required");
			$this->form_validation->set_rules("t_uri", "lang:admin_uri", "trim|min_length[3]|required");
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$check_result = $this->taxonomy_model->showTaxTermInfo($data['t_name'], 't_name', 'tid');
				
				if ($check_result != $data['tid'] && $check_result != null) {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $this->lang->line('tag_name_exists');
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
						
						redirect('site-admin/tag');
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
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
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('tag_tag'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/tag/tag_ae_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('tag_perm', 'tag_viewall_perm') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// sorting, search vars for links in views
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'asc' ? 'desc' : 'asc');
		$output['q'] = htmlspecialchars(trim($this->input->get('q')), ENT_QUOTES, config_item('charset'));
		
		// list tags
		$output['list_item'] = $this->taxonomy_model->listTags(array('list_for' => 'admin'));
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('tag_tag'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/tag/tag_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/tag');}
		$act = trim($this->input->post('act'));
		
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('tag_perm', 'tag_delete_perm') != true) {redirect('site-admin');}
			
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
			redirect('site-admin/tag');
		}
	}// process_bulk
	

}

