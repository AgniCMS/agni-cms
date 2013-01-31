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

class view_logins extends MY_Controller {

	
	function __construct() {
		parent::__construct();
		
		// load helper
		$this->load->helper( array( 'date', 'language' ) );
		
		// load language
		$this->lang->load( 'account' );
	}// __construct
	
	
	function index() {
		// is member login?
		if ( !$this->account_model->is_member_login() ) {redirect( site_url() );}
		
		// get id
		$cm_account = $this->account_model->get_account_cookie( 'member' );
		
		// load accounts table
		$row = $this->account_model->get_account_data( array( 'account_id' => $cm_account['id'] ) );
		if ( $row == null ) {
			redirect( site_url() );
		}
		$output['account'] = $row;
		
		// list logins
		$output['list_item'] = $this->account_model->list_account_logins( $row->account_id );
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'account_view_logins' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ( $this->input->is_ajax_request() ) {
			$this->load->view( 'front/templates/account/view_logins_view', $output );
		} else {
			$this->generate_page( 'front/templates/account/view_logins_view', $output );
		}
	}// index
	

}

