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
 
class media extends admin_controller {
	
	
	function __construct() {
		parent::__construct();
		
		// load model
		$this->load->model( array( 'media_model' ) );
		
		// load helper
		$this->load->helper( array( 'date', 'file', 'form' ) );
		
		// load language
		$this->lang->load( 'media' );
	}// __construct
	
	
	function _define_permission() {
		return array( 
			'media_perm' => 
				array( 
					'media_viewall_perm', 
					'media_upload_perm', 
					'media_copy_perm',
					'media_edit_other_perm',
					'media_edit_own_perm', 
					'media_delete_other_perm',
					'media_delete_own_perm' 
				) 
			);
	}// _define_permission
	
	
	function ajax_resize() {
		// check both permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) != true && $this->account_model->check_admin_permission( 'media_perm', 'media_edit_other_perm' ) != true ) {redirect( 'site-admin' );}
		
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$my_account_id = $ca_account['id'];
		unset( $ca_account );
		
		// check that it is ajax request.
		if ( $this->input->is_ajax_request() ) {
			$file_id = trim( $this->input->post( 'file_id' ) );
			
			// open db for edit and check permission (own, other)
			$data['file_id'] = $file_id;
			$row = $this->media_model->get_file_data_db( $data );
			unset( $data );
			if ( $row == null ) {
				redirect( 'site-admin/media' );
			}
			
			// check permissions-----------------------------------------------------------
			if ( $this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) && $row->account_id != $my_account_id ) {
				// this user has permission to edit own, but NOT editing own
				if ( !$this->account_model->check_admin_permission( 'media_perm', 'media_edit_other_perm' ) ) {
					// this user has NOT permission to edit other's, but editing other's
					unset( $row, $my_account_id );
					redirect( 'site-admin' );
				}
			} elseif ( !$this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) && $row->account_id == $my_account_id ) {
				// this user has NOT permission to edit own, but editing own.
				unset( $row, $my_account_id );
				redirect( 'site-admin' );
			}
			// end check permissions-----------------------------------------------------------
			
