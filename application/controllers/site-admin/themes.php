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

class themes extends admin_controller {

	
	function __construct() {
		parent::__construct();
		
		// load model
		$this->load->model( array( 'themes_model' ) );
		
		// load helper
		$this->load->helper( array( 'form' ) );
		
		// load lang
		$this->lang->load( 'themes' );
	}// __construct
	
	
	function _define_permission() {
		return array( 'themes_manage_perm' => array( 'themes_viewall_perm', 'themes_add_perm', 'themes_enable_disable_perm', 'themes_set_default_perm', 'themes_delete_perm' ) );
	}// _define_permission
	
	
	function add() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_add_perm' ) != true ) {redirect( 'site-admin' );}
		
		// save action.
		if ( $this->input->post() ) {
			$result = $this->themes_model->add_theme();
			
			if ( $result === true ) {
				// load session
				$this->load->library( 'session' );
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.lang( 'themes_added' ).'</div>' );
				
				redirect( 'site-admin/themes' );
			} else {
				$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
			}
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'themes_manager' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		$this->generate_page( 'site-admin/templates/themes/themes_add_view', $output );
	}// add
	
	
	function defaultadmin() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_set_default_perm' ) != true ) {redirect( 'site-admin' );}
		
		$theme_system_name = trim( $this->input->post( 'theme_system_name' ) );
		
		// set default
		$result = $this->themes_model->set_default( $theme_system_name, 'admin' );
		
		// read theme data
		$pdata = $this->themes_model->read_theme_metadata( $theme_system_name.'/'.$theme_system_name.'.info' );
		
		// load session
		$this->load->library( 'session' );
		if ( $result == true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.sprintf( lang( 'themes_default_done' ), ( $pdata['name'] != null ? $pdata['name'] : $theme_system_name ) ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'themes_default_fail' ).'</div>' );
		}
		
		redirect( 'site-admin/themes' );
	}// defaultadmin
	
	
	function defaults( $theme_system_name = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_set_default_perm' ) != true ) {redirect( 'site-admin' );}
		
		$result = $this->themes_model->set_default( $theme_system_name );
		
		// read theme data
		$pdata = $this->themes_model->read_theme_metadata( $theme_system_name.'/'.$theme_system_name.'.info' );
		
		// load session
		$this->load->library( 'session' );
		if ( $result == true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.sprintf( lang( 'themes_default_done' ), ( $pdata['name'] != null ? $pdata['name'] : $theme_system_name ) ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'themes_default_fail' ).'</div>' );
		}
		
		redirect( 'site-admin/themes' );
	}// defaults
	
	
	function delete( $theme_system_name = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_delete_perm' ) != true ) {redirect( 'site-admin' );}
		
		// read theme data
		$pdata = $this->themes_model->read_theme_metadata( $theme_system_name.'/'.$theme_system_name.'.info' );
		
		$output['theme_name'] = ( $pdata['name'] != null ? $pdata['name'] : $theme_system_name );
		
		// delete action
		if ( $this->input->post() ) {
			if ( $this->input->post( 'confirm' ) == 'yes' ) {
				$result = $this->themes_model->delete_theme( $theme_system_name );
				
				if ( $result === true ) {
					// load session
					$this->load->library( 'session' );
					$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.sprintf( lang( 'themes_deleted' ), $output['theme_name'] ).'</div>' );
					
					redirect( 'site-admin/themes' );
				} else {
					$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
				}
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'themes_manager' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page( 'site-admin/templates/themes/themes_del_view', $output );
	}// delete
	
	
	function disable( $theme_system_name = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_enable_disable_perm' ) != true ) {redirect( 'site-admin' );}
		
		$result = $this->themes_model->do_disable( $theme_system_name );
		
		$pdata = $this->themes_model->read_theme_metadata( $theme_system_name.'/'.$theme_system_name.'.info' );
		
		// load session
		$this->load->library( 'session' );
		if ( $result == true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.sprintf( lang( 'themes_disabled' ), ( $pdata['name'] != null ? $pdata['name'] : $theme_system_name ) ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'themes_disabled_fail' ).'</div>' );
		}
		
		redirect( 'site-admin/themes' );
	}// disable
	
	
	function enable( $theme_system_name = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_enable_disable_perm' ) != true ) {redirect( 'site-admin' );}
		
		$result = $this->themes_model->do_enable( $theme_system_name );
		
		// read theme data
		$pdata = $this->themes_model->read_theme_metadata( $theme_system_name.'/'.$theme_system_name.'.info' );
		
		// load session
		$this->load->library( 'session' );
		if ( $result == true ) {
			$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.sprintf( lang( 'themes_enabled' ), ( $pdata['name'] != null ? $pdata['name'] : $theme_system_name ) ).'</div>' );
		} else {
			$this->session->set_flashdata( 'form_status', '<div class="txt_error alert alert-error">'.lang( 'themes_enabled_fail' ).'</div>' );
		}
		
		redirect( 'site-admin/themes' );
	}// enable
	
	
	function index() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'themes_manage_perm', 'themes_viewall_perm' ) != true ) {redirect( 'site-admin' );}
		
		// load session for show last flashed session
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		
		// list enabled themes
		$output['list_enabled'] = $this->themes_model->list_enabled_themes();
		
		// list themes
		$output['list_item'] = $this->themes_model->list_all_themes();
		
		// default admin theme is...
		$output['theme_admin_name'] = '';
		$theme_system_name = $this->themes_model->get_default_theme( 'admin' );
		if ( $theme_system_name != null ) {
			$output['theme_admin_name'] = $theme_system_name;
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'themes_manager' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page( 'site-admin/templates/themes/themes_view', $output );
	}// index
	

}

