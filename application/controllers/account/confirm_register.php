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

class confirm_register extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('language'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	public function _remap($att1 = '', $att2 = '') 
	{
		if (isset($att2[0])) {$att2 = $att2[0];}// get confirm code in array
		$this->index($att1, $att2);
	}// _remap
	
	
	public function index($username = '', $confirm_code = '') 
	{
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		$breadcrumb[] = array('text' => $this->lang->line('frontend_home'), 'url' => '/');
		$breadcrumb[] = array('text' => lang('account_confirm_register'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// check in db
		$this->db->where('account_username', $username);
		$this->db->where('account_confirm_code', $confirm_code);
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			
			// update account
			$this->db->set('account_status', '1');
			$this->db->set('account_confirm_code', null);
			$this->db->where('account_id', $row->account_id);
			$this->db->update('accounts');
			
			$output['form_status'] = 'success';
			$output['form_status_message'] = $this->lang->line('account_confirm_register_completed');
			
			$this->modules_plug->do_action('account_register_confirmed', array('account_id' => $row->account_id, 'account_username' => $username, 'account_email' => $row->account_email));
		} else {
			$output['form_status'] = 'error';
			$output['form_status_message'] = $this->lang->line('account_confirm_register_invalid');
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_confirm_register'));
		// meta tags
		$meta = array('<meta name="robots" content="noindex, nofollow" />');
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/account/confirm_register_view', $output);
	}// index

	
}

