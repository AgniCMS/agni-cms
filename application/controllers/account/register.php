<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class register extends MY_Controller {

	
	function __construct() {
		parent::__construct();
		// load helper
		$this->load->helper( array( 'date', 'form', 'language' ) );
		// load language
		$this->lang->load( 'account' );
	}// __construct
	
	
	function index() {
		if ( $this->config_model->load_single( 'member_allow_register' ) == '0' ) {redirect( $this->base_url );}// check for allowed register?
		// get plugin captcha for check
		$output['plugin_captcha'] = $this->modules_plug->do_action( 'account_register_show_captcha' );
		// save action (register action)
		if ( $this->input->post() ) {
			$data['account_username'] = htmlspecialchars( trim( $this->input->post( 'account_username' ) ), ENT_QUOTES, config_item( 'charset' ) );
			$data['account_email'] = strip_tags( trim( $this->input->post( 'account_email', true ) ) );
			$data['account_password'] = trim( $this->input->post( 'account_password' ) );
			// load form validation
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'account_username', 'lang:account_username', 'trim|required|xss_clean|min_length[1]' );
			$this->form_validation->set_rules( 'account_email', 'lang:account_email', 'trim|required|valid_email|xss_clean' );
			$this->form_validation->set_rules( 'account_password', 'lang:account_password', 'trim|required' );
			$this->form_validation->set_rules( 'account_confirm_password', 'lang:account_confirm_password', 'trim|required|matches[account_password]' );
			if ( $this->form_validation->run() == false ) {
				$output['form_status'] = validation_errors( '<div class="txt_error alert alert-error">', '</div>' );
			} else {
				// check captcha
				if ( $output['plugin_captcha'] != null ) {
					// use plugin captcha to check
					if ( $this->modules_plug->do_action( 'account_register_check_captcha' ) == false ) {
						$output['form_status'] = '<div class="txt_error alert alert-error">'.$this->lang->line( 'account_wrong_captcha_code' ).'</div>';
					} else {
						$continue_register = true;
					}
				} else {
					// use system captcha to check
					$this->load->library( 'securimage/securimage' );
					if ( $this->securimage->check( $this->input->post( 'captcha', true ) ) == false ) {
						$output['form_status'] = '<div class="txt_error alert alert-error">'.$this->lang->line( 'account_wrong_captcha_code' ).'</div>';
					} else {
						$continue_register = true;
					}
				}
				// if captcha pass
				if ( isset( $continue_register ) && $continue_register === true ) {
					// register action
					$result = $this->account_model->register_account( $data );
					if ( $result === true ) {
						$output['hide_register_form'] = true;
						// if confirm member by email, use msg check email. if confirm member by admin, use msg wait for admin moderation.
						$member_verfication = $this->config_model->load( 'member_verification' );
						if ( $member_verfication == '1' ) {
							$output['form_status'] = '<div class="txt_success alert alert-success">'.$this->lang->line( 'account_registered_please_check_email' ).'</div>';
						} elseif ( $member_verfication == '2' ) {
							$output['form_status'] = '<div class="txt_success alert alert-success">'.$this->lang->line( 'account_registered_wait_admin_mod' ).'</div>';
						}
					} else {
						$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
					}
				}
			}
			// re-populate form
			$output['account_username'] = $data['account_username'];
			$output['account_email'] = $data['account_email'];
			
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'account_register' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page( 'front/templates/account/register_view', $output );
	}// index
	

}

