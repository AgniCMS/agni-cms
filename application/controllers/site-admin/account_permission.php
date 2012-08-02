<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class account_permission extends admin_controller {

	
	function __construct() {
		parent::__construct();
		// load model
		$this->load->model( array( 'permission_model' ) );
		// load helper
		$this->load->helper( array( 'form' ) );
		// load language
		$this->lang->load( 'account' );
	}// __construct
	
	
	function _define_permission() {
		return array( 'account_permission_perm' => array( 'account_permission_manage_perm' ) );
	}// _define_permission
	
	
	function index() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'account_permission_perm', 'account_permission_manage_perm' ) != true ) {redirect( 'site-admin' );}
		// load session for flashdata
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		$output['list_permissions'] = $this->permission_model->fetch_permissions();
		$output['list_permissions_check'] = $this->permission_model->list_permissions_check();
		$output['list_level_group'] = $this->account_model->list_level_group( false );
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'account_permission' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		$this->output->set_header( 'Pragma: no-cache' );
		$this->generate_page( 'site-admin/templates/account/account_permission_view', $output );
		unset( $output );
	}// index
	
	
	function reset() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'account_permission_perm', 'account_permission_manage_perm' ) != true ) {redirect( 'site-admin' );}
		$this->permission_model->reset_permissions();
	}// reset
	
	
	function save() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'account_permission_perm', 'account_permission_manage_perm' ) != true ) {redirect( 'site-admin' );}
		// save action
		if ( $this->input->post() ) {
			// remove all of previous settings
			$this->permission_model->reset_permissions();
			// preset array post permissions.
			$permission_page = $this->input->post( 'permission_page' );
			$permission_action = $this->input->post( 'permission_action' );
			// loop insert settings.
			foreach ( $this->input->post( 'level_group_id' ) as $key => $lv_groups ) {
				foreach ( $lv_groups as $level_group_id ) {
					$this->db->set( 'level_group_id', $level_group_id );
					$this->db->set( 'permission_page', trim( $permission_page[$key] ) );
					$this->db->set( 'permission_action', trim( $permission_action[$key] ) );
					$this->db->insert( 'account_level_permission' );
				}
			}
		}
		// set success msg and send back
		$this->load->library( 'session' );
		$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">' . $this->lang->line( 'admin_saved' ) . '</div>' );
		redirect( 'site-admin/account-permission' );
	}// save
	

}

