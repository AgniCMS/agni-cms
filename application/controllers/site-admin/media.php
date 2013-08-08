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
 
class media extends admin_controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('media_model'));
		
		// load helper
		$this->load->helper(array('date', 'file', 'form'));
		
		// load library
		$this->load->library(array('media_filesys'));
		$this->media_filesys->base_dir = $this->config->item('agni_upload_path').'media';
		
		// load language
		$this->lang->load('media');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array(
			'media_perm' => 
				array(
					'media_viewall_perm', 
					'media_manage_folder',
					'media_upload_perm', 
					'media_copy_perm',
					'media_edit_other_perm',
					'media_edit_own_perm', 
					'media_delete_other_perm',
					'media_delete_own_perm' 
				) 
			);
	}// _define_permission
	
	
	public function ajax_delete_folder() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_manage_folder') != true) {redirect('site-admin/media');}
		// check both permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_delete_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_delete_other_perm') != true) {redirect('site-admin/media');}
		
		if (!$this->input->is_ajax_request()) {redirect('site-admin/media');}
		
		$folder = $this->input->post('folder').'/';
		
		if (!$this->media_filesys->is_over_limit_base($this->media_filesys->base_dir, $folder)) {
			// recursive delete folders and files in it.
			$this->media_model->deleteMediaFolder($folder);
			
			$output['result'] = true;
		} else {
			$output['result'] = false;
		}
		
		// done, send output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_delete_folder
	
	
	public function ajax_crop() 
	{
		// check both permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// check that it is ajax request.
		if ($this->input->is_ajax_request()) {
			$file_id = trim($this->input->post('file_id'));
			
			// open db for edit and check permission (own, other)
			$data['file_id'] = $file_id;
			$row = $this->media_model->getFileDataDb($data);
			unset($data);
			if ($row == null) {
				return false;
			}
			
			// check permissions-----------------------------------------------------------
			if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') === false && $row->account_id == $my_account_id) {
				// user has NO permission to edit own and editing own.
				unset($row, $my_account_id);
				// flash error permission message
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_yours')
					)
				);
				redirect('site-admin/media');
			} elseif ($this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') === false && $row->account_id != $my_account_id) {
				// user has NO permission to edit others and editing others.
				unset($row, $my_account_id);
				// flash error permission message
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_others')
					)
				);
				redirect('site-admin/media');
			} 
			// end check permissions-----------------------------------------------------------
			
			// if cropping image
			if (strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png') {
				$width = trim($this->input->post('crop_w'));
					if (!is_numeric($width)) {return false;}
				$height = trim($this->input->post('crop_h'));
					if (!is_numeric($height)) {return false;}
				$crop_x1 = trim($this->input->post('crop_x1'));
					if (!is_numeric($crop_x1)) {return false;}
				$crop_y1 = trim($this->input->post('crop_y1'));
					if (!is_numeric($crop_y1)) {return false;}
				$crop_x2 = trim($this->input->post('crop_x2'));
					if (!is_numeric($crop_x2)) {return false;}
				$crop_y2 = trim($this->input->post('crop_y2'));
					if (!is_numeric($crop_y2)) {return false;}
				
				// calculate memory limit usage for resize image (actually it is cropping image)
				if ($this->media_model->checkMemAvailbleForResize($row->file, $width, $height)) {
					// crop using CI's image library.
					$this->load->library('image_lib');
					$config['source_image'] = $row->file;
					$config['quality'] = '100%';
					$config['width'] = $width;
					$config['height'] = $height;
					$config['maintain_ratio'] = false;
					$config['x_axis'] = $crop_x1;
					$config['y_axis'] = $crop_y1;
					$this->image_lib->initialize($config);
					unset($config);
					
					// if crop not success
					if (!$this->image_lib->crop()) {
						$output['result'] = false;
						$output['form_status'] = 'error';
						$output['form_status_message'] = $this->image_lib->display_errors();
					} else {
						// crop success.
						// update file size in db
						$size = get_file_info($row->file, 'size');
						$this->db->set('file_size', $size['size']);
						$this->db->where('file_id', $file_id);
						$this->db->update('files');

						// done.
						$output['result'] = true;
						$output['form_status'] = 'success';
						$output['form_status_message'] = $this->lang->line('media_crop_success');
						$output['croped_img'] = base_url($row->file.'?'.time());
					}
				} else {
					$memory_limit = ((int) ini_get('memory_limit') * 1024) * 1024;
					$require_mem = $this->media_model->checkMemAvailbleForResize($row->file, $width, $height, true);
					$output['result'] = false;
					$output['form_status'] = 'error';
					$output['form_status_message'] = sprintf($this->lang->line('media_crop_memory_exceed_limit'), $memory_limit, $require_mem);
				}
				
				// send data to output and done.
				$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
				$this->output->set_header('Pragma: no-cache');
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode($output));
			} else {
				log_message('error', 'The file that trying to crop is not image. '.$row->file);
				return false;
			}
		}
	}// ajax_crop
	
	
	public function ajax_new_folder() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_manage_folder') != true) {redirect('site-admin/media');}
		
		if (!$this->input->is_ajax_request()) {redirect('site-admin/media');}
		
		$current_path = trim($this->input->post('current_path'));
		$folder_name = trim($this->input->post('folder_name'));
		
		if ($this->media_filesys->is_over_limit_base($this->media_filesys->base_dir, $current_path.'/') === true) {
			$result = 'Hack attempt!';
		} else {
			$result = $this->media_filesys->create_folder($current_path.'/'.$folder_name, $folder_name);
		}
		
		if ($result === true) {
			$output['result'] = true;
		} else {
			$output['result'] = false;
			$output['result_text'] = $result;
		}
		
		unset($current_path, $folder_name, $result);
		
		// done, send output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_new_folder
	
	
	public function ajax_rename_folder() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_manage_folder') != true) {redirect('site-admin/media');}
		
		if (!$this->input->is_ajax_request()) {redirect('site-admin/media');}
		
		$current_path = trim($this->input->post('current_path'));
		$current_folder = trim($this->input->post('current_folder'));
		$folder_new_name = trim($this->input->post('folder_new_name'));
		
		$result = $this->media_filesys->renameFolder($current_path, $current_folder, $folder_new_name);
		
		if ($result === true) {
			$output['result'] = true;
			// rename in db too
			$this->media_model->renameFolderDb($current_path, $current_folder, $folder_new_name);
		} else {
			$output['result'] = false;
			$output['result_text'] = $result;
		}
		
		unset($current_folder, $current_path, $folder_new_name, $result);
		
		// done, send output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_rename_folder
	
	
	public function ajax_resize() 
	{
		// check both permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// check that it is ajax request.
		if ($this->input->is_ajax_request()) {
			$file_id = trim($this->input->post('file_id'));
			
			// open db for edit and check permission (own, other)
			$data['file_id'] = $file_id;
			$row = $this->media_model->getFileDataDb($data);
			unset($data);
			if ($row == null) {
				redirect('site-admin/media');
			}
			
			// check permissions-----------------------------------------------------------
			if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') === false && $row->account_id == $my_account_id) {
				// user has NO permission to edit own and editing own.
				unset($row, $my_account_id);
				// flash error permission message
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_yours')
					)
				);
				redirect('site-admin/media');
			} elseif ($this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') === false && $row->account_id != $my_account_id) {
				// user has NO permission to edit others and editing others.
				unset($row, $my_account_id);
				// flash error permission message
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'error',
						'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_others')
					)
				);
				redirect('site-admin/media');
			}
			// end check permissions-----------------------------------------------------------
			
			if (strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png') {
				$width = trim($this->input->post('width'));
					if (!is_numeric($width)) {return false;}
				$height = trim($this->input->post('height'));
					if (!is_numeric($height)) {return false;}
				
				// calculate memory limit usage for resize image
				if ($this->media_model->checkMemAvailbleForResize($row->file, $width, $height)) {
					// resize
					$this->load->library('vimage', $row->file);
					$this->vimage->resize_no_ratio($width, $height);
					$this->vimage->save('', $row->file);
					
					// update file size in db
					$size = get_file_info($row->file, 'size');
					$this->db->set('file_size', $size['size']);
					$this->db->where('file_id', $file_id);
					$this->db->update('files');
					
					// done.
					$output['result'] = true;
					$output['form_status'] = 'success';
					$output['form_status_message'] = $this->lang->line('media_resize_success');
					$output['resized_img'] = base_url($row->file.'?'.time());
				} else {
					$memory_limit = ((int) ini_get('memory_limit') * 1024) * 1024;
					$require_mem = $this->media_model->checkMemAvailbleForResize($row->file, $width, $height, true);
					$output['result'] = false;
					$output['form_status'] = 'error';
					$output['form_status_message'] = sprintf($this->lang->line('media_resize_memory_exceed_limit'), $memory_limit, $require_mem);
				}
				
				// send data to output and done.
				$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
				$this->output->set_header('Pragma: no-cache');
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode($output));
			} else {
				log_message('error', 'The file that trying to resize is not image. '.$row->file);
				return false;
			}
		}
	}// ajax_resize
	
	
	public function copy($file_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_copy_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// open db for copy files and info
		$data['file_id'] = $file_id;
		$row = $this->media_model->getFileDataDb($data);
		unset($data);
		
		if ($row == null) {
			redirect('site-admin/media');
		}
		
		// copy file processes.------------------------------------------------------------------------------------------
		$file = explode('.', $row->file);
		
		// cut only name.
		$file_name = '';
		for ($i = 0; $i < count($file)-1; $i++) {
			$file_name .= $file[$i];
			if ($i < (count($file)-2)) {
				$file_name .= '.';
			}
		}
		
		// cut only file ext.
		$file_ext = '';
		if (isset($file[count($file)-1])) {
			$file_ext = '.'.$file[count($file)-1];
		}
		
		// loop new name + num until not found.
		$i = 1;
		$found = true;
		do {
			// set new name.
			$new_file_name = $file_name.'('.$i.')';
			if (file_exists($new_file_name.$file_ext)) {
				$found = true;
				if ($i > 1000) {
					// prevent cpu heat for too many copy.
					$this->load->helper('string');
					$file_name = $file_name.'-'.random_string('alnum', 3).time();
					$found = false;
				}
			} else {
				$file_name = $new_file_name;
				$found = false;
			}
			$i++;
		}while($found === true);
		
		// copy file
		copy($row->file, $file_name.$file_ext);
		//-----------------------------------------------------------------------------------------------------------------
		
		// get new file name only
		$file = explode('/', $file_name);
		$file_name_only = $file[count($file)-1];
		
		// copy info
		$data['account_id'] = $my_account_id;
		$data['folder'] = $row->folder;
		$data['file'] = $file_name.$file_ext;
		$data['file_name'] = $file_name_only.$file_ext;
		$data['file_original_name'] = $row->file_original_name;
		$data['file_client_name'] = $row->file_client_name;
		$data['file_mime_type'] = $row->file_mime_type;
		$data['file_ext'] = $row->file_ext;
		$data['file_size'] = $row->file_size;
		$data['media_name'] = $row->media_name;
		$data['media_keywords'] = $row->media_keywords;
		$this->media_model->addDataOnly($data);
		unset($data);
		
		// done
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/media');
		}
	}// copy
	
	
	public function edit($file_id = '') 
	{
		// check both permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// open db for edit and check permission (own, other)
		$data['file_id'] = $file_id;
		$row = $this->media_model->getFileDataDb($data);
		unset($data);
		if ($row == null) {
			redirect('site-admin/media');
		}
		
		// check permissions-----------------------------------------------------------
		if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') === false && $row->account_id == $my_account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_yours')
				)
			);
			redirect('site-admin/media');
		} elseif ($this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') === false && $row->account_id != $my_account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('media_you_have_no_permission_edit_others')
				)
			);
			redirect('site-admin/media');
		}
		// end check permissions-----------------------------------------------------------
		
		// 
		$output['row'] = $row;
		$output['media_name'] = $row->media_name;
		$output['media_description'] = htmlspecialchars($row->media_description, ENT_QUOTES, config_item('charset'));
		$output['media_keywords'] = $row->media_keywords;
		
		// save action
		if ($this->input->post()) {
			
			$data['file_id'] = $file_id;
			$data['media_name'] = htmlspecialchars(trim($this->input->post('media_name', true)), ENT_QUOTES, config_item('charset'));
				if ($data['media_name'] == null) {$data['media_name'] = null;}
			$data['media_description'] = trim($this->input->post('media_description', true));
				if ($data['media_description'] == null) {$data['media_description'] = null;}
			$data['media_keywords'] = htmlspecialchars(trim($this->input->post('media_keywords', true)), ENT_QUOTES, config_item('charset'));
				if ($data['media_keywords'] == null) {$data['media_keywords'] = null;}
			
			// update to db
			$result = $this->media_model->edit($data);
			if ($result === true) {
				$this->load->library('session');
				$this->session->set_flashdata(
					'form_status',
					array(
						'form_status' => 'success',
						'form_status_message' => $this->lang->line('admin_saved')
					)
				);
				redirect('site-admin/media');
			}
			
			// re-populate form
			$output['media_name'] = $data['media_name'];
			$output['media_description'] = htmlspecialchars($data['media_description'], ENT_QUOTES, config_item('charset'));
			$output['media_keywords'] = $data['media_keywords'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('media_media'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
		$this->output->set_header('Pragma: no-cache');
		$this->generate_page('site-admin/templates/media/media_e_view', $output);
	}// edit
	
	
	public function get_img($file_id = '', $return_element = 'img') 
	{
		echo $this->media_model->get_img($file_id, $return_element);
		return ;
	}// get_img
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_viewall_perm') != true) {redirect('site-admin');}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$output['my_account_id'] = $ca_account['id'];
		unset($ca_account);
		
		// get values
		$output['filter'] = trim($this->input->get('filter', true));
		$output['filter_val'] = trim($this->input->get('filter_val', true));
		$output['q'] = htmlspecialchars(trim($this->input->get('q', true)), ENT_QUOTES, config_item('charset'));
		$output['orders'] = trim($this->input->get('orders', true));
		$output['cur_sort'] = $this->input->get('sort', true);
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'desc' ? 'asc' : 'desc');
		
		$current_path = ($this->input->get('current_path') != null ? rtrim($this->input->get('current_path'), '/') : $this->media_filesys->base_dir);
		$current_path = ($this->media_filesys->is_over_limit_base($this->media_filesys->base_dir, $current_path) ? $this->media_filesys->base_dir : $current_path);
		$output['current_path'] = $current_path;
		
		$output['output'] = $output;// send all values to $output array in views. this is very usefull in function.
		
		// list folders
		$this->media_filesys->current_path = $current_path;
		$output['list_folder'] = $this->media_filesys->list_dir_and_sub();
		
		// list item
		$data['folder'] = $current_path.'/';
		$data['list_for'] = 'admin';
		$output['list_item'] = $this->media_model->listMedia($data);
		unset($data);
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('media_media'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ($this->input->is_ajax_request()) {
			$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
			$this->output->set_header('Pragma: no-cache');
			$this->load->view('site-admin/templates/media/media_ajax_list_view', $output);
			return true;
		} else {
			$this->generate_page('site-admin/templates/media/media_view', $output);
		}
	}// index
	
	
	public function move_file($ids = '') 
	{
		// check both permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_edit_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		if ($ids == null) {
			$ids = $this->input->post('id');
		}
		
		if (!is_array($ids) || empty($ids)) {
			redirect('site-admin/media');
		}
		
		// check permission each file and remove unallowed one.
		foreach ($ids as $key => $id) {
			$data['file_id'] = $id;
				$row = $this->media_model->getFileDataDb($data);
				unset($data);
				
				// file not found in db, skip it.
				if ($row == null) {continue;}
				
				// check permissions-----------------------------------------------------------
				if ($this->account_model->checkAdminPermission('media_perm', 'media_delete_own_perm') === false && $row->account_id == $my_account_id) {
					// user has NO permission to edit own and editing own.
					unset($row, $my_account_id, $ids[$key]);
					$cannot_move_some_files = true;
					continue;
				} elseif ($this->account_model->checkAdminPermission('media_perm', 'media_delete_other_perm') === false && $row->account_id != $my_account_id) {
					// user has NO permission to edit others and editing others.
					unset($row, $my_account_id, $ids[$key]);
					$cannot_move_some_files = true;
					continue;
				}
				// end check permissions-----------------------------------------------------------
		}
		
		// method post, move action
		if ($this->input->post()) {
			$data['ids'] = $this->input->post('id');
			$data['target_folder'] = trim($this->input->post('target_folder'));
			
			if ($data['target_folder'] == null) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $this->lang->line('media_please_select_target_folder');
			} else {
				$result = $this->media_model->moveMedia($data);
				
				if ($result === true) {
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('media_files_moved_successfully')
						)
					);
					redirect('site-admin/media');
				}
			}
			
			$output['target_folder'] = $data['target_folder'];
		}
		
		$output['list_folder'] = $this->media_filesys->list_dir_and_sub();
		$output['ids'] = $id;
		
		// get files information from db.
		foreach ($ids as $key => $id) {
			$this->db->or_where('file_id', $id);
		}
		$query = $this->db->get('files');
		$output['files'] = $query->result();
		
		if (isset($cannot_move_some_files) && $cannot_move_some_files === true) {
			$output['form_status'] = 'error';
			$output['form_status_message'] = $this->lang->line('media_unable_to_move_some_file_due_to_system_permission');
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('media_media'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/media/media_move_file_view', $output);
	}// move_file
	
	
	public function popup() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_viewall_perm') != true) {return null;}
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$output['my_account_id'] = $ca_account['id'];
		unset($ca_account);
		
		// get values
		$output['filter'] = trim($this->input->get('filter', true));
		$output['filter_val'] = trim($this->input->get('filter_val', true));
		$output['q'] = htmlspecialchars(trim($this->input->get('q', true)), ENT_QUOTES, config_item('charset'));
		$output['orders'] = trim($this->input->get('orders', true));
		$output['cur_sort'] = $this->input->get('sort', true);
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'desc' ? 'asc' : 'desc');
		
		$current_path = ($this->input->get('current_path') != null ? rtrim($this->input->get('current_path'), '/') : $this->media_filesys->base_dir);
		$current_path = ($this->media_filesys->is_over_limit_base($this->media_filesys->base_dir, $current_path) ? $this->media_filesys->base_dir : $current_path);
		$output['current_path'] = $current_path;
		
		$output['output'] = $output;// send all values to $output array in views. this is very usefull in function.
		
		// list folders
		$this->media_filesys->current_path = $current_path;
		$output['list_folder'] = $this->media_filesys->list_dir_and_sub();
		
		// list item
		$data['folder'] = $current_path.'/';
		$data['list_for'] = 'admin';
		$output['list_item'] = $this->media_model->listMedia($data);
		unset($data);
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('media_media'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		if ($this->input->is_ajax_request()) {
			$this->output->set_header('Cache-Control: no-store, no-cache, must-revalidate');
			$this->output->set_header('Pragma: no-cache');
			$this->load->view('site-admin/templates/media/media_popup_ajax_list_view', $output);
			return true;
		} else {
			$this->load->view('site-admin/templates/media/media_popup_view', $output);
		}
	}// popup
	
	
	public function process_bulk() 
	{
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// get file id and action
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/media');}
		$act = trim($this->input->post('act'));
		
		// do action.
		if ($act == 'del') {
			// check both permission
			if ($this->account_model->checkAdminPermission('media_perm', 'media_delete_own_perm') != true && $this->account_model->checkAdminPermission('media_perm', 'media_delete_other_perm') != true) {redirect('site-admin');}
			
			foreach ($id as $an_id) {
				$data['file_id'] = $an_id;
				$row = $this->media_model->getFileDataDb($data);
				unset($data);
				
				// file not found in db, skip it.
				if ($row == null) {continue;}
				
				// check permissions-----------------------------------------------------------
				if ($this->account_model->checkAdminPermission('media_perm', 'media_delete_own_perm') === false && $row->account_id == $my_account_id) {
					// user has NO permission to edit own and editing own.
					unset($row, $my_account_id);
					continue;
				} elseif ($this->account_model->checkAdminPermission('media_perm', 'media_delete_other_perm') === false && $row->account_id != $my_account_id) {
					// user has NO permission to edit others and editing others.
					unset($row, $my_account_id);
					continue;
				}
				// end check permissions-----------------------------------------------------------
				
				$this->media_model->delete($an_id);
			}
		} elseif ($act == 'move_to_folder') {
			return $this->move_file($id);
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/media');
		}
	}// process_bulk
	
	
	public function upload() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('media_perm', 'media_upload_perm') != true) {redirect('site-admin');}
		
		// upload
		$upload_result = $this->media_model->uploadMedia();
		
		// fix non utf-8 in browsers.
		echo '<!DOCTYPE html>
			<html>
			<head>
			<title></title>
			<meta http-equiv="Content-type" content="text/html; charset='.config_item('charset').'" />';
		
		//
		if ($upload_result === true) {
			echo '<script type="text/javascript">window.parent.upload_status(\'<div class="txt_success alert alert-success">'.$this->lang->line("media_upload_complete").'</div>\');</script>';
		} else {
			echo '<script type="text/javascript">window.parent.upload_status(\'<div class="txt_error alert alert-error">'.$upload_result.'</div>\');</script>';
		}
		
		echo '</head><body></body></html>';
	}// upload
	
	
}

// EOF