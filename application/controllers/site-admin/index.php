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

class index extends admin_controller {

	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function index() {
		// load session for flashdata
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'admin_home' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		$this->generate_page( 'site-admin/templates/index/index_view', $output );
	}// index
	

}

