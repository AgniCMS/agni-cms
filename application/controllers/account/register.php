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

class register extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('date', 'form', 'language'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	public function index() 
	{
		if ($this->config_model->loadSingle('member_allow_register') == '0') {redirect($this->base_url);}// check for allowed register?
		
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		$breadcrumb[] = array('text' => $this->lang->line('frontend_home'), 'url' => '/');
		$breadcrumb[] = array('text' => lang('account_register'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// get plugin captcha for check
		$output['plugin_captcha'] = $this->modules_plug->do_filter('account_register_show_captcha');
		
		// save action (register action)
		if ($this->input->post()) {
			$data['account_username'] = htmlspecialchars(trim($this->input->post('account_username')), ENT_QUOTES, config_item('charset'));
			$data['account_email'] = strip_tags(trim($this->input->post('account_email', true)));
			$data['account_password'] = trim($this->input->post('account_password'));
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('account_username', 'lang:account_username', 'trim|required|xss_clean|min_length[1]|no_space_between_text');
			$this->form_validation->set_rules('account_email', 'lang:account_email', 'trim|required|valid_email|xss_clean');
			$this->form_validation->set_rules('account_password', 'lang:account_password', 'trim|required');
			$this->form_validation->set_rules('account_confirm_password', 'lang:account_confirm_password', 'trim|required|matches[account_password]');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// check plugin captcha
				if ($output['plugin_captcha'] != null) {
					// use plugin captcha to check
					$plug_captcha_check = $this->modules_plug->do_action('account_register_check_captcha', $_POST);
					
					if (isset($plug_captcha_check['account_register_check_captcha']) && is_array($plug_captcha_check['account_register_check_captcha']) && in_array(true, $plug_captcha_check['account_register_check_captcha'], true)) {
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
						$continue_register = true;
					}
				}
				// if captcha pass
				if (isset($continue_register) && $continue_register === true) {
					// register action
					$result = $this->account_model->registerAccount($data);
					
					if ($result === true) {
						$output['hide_register_form'] = true;
						
						// if confirm member by email, use msg check email. if confirm member by admin, use msg wait for admin moderation.
						$member_verfication = $this->config_model->load('member_verification');
						if ($member_verfication == '1') {
							$output['form_status'] = 'success';
							$output['form_status_message'] = $this->lang->line('account_registered_please_check_email');
						} elseif ($member_verfication == '2') {
							$output['form_status'] = 'success';
							$output['form_status_message'] = $this->lang->line('account_registered_wait_admin_mod');
						}
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
				}
			}
			
			// re-populate form
			$output['account_username'] = $data['account_username'];
			$output['account_email'] = $data['account_email'];
			
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_register'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/account/register_view', $output);
	}// index
	

}

