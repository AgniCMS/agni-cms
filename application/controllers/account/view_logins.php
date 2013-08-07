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

class view_logins extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('date', 'language'));
		
		// load language
		$this->lang->load('account');
	}// __construct
	
	
	public function index() 
	{
		// is member login?
		if (!$this->account_model->isMemberLogin()) {redirect(site_url());}
		
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		$breadcrumb[] = array('text' => $this->lang->line('frontend_home'), 'url' => '/');
		$breadcrumb[] = array('text' => lang('account_edit_profile'), 'url' => site_url('account/edit-profile'));
		$breadcrumb[] = array('text' => lang('account_view_logins'), 'url' => current_url());
		$output['breadcrumb'] = $breadcrumb;
		unset($breadcrumb);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// get id
		$cm_account = $this->account_model->getAccountCookie('member');
		
		// load accounts table
		$row = $this->account_model->getAccountData(array('account_id' => $cm_account['id']));
		if ($row == null) {
			redirect(site_url());
		}
		$output['account'] = $row;
		
		// list logins
		$output['list_item'] = $this->account_model->listAccountLogins($row->account_id);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('account_view_logins'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ($this->input->is_ajax_request()) {
			$this->load->view('front/templates/account/view_logins_view', $output);
		} else {
			$this->generate_page('front/templates/account/view_logins_view', $output);
		}
	}// index
	

}

