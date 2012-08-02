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

class module extends admin_controller {

	
	function __construct() {
		parent::__construct();
		// load model
		$this->load->model( array( 'modules_model' ) );
		// load helper
		$this->load->helper( array( 'form' ) );
		// load language
		$this->lang->load( 'modules' );
	}// __construct
	
	
	function _define_permission() {
		return array( 'modules_manage_perm' => array( 'modules_viewall_perm', 'modules_add_perm', 'modules_activate_deactivate_perm', 'modules_delete_perm' ) );
	}// _define_permission
	
	
	function activate() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_activate_deactivate_perm' ) != true ) {redirect( 'site-admin' );}
		// get module sys name
		$module_system_name = trim( $this->input->get( 'id' ) );
		$result = $this->modules_model->do_activate( $module_system_name );
		// load session
		$this->load->library( 'session' );
		if ( $result === true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_activated' ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_activated_fail' ).'</div>' );
		}
		redirect( 'site-admin/module' );
	}// activate
	
	
	function add() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_add_perm' ) != true ) {redirect( 'site-admin' );}
		// save action.
		if ( $this->input->post() ) {
			$result = $this->modules_model->add_module();
			if ( $result === true ) {
				// load session
				$this->load->library( 'session' );
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_added' ).'</div>' );
				redirect( 'site-admin/module' );
			} else {
				$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
			}
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'modules_modules' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		$this->generate_page( 'site-admin/templates/modules/modules_add_view', $output );
	}// add
	
	
	function deactivate() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_activate_deactivate_perm' ) != true ) {redirect( 'site-admin' );}
		// get module sys name
		$module_system_name = trim( $this->input->get( 'id' ) );
		$result = $this->modules_model->do_deactivate( $module_system_name );
		// load session
		$this->load->library( 'session' );
		if ( $result === true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_deactivated' ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_deactivated_fail' ).'</div>' );
		}
		redirect( 'site-admin/module' );
	}// deactivate
	
	
	function delete() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_delete_perm' ) != true ) {redirect( 'site-admin' );}
		// get module sys name
		$module_system_name = trim( $this->input->post( 'id' ) );
		$result = $this->modules_model->delete_a_module( $module_system_name );
		// load session
		$this->load->library( 'session' );
		if ( $result === true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_deleted' ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_deleted_fail' ).'</div>' );
		}
		redirect( 'site-admin/module' );
	}// delete
	
	
	function index() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_viewall_perm' ) != true ) {redirect( 'site-admin' );}
		// load session for show last flashed session
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		// list modules
		$output['list_item'] = $this->modules_model->list_all_modules();
		if ( is_array( $output['list_item'] ) ) {
			$output['pagination'] = $this->pagination->create_links();
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'modules_modules' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		$this->generate_page( 'site-admin/templates/modules/modules_view', $output );
	}// index
	
	
	function process_bulk() {
		$id = $this->input->post( 'id' );
		if ( !is_array( $id ) ) {redirect( 'site-admin/module' );}
		$act = trim( $this->input->post( 'act' ) );
		// load library
		$this->load->library( 'session' );
		if ( $act == 'activate' ) {
			// check permission
			if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_activate_perm' ) != true ) {redirect( 'site-admin' );}
			foreach ( $id as $an_id ) {
				$result = $this->modules_model->do_activate( $an_id );
				if ( $result === false ) {
					$fail_activate = true;
				}
			}
			if ( isset( $fail_activate ) && $fail_activate == true ) {
				$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_activated_fail_some' ).'</div>' );
			} else {
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_activated' ).'</div>' );
			}
			unset( $fail_activate, $result );
		} elseif ( $act == 'deactivate' ) {
			// check permission
			if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_deactivate_perm' ) != true ) {redirect( 'site-admin' );}
			foreach ( $id as $an_id ) {
				$result = $this->modules_model->do_deactivate( $an_id );
				if ( $result === false ) {
					$fail_deactivate = true;
				}
			}
			if ( isset( $fail_activate ) && $fail_activate == true ) {
				$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_deactivated_fail_some' ).'</div>' );
			} else {
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_deactivated' ).'</div>' );
			}
			unset( $fail_deactivate, $result );
		} elseif ( $act == 'del' ) {
			// check permission
			if ( $this->account_model->check_admin_permission( 'modules_manage_perm', 'modules_delete_perm' ) != true ) {redirect( 'site-admin' );}
			$delete_fail = false;
			foreach ( $id as $an_id ) {
				$result = $this->modules_model->delete_a_module( $an_id );
				if ( $result === false ) {
					$delete_fail = true;
				}
			}
			if ( $delete_fail == true ) {
				$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'modules_delete_fail_some' ).'</div>' );
			} else {
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'modules_deleted' ).'</div>' );
			}
			unset( $delete_fail, $result );
		}
		// go back
		$this->load->library( 'user_agent' );
		if ( $this->agent->is_referral() ) {
			redirect( $this->agent->referrer() );
		} else {
			redirect( 'site-admin/module' );
		}
	}// process_bulk
	

}

