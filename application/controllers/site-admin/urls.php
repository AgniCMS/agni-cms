<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class urls extends admin_controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('url_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load language
		$this->lang->load('urls');
		
		// set model property
		$this->url_model->c_type = 'redirect';
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('urls_perm' => array('urls_perm_view_all', 'urls_perm_add', 'urls_perm_edit', 'urls_perm_delete'));
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('urls_perm', 'urls_perm_add') != true) {redirect('site-admin');}
		
		// preset value
		$output['redirect_code'] = 302;
		
		// post method. save action
		if ($this->input->post()) {
			$data['uri'] = trim($this->input->post('uri'));
			$data['redirect_to'] = trim($this->input->post('redirect_to'));
			$data['redirect_code'] = trim($this->input->post('redirect_code'));
				if (!is_numeric($data['redirect_code'])) {$data['redirect_code'] = 301;}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('uri', 'lang:urls_uri', 'trim|required|xss_clean');
			$this->form_validation->set_rules('redirect_to', 'lang:urls_redirect_to', 'trim|required|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save
				$result = $this->url_model->addUrlRedirect($data);
				
				if (isset($result['result']) && $result['result'] === true) {
					// load session library
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					
					redirect('site-admin/urls');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['uri'] = htmlspecialchars($data['uri'], ENT_QUOTES, config_item('charset'));
			$output['redirect_to'] = htmlspecialchars($data['redirect_to'], ENT_QUOTES, config_item('charset'));
			$output['redirect_code'] = $data['redirect_code'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('urls_url_redirect'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/urls/urls_ae_view', $output);
	}// add
	
	
	public function ajax_check_uri() 
	{
		if ($this->input->post() && $this->input->is_ajax_request()) {
			$uri = trim($this->input->post('uri'));
			$nodupedit = trim($this->input->post('nodupedit'));
			$nodupedit = ($nodupedit == 'true' ? true : false);
			$id = intval($this->input->post('id'));
			
			$output['input_uri'] = $this->url_model->noDupUrlUri($uri, $nodupedit, $id);
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// ajax_check_uri
	
	
	public function edit($alias_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('urls_perm', 'urls_perm_edit') != true) {redirect('site-admin');}
		
		// load data for edit
		$data['alias_id'] = $alias_id;
		$data['language'] = $this->lang->get_current_lang();
		$row = $this->url_model->getUrlAliasDataDb($data);
		unset($data);
		
		if ($row == null) {
			redirect('site-admin/urls');
		}
		
		// store data for edit view
		$output['alias_id'] = $alias_id;
		$output['row'] = $row;
		$output['uri'] = htmlspecialchars($row->uri, ENT_QUOTES, config_item('charset'));
		$output['redirect_to'] = htmlspecialchars($row->redirect_to, ENT_QUOTES, config_item('charset'));
		$output['redirect_code'] = $row->redirect_code;
		
		// post method. save action
		if ($this->input->post()) {
			$data['alias_id'] = $alias_id;
			$data['uri'] = trim($this->input->post('uri'));
			$data['redirect_to'] = trim($this->input->post('redirect_to'));
			$data['redirect_code'] = trim($this->input->post('redirect_code'));
				if (!is_numeric($data['redirect_code'])) {$data['redirect_code'] = 301;}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('uri', 'lang:urls_uri', 'trim|required|xss_clean');
			$this->form_validation->set_rules('redirect_to', 'lang:urls_redirect_to', 'trim|required|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save
				$result = $this->url_model->editUrlRedirect($data);
				
				if (isset($result['result']) && $result['result'] === true) {
					// load session library
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					
					redirect('site-admin/urls');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output['uri'] = htmlspecialchars($data['uri'], ENT_QUOTES, config_item('charset'));
			$output['redirect_to'] = htmlspecialchars($data['redirect_to'], ENT_QUOTES, config_item('charset'));
			$output['redirect_code'] = $data['redirect_code'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('urls_url_redirect'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/urls/urls_ae_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('urls_perm', 'urls_perm_view_all') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// sort and get values
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'asc' ? 'desc' : 'asc');
		$output['q'] = htmlspecialchars(trim($this->input->get('q')), ENT_QUOTES, config_item('charset'));
		
		// list item
		$output['list_item'] = $this->url_model->listUrlItem(array('list_for' => 'admin'));
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('urls_url_redirect'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/urls/urls_view', $output);
	}// index
	
	
	public function multiple() 
	{
		$id = $this->input->post('id');
		$act = trim($this->input->post('act'));
		
		if ($act == 'del') {
			// check permission
			if ($this->account_model->checkAdminPermission('urls_perm', 'urls_perm_delete') != true) {redirect('site-admin');}
			
			if (is_array($id)) {
				foreach ($id as $an_id) {
					$this->url_model->deleteUrlRedirect($an_id);
				}
			}
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/urls');
		}
	}// multiple
	
	
}

// EOF