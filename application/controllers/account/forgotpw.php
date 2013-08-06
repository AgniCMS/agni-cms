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

class forgotpw extends MY_Controller 
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
		$breadcrumb[] = array('text' => lang('account_forget_userpass'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		$output['plugin_captcha'] = $this->modules_plug->do_filter('account_show_captcha');
		
		// submitted email to reset password
		if ($this->input->post()) {
			$data['account_email'] = trim($this->input->post('account_email'));
			
			// load libraries
			$this->load->library(array('form_validation', 'securimage/securimage'));
			$this->form_validation->set_rules('account_email', 'lang:account_email', 'trim|required|valid_email');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// check captcha
				if ($output['plugin_captcha'] != null) {
					// use plugin captcha to check
					$plug_captcha_check = $this->modules_plug->do_action('account_check_captcha', $_POST);
					
					if (isset($plug_captcha_check['account_check_captcha']) && is_array($plug_captcha_check['account_check_captcha']) && in_array(true, $plug_captcha_check['account_check_captcha'], true)) {
						$continue = true;
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $this->lang->line('account_wrong_captcha_code');
					}
				} else {
					// use system captcha to check
					$this->load->library('securimage/securimage');
					if ($this->securimage->check($this->input->post('captcha', true)) == false) {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $this->lang->line('account_wrong_captcha_code');
					} else {
						$continue = true;
					}
				}
				
				// if captcha pass
				if (isset($continue) && $continue === true) {
					$result = $this->account_model->resetPassword1($data['account_email']);
					
					if ($result === true) {
						$output['hide_form'] = true;
						$output['form_status'] = 'success';
						$output['form_status_message'] = $this->lang->line('account_please_check_email_confirm_resetpw');
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
				}
			}
			
			// re-populate form
			$output['account_email'] = $data['account_email'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_forget_userpass'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/account/forgotpw_view', $output);
	}// index
	

}