			if ( strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png' ) {
				$width = trim( $this->input->post( 'width' ) );
					if ( !is_numeric( $width ) ) {return false;}
				$height = trim( $this->input->post( 'height' ) );
					if ( !is_numeric( $height ) ) {return false;}
				
				// calculate memory limit usage for resize image
				if ( $this->media_model->checkMemAvailbleForResize( $row->file, $width, $height ) ) {
					// resize
					$this->load->library('vimage', $row->file );
					$this->vimage->resize_no_ratio( $width, $height );
					$this->vimage->save('', $row->file );
					
					// update file size in db
					$size = get_file_info( $row->file, 'size' );
					$this->db->set( 'file_size', $size['size'] );
					$this->db->where( 'file_id', $file_id );
					$this->db->update( 'files' );
					
					// done.
					$output['result'] = true;
					$output['form_status'] = '<div class="txt_success alert alert-success">'.$this->lang->line( 'media_resize_success' ).'</div>';
					$output['resized_img'] = base_url().$row->file.'?'.time();
				} else {
					$memory_limit = ((int) ini_get('memory_limit') * 1024) * 1024;
					$require_mem = $this->media_model->checkMemAvailbleForResize( $row->file, $width, $height, true );
					$output['result'] = false;
					$output['form_status'] = '<div class="txt_error alert alert-error">'.sprintf( $this->lang->line( 'media_resize_memory_exceed_limit' ), $memory_limit, $require_mem ).'</div>';
				}
				
				// send data to output and done.
				$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
				$this->output->set_header( 'Pragma: no-cache' );
				$this->output->set_content_type( 'application/json' );
				$this->output->set_output( json_encode( $output ) );
			} else {
				log_message( 'error', 'The file that trying to resize is not image. '.$row->file );
				return false;
			}
		}
	}// ajax_resize
	
	
	function copy( $file_id = '' ) {
		// check permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_copy_perm' ) != true ) {redirect( 'site-admin' );}
		
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$my_account_id = $ca_account['id'];
		unset( $ca_account );
		
		// open db for copy files and info
		$data['file_id'] = $file_id;
		$row = $this->media_model->get_file_data_db( $data );
		unset( $data );
		
		if ( $row == null ) {
			redirect( 'site-admin/media' );
		}
		
		// copy file processes.------------------------------------------------------------------------------------------
		$file = explode( '.', $row->file );
		
		// cut only name.
		$file_name = '';
		for ( $i = 0; $i < count($file)-1; $i++ ) {
			$file_name .= $file[$i];
			if ( $i < (count($file)-2) ) {
				$file_name .= '.';
			}
		}
		
		// cut only file ext.
		$file_ext = '';
		if ( isset( $file[count($file)-1] ) ) {
			$file_ext = '.'.$file[count($file)-1];
		}
		
		// loop new name + num until not found.
		$i = 1;
		$found = true;
		do {
			// set new name.
			$new_file_name = $file_name.'('.$i.')';
			if ( file_exists( $new_file_name.$file_ext ) ) {
				$found = true;
				if ( $i > 1000 ) {
					// prevent cpu heat for too many copy.
					$this->load->helper( 'string' );
					$file_name = $file_name.'-'.random_string( 'alnum', 3 ).time();
					$found = false;
				}
			} else {
				$file_name = $new_file_name;
				$found = false;
			}
			$i++;
		}while( $found === true );
		
		// copy file
		copy( $row->file, $file_name.$file_ext );
		//-----------------------------------------------------------------------------------------------------------------
		
		// get new file name only
		$file = explode( '/', $file_name );
		$file_name_only = $file[count($file)-1];
		
		// copy info
		$data['account_id'] = $my_account_id;
		$data['language'] = $row->language;
		$data['file'] = $file_name.$file_ext;
		$data['file_name'] = $file_name_only.$file_ext;
		$data['file_original_name'] = $row->file_original_name;
		$data['file_client_name'] = $row->file_client_name;
		$data['file_mime_type'] = $row->file_mime_type;
		$data['file_ext'] = $row->file_ext;
		$data['file_size'] = $row->file_size;
		$data['media_name'] = $row->media_name;
		$data['media_keywords'] = $row->media_keywords;
		$this->media_model->add_data_only( $data );
		unset( $data );
		
		// done
		// go back
		$this->load->library( 'user_agent' );
		if ( $this->agent->is_referral() ) {
			redirect( $this->agent->referrer() );
		} else {
			redirect( 'site-admin/media' );
		}
	}// copy
	
	
	function edit( $file_id = '' ) {
		// check both permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) != true && $this->account_model->check_admin_permission( 'media_perm', 'media_edit_other_perm' ) != true ) {redirect( 'site-admin' );}
		
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$my_account_id = $ca_account['id'];
		unset( $ca_account );
		
		// open db for edit and check permission (own, other)
		$data['file_id'] = $file_id;
		$row = $this->media_model->get_file_data_db( $data );
		unset( $data );
		if ( $row == null ) {
			redirect( 'site-admin/media' );
		}
		
		// check permissions-----------------------------------------------------------
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) && $row->account_id != $my_account_id ) {
			// this user has permission to edit own, but NOT editing own
			if ( !$this->account_model->check_admin_permission( 'media_perm', 'media_edit_other_perm' ) ) {
				// this user has NOT permission to edit other's, but editing other's
				unset( $row, $my_account_id );
				redirect( 'site-admin' );
			}
		} elseif ( !$this->account_model->check_admin_permission( 'media_perm', 'media_edit_own_perm' ) && $row->account_id == $my_account_id ) {
			// this user has NOT permission to edit own, but editing own.
			unset( $row, $my_account_id );
			redirect( 'site-admin' );
		}
		// end check permissions-----------------------------------------------------------
		
		// 
		$output['row'] = $row;
		$output['media_name'] = $row->media_name;
		$output['media_description'] = htmlspecialchars( $row->media_description, ENT_QUOTES, config_item( 'charset' ) );
		$output['media_keywords'] = $row->media_keywords;
		
		// save action
		if ( $this->input->post() ) {
			
			$data['file_id'] = $file_id;
			$data['media_name'] = htmlspecialchars( trim( $this->input->post( 'media_name', true ) ), ENT_QUOTES, config_item( 'charset' ) );
				if ( $data['media_name'] == null ) {$data['media_name'] = null;}
			$data['media_description'] = trim( $this->input->post( 'media_description', true ) );
				if ( $data['media_description'] == null ) {$data['media_description'] = null;}
			$data['media_keywords'] = htmlspecialchars( trim( $this->input->post( 'media_keywords', true ) ), ENT_QUOTES, config_item( 'charset' ) );
				if ( $data['media_keywords'] == null ) {$data['media_keywords'] = null;}
			
			// update to db
			$result = $this->media_model->edit( $data );
			if ( $result === true ) {
				$this->load->library( 'session' );
				$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">' . $this->lang->line( 'admin_saved' ) . '</div>' );
				redirect( 'site-admin/media' );
			}
			
			// re-populate form
			$output['media_name'] = $data['media_name'];
			$output['media_description'] = htmlspecialchars( $data['media_description'], ENT_QUOTES, config_item( 'charset' ) );
			$output['media_keywords'] = $data['media_keywords'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'media_media' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
		$this->output->set_header( 'Pragma: no-cache' );
		$this->generate_page( 'site-admin/templates/media/media_e_view', $output );
	}// edit
	
	
	function get_img( $file_id = '', $return_element = 'img' ) {
		echo $this->media_model->get_img( $file_id, $return_element );
		return ;
	}// get_img
	
	
	function index() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_viewall_perm' ) != true ) {redirect( 'site-admin' );}
		
		// load session for flashdata
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$output['my_account_id'] = $ca_account['id'];
		unset( $ca_account );
		
		// get values
		$output['filter'] = trim( $this->input->get( 'filter', true ) );
		$output['filter_val'] = trim( $this->input->get( 'filter_val', true ) );
		$output['q'] = htmlspecialchars( trim( $this->input->get( 'q', true ) ), ENT_QUOTES, config_item( 'charset' ) );
		$output['orders'] = trim( $this->input->get( 'orders', true ) );
		$output['cur_sort'] = $this->input->get( 'sort', true );
		$output['sort'] = ( $this->input->get( 'sort' ) == null || $this->input->get( 'sort' ) == 'desc' ? 'asc' : 'desc' );
		
		// list item
		$output['list_item'] = $this->media_model->list_item( 'admin' );
		if ( is_array( $output['list_item'] ) ) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'media_media' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ( $this->input->is_ajax_request() ) {
			$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			$this->output->set_header( 'Pragma: no-cache' );
			$this->load->view( 'site-admin/templates/media/media_ajax_list_view', $output );
			return true;
		} else {
			$this->generate_page( 'site-admin/templates/media/media_view', $output );
		}
	}// index
	
	
	function popup() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_viewall_perm' ) != true ) {return null;}
		
		// load session for flashdata
		$this->load->library( 'session' );
		$form_status = $this->session->flashdata( 'form_status' );
		if ( $form_status != null ) {
			$output['form_status'] = $form_status;
		}
		unset( $form_status );
		
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$output['my_account_id'] = $ca_account['id'];
		unset( $ca_account );
		
		// get values
		$output['filter'] = trim( $this->input->get( 'filter', true ) );
		$output['filter_val'] = trim( $this->input->get( 'filter_val', true ) );
		$output['q'] = htmlspecialchars( trim( $this->input->get( 'q', true ) ), ENT_QUOTES, config_item( 'charset' ) );
		$output['orders'] = trim( $this->input->get( 'orders', true ) );
		$output['cur_sort'] = $this->input->get( 'sort', true );
		$output['sort'] = ( $this->input->get( 'sort' ) == null || $this->input->get( 'sort' ) == 'desc' ? 'asc' : 'desc' );
		
		// list item
		$output['list_item'] = $this->media_model->list_item( 'admin' );
		if ( is_array( $output['list_item'] ) ) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'media_media' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ( $this->input->is_ajax_request() ) {
			$this->output->set_header( 'Cache-Control: no-store, no-cache, must-revalidate' );
			$this->output->set_header( 'Pragma: no-cache' );
			$this->load->view( 'site-admin/templates/media/media_popup_ajax_list_view', $output );
			return true;
		} else {
			$this->load->view( 'site-admin/templates/media/media_popup_view', $output );
		}
	}// popup
	
	
	function process_bulk() {
		// get account id
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$my_account_id = $ca_account['id'];
		unset( $ca_account );
		
		// get file id and action
		$id = $this->input->post( 'id' );
		if ( !is_array( $id ) ) {redirect( 'site-admin/media' );}
		$act = trim( $this->input->post( 'act' ) );
		
		// do action.
		if ( $act == 'del' ) {
			// check both permission
			if ( $this->account_model->check_admin_permission( 'media_perm', 'media_delete_own_perm' ) != true && $this->account_model->check_admin_permission( 'media_perm', 'media_delete_other_perm' ) != true ) {redirect( 'site-admin' );}
			
			foreach ( $id as $an_id ) {
				$data['file_id'] = $an_id;
				$row = $this->media_model->get_file_data_db( $data );
				unset( $data );
				
				// file not found in db, skip it.
				if ( $row == null ) {continue;}
				
				// check permissions-----------------------------------------------------------
				if ( $this->account_model->check_admin_permission( 'media_perm', 'media_delete_own_perm' ) && $row->account_id != $my_account_id ) {
					// this user has permission to delete own, but NOT delete own
					if ( !$this->account_model->check_admin_permission( 'media_perm', 'media_delete_other_perm' ) ) {
						// this user has NOT permission to delete other's, but deleting other's
						unset( $row );
						continue;
					}
				} elseif ( !$this->account_model->check_admin_permission( 'media_perm', 'media_delete_own_perm' ) && $row->account_id == $my_account_id ) {
					// this user has NOT permission to delete own, but deleting own.
					unset( $row );
					continue;
				}
				// end check permissions-----------------------------------------------------------
				
				$this->media_model->delete( $an_id );
			}
		}
		
		// go back
		$this->load->library( 'user_agent' );
		if ( $this->agent->is_referral() ) {
			redirect( $this->agent->referrer() );
		} else {
			redirect( 'site-admin/media' );
		}
	}// process_bulk
	
	
	function upload() {
		// check permission
		if ( $this->account_model->check_admin_permission( 'media_perm', 'media_upload_perm' ) != true ) {redirect( 'site-admin' );}
		
		// upload
		$upload_result = $this->media_model->upload_media();
		
		// fix non utf-8 in browsers.
		echo '<!DOCTYPE html>
			<html>
			<head>
			<title></title>
			<meta http-equiv="Content-type" content="text/html; charset='.config_item( 'charset' ).'" />';
		
		//
		if ( $upload_result === true ) {
			echo '<script type="text/javascript">window.parent.upload_status(\'<div class="txt_success alert alert-success">'.$this->lang->line("media_upload_complete").'</div>\');</script>';
		} else {
			echo '<script type="text/javascript">window.parent.upload_status(\'<div class="txt_error alert alert-error">'.$upload_result.'</div>\');</script>';
		}
		
		echo '</head><body></body></html>';
	}// upload
	
	
}

// EOF