<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class urls extends admin_controller {
	
	
	function __construct() {
		parent::__construct();
		// load model
		$this->load->model( array( 'url_model' ) );
		// load helper
		$this->load->helper( array( 'form' ) );
		// load language
		$this->lang->load( 'urls' );
		// set model property
		$this->url_model->c_type = 'redirect';
	}// __construct
	
	
	function _define_permission() {
		return array( 'urls_perm' => array( 'urls_perm_view_all', 'urls_perm_add', 'urls_perm_edit', 'urls_perm_delete' ) );
	}// _define_permission
	
	
	function add() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'urls_perm', 'urls_perm_add' ) != true ) {redirect( 'site-admin' );}
		// preset value
		$output['redirect_code'] = 302;
		// post method. save action
		if ( $this->input->post() ) {
			$data['uri'] = trim( $this->input->post( 'uri' ) );
			$data['redirect_to'] = trim( $this->input->post( 'redirect_to' ) );
			$data['redirect_code'] = trim( $this->input->post( 'redirect_code' ) );
				if ( !is_numeric( $data['redirect_code'] ) ) {$data['redirect_code'] = 301;}
			// load form validation
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'uri', 'lang:urls_uri', 'trim|required|xss_clean' );
			$this->form_validation->set_rules( 'redirect_to', 'lang:urls_redirect_to', 'trim|required|xss_clean' );
			if ( $this->form_validation->run() == false ) {
				$output['form_status'] = validation_errors( '<div class="txt_error alert alert-error">', '</div>' );
			} else {
				// save
				$result = $this->url_model->add_redirect( $data );
				if ( isset( $result['result'] ) && $result['result'] === true ) {
					// load session library
					$this->load->library( 'session' );
					$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.$this->lang->line( 'admin_saved' ).'</div>' );
					redirect( 'site-admin/urls' );
				} else {
					$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
				}
			}
			// re-populate form
			$output['uri'] = htmlspecialchars( $data['uri'], ENT_QUOTES, config_item( 'charset' ) );
			$output['redirect_to'] = htmlspecialchars( $data['redirect_to'], ENT_QUOTES, config_item( 'charset' ) );
			$output['redirect_code'] = $data['redirect_code'];
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'urls_url_redirect' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page( 'site-admin/templates/urls/urls_ae_view', $output );
	}// add
	
	
	function ajax_check_uri() {
		if ( $this->input->post() && $this->input->is_ajax_request() ) {
			$uri = trim( $this->input->post( 'uri' ) );
			$nodupedit = trim( $this->input->post( 'nodupedit' ) );
			$nodupedit = ( $nodupedit == 'true' ? true : false );
			$id = intval( $this->input->post( 'id' ) );
			$output['input_uri'] = $this->url_model->nodup_uri( $uri, $nodupedit, $id );
			// output
			$this->output->set_content_type( 'application/json' );
			$this->output->set_output( json_encode( $output ) );
		}
	}// ajax_check_uri
	
	
	function edit( $alias_id = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'urls_perm', 'urls_perm_edit' ) != true ) {redirect( 'site-admin' );}
		// load data for edit
		$this->db->where( 'alias_id', $alias_id );
		$this->db->where( 'language', $this->lang->get_current_lang() );
		$query = $this->db->get( 'url_alias' );
		if ( $query->num_rows() <= 0 ) {
			$query->free_result();
			redirect( 'site-admin/urls' );
		}
		$row = $query->row();
		$query->free_result();
		// store data for edit view
		$output['alias_id'] = $alias_id;
		$output['row'] = $row;
		$output['uri'] = htmlspecialchars( $row->uri, ENT_QUOTES, config_item( 'charset' ) );
		$output['redirect_to'] = htmlspecialchars( $row->redirect_to, ENT_QUOTES, config_item( 'charset' ) );
		$output['redirect_code'] = $row->redirect_code;
		// post method. save action
		if ( $this->input->post() ) {
			$data['alias_id'] = $alias_id;
			$data['uri'] = trim( $this->input->post( 'uri' ) );
			$data['redirect_to'] = trim( $this->input->post( 'redirect_to' ) );
			$data['redirect_code'] = trim( $this->input->post( 'redirect_code' ) );
				if ( !is_numeric( $data['redirect_code'] ) ) {$data['redirect_code'] = 301;}
			// load form validation
			$this->load->library( 'form_validation' );
			$this->form_validation->set_rules( 'uri', 'lang:urls_uri', 'trim|required|xss_clean' );
			$this->form_validation->set_rules( 'redirect_to', 'lang:urls_redirect_to', 'trim|required|xss_clean' );
			if ( $this->form_validation->run() == false ) {
				$output['form_status'] = validation_errors( '<div class="txt_error alert alert-error">', '</div>' );
			} else {
				// save
				$result = $this->url_model->edit_redirect( $data );
				if ( isset( $result['result'] ) && $result['result'] === true ) {
					// load session library
					$this->load->library( 'session' );
					$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.$this->lang->line( 'admin_saved' ).'</div>' );
					redirect( 'site-admin/urls' );
				} else {
					$output['form_status'] = '<div class="txt_error alert alert-error">'.$result.'</div>';
				}
			}
			// re-populate form
			$output['uri'] = htmlspecialchars( $data['uri'], ENT_QUOTES, config_item( 'charset' ) );
			$output['redirect_to'] = htmlspecialchars( $data['redirect_to'], ENT_QUOTES, config_item( 'charset' ) );
			$output['redirect_code'] = $data['redirect_code'];
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'urls_url_redirect' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page( 'site-admin/templates/urls/urls_ae_view', $output );
	}// edit
	
	
	function index() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'urls_perm', 'urls_perm_view_all' ) != true ) {redirect( 'site-admin' );}
		// load session for flashdata
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		// sort and get values
		$output['sort'] = ($this->input->get( 'sort' ) == null || $this->input->get( 'sort' ) == 'asc' ? 'desc' : 'asc' );
		$output['q'] = htmlspecialchars( trim( $this->input->get( 'q' ) ), ENT_QUOTES, config_item( 'charset' ) );
		// list item
		$output['list_item'] = $this->url_model->list_item();
		if ( is_array( $output['list_item'] ) ) {
			$output['pagination'] = $this->pagination->create_links();
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'urls_url_redirect' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page( 'site-admin/templates/urls/urls_view', $output );
	}// index
	
	
	function multiple() {
		$id = $this->input->post( 'id' );
		$act = trim( $this->input->post( 'act' ) );
		if ( $act == 'del' ) {
			// check permission
			if ( $this->account_model->check_admin_permission( 'urls_perm', 'urls_perm_delete' ) != true ) {redirect( 'site-admin' );}
			if ( is_array( $id ) ) {
				foreach ( $id as $an_id ) {
					$this->url_model->delete_redirect( $an_id );
				}
			}
		}
		// go back
		$this->load->library( 'user_agent' );
		if ( $this->agent->is_referral() ) {
			redirect( $this->agent->referrer() );
		} else {
			redirect( 'site-admin/urls' );
		}
	}// multiple
	
	
}

// EOF