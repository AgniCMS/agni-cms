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

class config extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('themes_model'));
		
		// load library
		$this->load->library(array('encrypt'));
		
		// load helper
		$this->load->helper(array('date', 'form'));
		
		// load language
		$this->lang->load('config');
		
		// load config
		$this->config->load('agni');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('config_global' => array('config_global'));
	}// _define_permission
	
	
	public function ajax_test_ftp() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('config_global', 'config_global') != true) {redirect('site-admin');}
		
		if (!$this->input->is_ajax_request()) {redirect('site-admin');}
		
		// test ftp connection.
		$config['hostname'] = $this->input->post('hostname');
		$config['username'] = $this->input->post('username');
		$config['password'] = $this->input->post('password');
		$config['port'] = (int) $this->input->post('port');
		$config['passive'] = ($this->input->post('passive') == 'true' ? true : false);
		$config['debug'] = false;
		
		$basepath = $this->input->post('basepath');
		
		// load library
		$this->load->library('ftp');
		
		$connect_result = $this->ftp->connect($config);
		
		if ($connect_result === true) {
			$files = $this->ftp->list_files($basepath);

			if (is_array($files) && !empty($files)) {
				natsort($files);

				echo '<div class="txt_info alert alert-info">'.lang('config_ftp_basepath_correct_should_see_application_modules_public_system_folders').'</div>';
				foreach ($files as $file) {
					echo str_replace($basepath, '', $file) . '<br />';
				}
			} else {
				echo '<div class="txt_error alert alert-error">'.lang('config_ftp_basepath_incorrect').'</div>';
			}
		} else {
			echo '<div class="txt_error alert alert-error">'.$this->lang->line('config_ftp_could_not_connect_to_server').'</div>';
		}
		
		$this->ftp->close();
	}// ajax_test_ftp
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('config_global', 'config_global') != true) {redirect('site-admin');}
		
		// load session
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// load config to form
		$this->db->where('config_core', '1');
		$query = $this->db->get('config');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$output[$row->config_name] = htmlspecialchars($row->config_value);
			}
			$output['content_frontpage_category'] = $this->config_model->loadSingle('content_frontpage_category', $this->lang->get_current_lang());
		} else {
			log_message('error', 'No config in config table.');
			redirect('site-admin');
		}
		$query->free_result();
		
		// method post request (save data)
		if ($this->input->post()) {
			//tab1
			$data['site_name'] = trim($this->input->post('site_name', true));
			$data['page_title_separator'] = $this->input->post('page_title_separator', true);
			$data['site_timezone'] = trim($this->input->post('timezones', true));
			$data['angi_auto_update'] = trim($this->input->post('angi_auto_update'));
			if ($data['angi_auto_update'] != '1') {$data['angi_auto_update'] = '0';}
			$data['agni_auto_update_url'] = trim($this->input->post('agni_auto_update_url'));
			$data['agni_system_cron'] = trim($this->input->post('agni_system_cron'));
			if ($data['agni_system_cron'] != '1') {$data['agni_system_cron'] = '0';}
			
			//tab2
			$data['member_allow_register'] = $this->input->post('member_allow_register');
			if ($data['member_allow_register'] != '1') {$data['member_allow_register'] = '0';}
			$data['member_register_notify_admin'] = $this->input->post('member_register_notify_admin');
			if ($data['member_register_notify_admin'] != '1') {$data['member_register_notify_admin'] = '0';}
			$data['member_verification'] = $this->input->post('member_verification');
			$data['member_admin_verify_emails'] = trim($this->input->post('member_admin_verify_emails'));
			$data['duplicate_login'] = $this->input->post('duplicate_login');
			if ($data['duplicate_login'] != '1') {$data['duplicate_login'] = '0';}
			$data['allow_avatar'] = $this->input->post('allow_avatar');
			if ($data['allow_avatar'] != '1') {$data['allow_avatar'] = '0';}
			$data['avatar_size'] = trim($this->input->post('avatar_size'));
			if (!is_numeric($data['avatar_size'])) {$data['avatar_size'] = '200';}
			$data['avatar_allowed_types'] = trim($this->input->post('avatar_allowed_types', true));
			if (empty($data['avatar_allowed_types'])) {$data['avatar_allowed_types'] = 'jpg|jpeg';}
			
			//tab3
			$data['mail_protocol'] = $this->input->post('mail_protocol');
			$data['mail_mailpath'] = trim($this->input->post('mail_mailpath'));
			$data['mail_smtp_host'] = trim($this->input->post('mail_smtp_host'));
			$data['mail_smtp_user'] = trim($this->input->post('mail_smtp_user'));
			$data['mail_smtp_pass'] = trim($this->input->post('mail_smtp_pass'));
			$data['mail_smtp_port'] = (int) $this->input->post('mail_smtp_port');
			$data['mail_sender_email'] = trim($this->input->post('mail_sender_email', true));
			
			//tab4
			$data['content_show_title'] = $this->input->post('content_show_title');
			if ($data['content_show_title'] != '1') {$data['content_show_title'] = '0';}
			$data['content_show_time'] = $this->input->post('content_show_time');
			if ($data['content_show_time'] != '1') {$data['content_show_time'] = '0';}
			$data['content_show_author'] = $this->input->post('content_show_author');
			if ($data['content_show_author'] != '1') {$data['content_show_author'] = '0';}
			$data['content_items_perpage'] = trim($this->input->post('content_items_perpage'));
			if (!is_numeric($data['content_items_perpage'])) {$data['content_items_perpage'] = '10';}
			$data['content_frontpage_category'] = trim($this->input->post('content_frontpage_category'));
			if (!is_numeric($data['content_frontpage_category']) || $data['content_frontpage_category'] == null) {$data['content_frontpage_category'] = null;}
			
			// tab media
			$data['media_allowed_types'] = trim($this->input->post('media_allowed_types'));
			if (empty($data['media_allowed_types'])) {$data['media_allowed_types'] = 'jpeg|jpg|gif|png';}
			
			// tab comment
			$data['comment_allow'] = $this->input->post('comment_allow');
			if ($data['comment_allow'] != '1' && $data['comment_allow'] != '0') {$data['comment_allow'] = null;}
			$data['comment_show_notallow'] = $this->input->post('comment_show_notallow');
			if ($data['comment_show_notallow'] != '1') {$data['comment_show_notallow'] = '0';}
			$data['comment_perpage'] = trim($this->input->post('comment_perpage'));
			if (!is_numeric($data['comment_perpage'])) {$data['comment_perpage'] = '40';}
			$data['comment_new_notify_admin'] = $this->input->post('comment_new_notify_admin');
			if ($data['comment_new_notify_admin'] < '0' || $data['comment_new_notify_admin'] > '2') {$data['comment_new_notify_admin'] = '1';}
			$data['comment_admin_notify_emails'] = trim($this->input->post('comment_admin_notify_emails'));
			
			// tab ftp
			$data['ftp_host'] = trim($this->input->post('ftp_host'));
			$data['ftp_username'] = trim($this->input->post('ftp_username'));
			$data['ftp_password'] = trim($this->input->post('ftp_password'));
			if ($data['ftp_password'] != null) {$data['ftp_password'] = $this->encrypt->encode($data['ftp_password']);}
			$data['ftp_port'] = trim($this->input->post('ftp_port'));
			if ($data['ftp_port'] == null || !is_numeric($data['ftp_port'])) {$data['ftp_port'] = '21';}
			$data['ftp_passive'] = trim($this->input->post('ftp_passive'));
			$data['ftp_basepath'] = trim($this->input->post('ftp_basepath'));
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('site_name', 'lang:config_sitename', 'trim|required|xss_clean');
			$this->form_validation->set_rules('member_admin_verify_emails', 'lang:config_member_admin_verify_emails', 'required|valid_emails');
			$this->form_validation->set_rules('mail_sender_email', 'lang:config_mail_sender_email', 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('content_items_perpage', 'lang:config_content_items_perpage', 'trim|required|integer|xss_clean');
			$this->form_validation->set_rules('comment_perpage', 'lang:config_comment_perpage', 'trim|required|integer|xss_clean');
			$this->form_validation->set_rules('comment_admin_notify_emails', 'lang:config_comment_admin_notify_emails', 'trim|required|valid_email|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save config
				$result = $this->config_model->save($data);
				
				if ($result === true) {
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					redirect('site-admin/config');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-population form
			foreach ($data as $key => $item) {
				$output[$key] = htmlspecialchars($item);
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('config_global'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/config/config_view', $output);
	}// index
	

}

