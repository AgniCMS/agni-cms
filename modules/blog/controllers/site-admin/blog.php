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
 
class blog extends admin_controller {
	
	
	function __construct() {
		parent::__construct();
		// load model
		$this->load->model(array('blog_model'));
		// load helper
		$this->load->helper(array('form'));
	}// __construct
	
	
	function add() {
		// check permission
		if ($this->account_model->checkAdminPermission('blog_admin', 'blog_add_post') != true) {redirect('site-admin');}
		// post method. save action
		if ($this->input->post()) {
			$data['blog_title'] = htmlspecialchars(trim($this->input->post('blog_title', true)), ENT_QUOTES, config_item('charset'));
			$data['blog_content'] = trim($this->input->post('blog_content'));
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('blog_title', 'lang:blog_title', 'trim|required|xss_clean');
			$this->form_validation->set_rules('blog_content', 'lang:blog_content', 'trim|required');
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->blog_model->add($data);
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
					redirect('blog/site-admin/blog');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			// re-populate form
			$output['blog_title'] = $data['blog_title'];
			$output['blog_content'] = $data['blog_content'];
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('blog_blog'));
		// meta tags
		// link tags
		// script tags
		$script_tags[] = '<script src="'.$this->theme_path.'share-js/jquery.textarea.js"></script>';
		$output['page_script'] = $this->html_model->gen_tags($script_tags);
		unset($script_tags);
		// end head tags output ##############################
		// output
		$this->generate_page('site-admin/blog_add_edit_view', $output);
	}// add
	
	
	function edit($blog_id = '') {
		// check permission
		if ($this->account_model->checkAdminPermission('blog_admin', 'blog_edit_post') != true) {redirect('site-admin');}
		// open db load data for form
		$this->db->where('blog_id', $blog_id);
		$query = $this->db->get('blog');
		if($query->num_rows() <= 0) {
			// not found
			$query->free_result();
			redirect('blog/site-admin/blog');
		}
		$row = $query->row();
		$query->free_result();
		// set data in db for form
		$output['blog_title'] = $row->blog_title;
		$output['blog_content'] = $row->blog_content;
		// post method. save action
		if ($this->input->post()) {
			$data['blog_id'] = $blog_id;
			$data['blog_title'] = htmlspecialchars(trim($this->input->post('blog_title', true)), ENT_QUOTES, config_item('charset'));
			$data['blog_content'] = trim($this->input->post('blog_content'));
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('blog_title', 'lang:blog_title', 'trim|required|xss_clean');
			$this->form_validation->set_rules('blog_content', 'lang:blog_content', 'trim|required');
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$result = $this->blog_model->edit($data);
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
					redirect('blog/site-admin/blog');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			// re-populate form
			$output['blog_title'] = $data['blog_title'];
			$output['blog_content'] = $data['blog_content'];
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('blog_blog'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page('site-admin/blog_add_edit_view', $output);
	}// edit
	
	
	function index() {
		// check permission
		if ($this->account_model->checkAdminPermission('blog_admin', 'blog_all_post') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list posts
		$output['list_item'] = $this->blog_model->list_item('admin');
		if (is_array($output['list_item'])) {
			$this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('blog_blog'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/blog_view', $output);
	}// index
	
	
	function multiple() {
		$act = trim($this->input->post('act'));
		$ids = $this->input->post('id');
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('blog_admin', 'blog_delete_post') != true) {redirect('site-admin');}
			foreach ($ids as $an_id) {
				$this->blog_model->delete($an_id);
			}
		}
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('blog/site-admin/blog');
		}
	}// multiple
	
	
}

// EOF