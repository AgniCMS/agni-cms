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

class resend_activate extends MY_Controller 
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
		$breadcrumb[] = array('text' => lang('account_resend_verify_email'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// method post (re-send action)
		if ($this->input->post()) {
			$data['account_email'] = trim($this->input->post('account_email'));
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('account_email', 'lang:account_email', 'trim|required|valid_email|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// check for registered email but not confirm
				$this->db->where('account_email', $data['account_email']);
				$this->db->where('account_status', '0');
				$this->db->where('account_status_text', null);
				$this->db->where('account_new_email', null);
				$this->db->where('account_new_password', null);
				$this->db->where('account_confirm_code != NULL');
				$query = $this->db->get('accounts');
				
				if ($query->num_rows() <= 0) {
					$query->free_result();
					
					$output['form_status'] = 'error';
					$output['form_status_message'] = $this->lang->line('account_not_found_with_this_email');
				} else {
					$row = $query->row();
					$query->free_result();
					
					// generate confirm code
					$this->load->helper('string');
					$data['account_confirm_code'] = random_string('alnum', '6');
					$data['account_username'] = $row->account_username;
					
					// re-send email
					$result = $this->account_model->sendRegisterEmail($data);
					
					if ($result === true) {
						$this->db->set('account_confirm_code', $data['account_confirm_code']);
						$this->db->where('account_id', $row->account_id);
						$this->db->update('accounts');
						$output['form_status'] = 'success';
						$output['form_status_message'] = $this->lang->line('account_registered_please_check_email');
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $result;
					}
					
					unset($result, $row, $query);
				}
			}
			// re-populate form
			$output['account_email'] = $data['account_email'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_resend_verify_email'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/account/resend_activate_view', $output);
	}// index
	

}

