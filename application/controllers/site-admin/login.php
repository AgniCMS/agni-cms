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
		
		// load model
		$this->load->model(array('account_model', 'config_model', 'html_model'));
		
		// load helper
		$this->load->helper(array('form', 'language', 'siteinfo'));
		
		// load language
		$this->lang->load('admin');
		$this->lang->load('account');
	}// __construct
	
	
	public function _define_permission() 
	{
		// return array('page name, controller' => array('action1', 'action2 or more more'));
		return array('account_admin_login' => array('account_admin_login'));
	}// _define_permission
	
	
	public function _browser_check() 
	{
		// load library
		$this->load->library(array('Browser'));
		
		if (($this->browser->getBrowser() == browser::BROWSER_IE && $this->browser->getVersion() >= 8) ||
			($this->browser->getBrowser() == browser::BROWSER_OPERA && $this->browser->getVersion() >= 10) ||
			($this->browser->getBrowser() == browser::BROWSER_FIREFOX && $this->browser->getVersion() >= 3) ||
			($this->browser->getBrowser() == browser::BROWSER_SAFARI && $this->browser->getVersion() >= 4) || 
			($this->browser->getBrowser() == browser::BROWSER_CHROME && $this->browser->getVersion() >= 8) || 
			($this->browser->getBrowser() == browser::BROWSER_ANDROID)) {
			return 'yes';
		} elseif (($this->browser->getBrowser() == browser::BROWSER_IE && $this->browser->getVersion() < 8)) {
			return 'no';
		} else {
			return 'unknow';
		}
	}// _browser_check
	
	
	public function index() 
	{
		// set theme to admin default theme (this controller use front controller NOT admin controller, if front controller set to others it can mess up theme and style.)
		$theme_system_name = $this->themes_model->getDefaultTheme('admin');
		$this->theme_path = base_url().config_item('agni_theme_path').$theme_system_name.'/';// for use in css
		$this->theme_system_name = $theme_system_name;
		
		// set login redirect
		if ($this->input->get('rdr') != null) {
			$output['go_to'] = urlencode($this->input->get('rdr'));
		}
		
		// load session library
		$this->load->library(array('securimage/securimage', 'session'));
		
		// read account error. eg. duplicate login error from check_login() in account model.
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
		
		// browser check
		$output['browser_check'] = $this->_browser_check();
		
		// do log in
		if ($this->input->post()) {
			$data['username'] = htmlspecialchars(trim($this->input->post('username')), ENT_QUOTES, config_item('charset'));
			$data['password'] = trim($this->input->post('password'));
			
			// validate form
			$this->load->library('form_validation');
			$this->form_validation->set_rules('username', 'lang:account_username', 'trim|required');
			$this->form_validation->set_rules('password', 'lang:account_password', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				$login_fail_last_time = $this->account_model->loginFailLastTime($data['username']);
				$count_login_fail = $this->account_model->countLoginFail($data['username']);
				
				if (($count_login_fail !== false && $login_fail_last_time !== false) && ($count_login_fail > 10 && (time()-strtotime($login_fail_last_time))/(60) < 30)) {
					// login failed over 10 times
					$result = $this->lang->line('account_login_fail_to_many');
				} else {
					if (isset($output['show_captcha']) && $output['show_captcha'] == true && $this->securimage->check(strtoupper(trim($this->input->post('captcha', true)))) == false) {
						$result = $this->lang->line('account_wrong_captcha_code');
					} else {
						// try to login
						$result = $this->account_model->adminLogIn($data);
					}
				}
				unset($login_fail_last_time, $count_login_fail);
				
				// fetch last data (after login fail, there is a logins update)
				$login_fail_last_time = $this->account_model->loginFailLastTime($data['username']);
				$count_login_fail = $this->account_model->countLoginFail($data['username']);
				if ($count_login_fail > 2 && $this->input->is_ajax_request()) {
					$output['show_captcha'] = true;
				}
				
				// check login result and count login fail.
				if ($result === true) {
					$this->session->unset_userdata('fail_count');
					$this->session->unset_userdata('fail_count_time');
					$this->session->unset_userdata('show_captcha');
					unset($login_fail_last_time, $count_login_fail);
					
					if (!$this->input->is_ajax_request()) {
						// if not ajax request
						
						// if set go to, redirect to there
						if (isset($output['go_to'])) {
							redirect($this->input->get('rdr'));
						} else {
							redirect('site-admin');
						}
					} else {
						// if ajax request
						$output['form_status'] = true;
						
						// if set go to, send to there
						if (isset($output['go_to'])) {
							$output['go_to'] = $this->input->get('rdr', true);
						} else {
							$output['go_to'] = site_url('site-admin');
						}
					}
				} else {
					$this->session->set_userdata('fail_count', $count_login_fail);
					$this->session->set_userdata('fail_count_time', strtotime($login_fail_last_time));
					
					if ($count_login_fail >= 3) {
						$this->session->set_userdata('show_captcha', true);
					}
					
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
				unset($login_fail_last_time, $count_login_fail);
			}
			
			// re-populate form
			$output['username'] = htmlspecialchars($data['username']);
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_login'));
		// meta tags
		$meta = array('<meta name="robots" content="noindex, nofollow" />');
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if (!$this->input->is_ajax_request()) {
			$this->load->view('site-admin/templates/login/login_view', $output);
		} else {
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// index
	
	
	public function resetpw() 
	{
		if (!$this->input->is_ajax_request()) {redirect('site-admin');}
		if ($this->input->post()) {
			// load libraries
			$this->load->library(array('form_validation', 'securimage/securimage'));
			$this->form_validation->set_rules('email', 'lang:account_email', 'trim|required|valid_email');
			
			if ($this->form_validation->run() == false) {
				$result = validation_errors('<div>', '</div>');
			} else {
				$email = trim($this->input->post('email'));
				// check captcha
				if ($this->securimage->check(strtoupper(trim($this->input->post('captcha', true)))) == false) {
					$result = $this->lang->line('account_wrong_captcha_code');
				} else {
					// send request reset password
					$result = $this->account_model->resetPassword1($email);
				}
			}
			// check result
			if ($result === true) {
				$output['result'] = true;
				$output['form_status'] = 'success';
				$output['form_status_message'] = $this->lang->line('account_please_check_email_confirm_resetpw');
			} else {
				$output['result'] = false;
				$output['form_status'] = 'error';
				$output['form_status_message'] = $result;
			}
			unset($email, $result);
		}
		// output json
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// resetpw
	

}

