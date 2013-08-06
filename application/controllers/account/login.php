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

class login extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('form', 'language'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	public function index() 
	{
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		$breadcrumb[] = array('text' => $this->lang->line('frontend_home'), 'url' => '/');
		$breadcrumb[] = array('text' => lang('account_login'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// set login redirect referrer (when done)
		$this->load->library('user_agent');
		if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
			$output['go_to'] = urlencode($this->agent->referrer());
		}
		if ($this->input->get('rdr') != null) {
			$output['go_to'] = urlencode($this->input->get('rdr', true));
		}
		
		// load library
		$this->load->library(array('securimage/securimage', 'session'));
		
		// read account error. eg. duplicate, simultaneous login error from check_login() in account model.
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// count login fail
		if ($this->session->userdata('fail_count') >= 3 || $this->session->userdata('show_captcha') == true) {
			$output['show_captcha'] = true;
			
			if ((time()-$this->session->userdata('fail_count_time'))/(60) < 30) {
				// fail over 30 minute, reset.
				$this->session->unset_userdata('fail_count');
				$this->session->unset_userdata('fail_count_time');
				$this->session->unset_userdata('show_captcha');
			}
		}
		
		// login submitted
		if ($this->input->post()) {
			$data['account_username'] = htmlspecialchars(trim($this->input->post('account_username')), ENT_QUOTES, config_item('charset'));
			$data['account_password'] = trim($this->input->post('account_password'));
			
			// validate form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('account_username', 'lang:account_username', 'trim|required');
			$this->form_validation->set_rules('account_password', 'lang:account_password', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$login_fail_last_time = $this->account_model->loginFailLastTime($data['account_username']);
				$count_login_fail = $this->account_model->countLoginFail($data['account_username']);
				
				// count login fail and wait time
				if (($count_login_fail !== false && $login_fail_last_time !== false) && ($count_login_fail > 10 && (time()-strtotime($login_fail_last_time))/(60) < 30)) {
					// login failed over 10 times
					$result = $this->lang->line('account_login_fail_to_many');
				} else {
					if (isset($output['show_captcha']) && $output['show_captcha'] == true && $this->securimage->check(strtoupper(trim($this->input->post('captcha', true)))) == false) {
						$result = $this->lang->line('account_wrong_captcha_code');
					} else {
						// try to login
						$result = $this->account_model->memberLogIn($data);
					}
				}
				
				// check result and login fail count
				if ($result === true) {
					$this->session->unset_userdata('fail_count');
					$this->session->unset_userdata('fail_count_time');
					$this->session->unset_userdata('show_captcha');
					
					if (!$this->input->is_ajax_request()) {
						if (isset($output['go_to'])) {
							redirect($this->input->get('rdr', true));
						} else {
							redirect(site_url());
						}
					} else {
						// ajax login
						if (!isset($output['go_to'])) {
							$output['go_to'] = site_url();
						}
						$output['login_status'] = true;
						$this->output->set_content_type('application/json');
						$this->output->set_output(json_encode($output));
						return true;
					}
				} else {
					// fetch last data (after login fail, there is a logins update)
					$login_fail_last_time = $this->account_model->loginFailLastTime($data['account_username']);
					$count_login_fail = $this->account_model->countLoginFail($data['account_username']);
					
					if ($count_login_fail > 2 && $this->input->is_ajax_request()) {
						$output['show_captcha'] = true;
					}
					
					// set session fail_count and fail_count_time
					$this->session->set_userdata('fail_count', $count_login_fail);
					$this->session->set_userdata('fail_count_time', strtotime($login_fail_last_time));
					
					if ($count_login_fail >= 3) {
						$this->session->set_userdata('show_captcha', true);
					}
					
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
				
			}
			
			// re-populate form
			$output['account_username'] = $data['account_username'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_login'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/account/login_view', $output);
	}// index
	

}

