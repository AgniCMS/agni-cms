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
 
class comment extends admin_controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('comments_model'));
		
		// load helper
		$this->load->helper(array('date', 'form'));
		
		// load language
		$this->lang->load('comment');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array(
				'comment_perm' =>
					array(
						'comment_viewall_perm',
						'comment_approve_unapprove_perm',
						'comment_edit_own_perm',
						'comment_edit_other_perm',
						'comment_delete_own_perm',
						'comment_delete_other_perm',
						'comment_allowpost_perm',
						'comment_nomoderation_perm'
					)
		);
	}// _define_permission
	
	
	public function edit($comment_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm') != true && $this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// open comments table for check permission and edit.
		$data['comment_id'] = $comment_id;
		$comment = $this->comments_model->getCommentDataDb($data);
		if ($comment == null) {redirect('site-admin/comment');}// not found
		
		$row = $comment;
		unset($comment);
		
		// check permissions-----------------------------------------------------------
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_own_perm') === false && $row->account_id == $my_account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_edit_yours')
				)
			);
			redirect('site-admin/comment');
		} elseif ($this->account_model->checkAdminPermission('comment_perm', 'comment_edit_other_perm') === false && $row->account_id != $my_account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('comment_you_have_no_permission_edit_others')
				)
			);
			redirect('site-admin/comment');
		}
		// end check permissions-----------------------------------------------------------
		
		// set values for edit
		$output['row'] = $row;
		$output['subject'] = $row->subject;
		$output['name'] = $row->name;
		$output['comment_body_value'] = $row->comment_body_value;
		$output['email'] = $row->email;
		$output['homepage'] = $row->homepage;
		
		// save action
		if ($this->input->post()) {
			
			// store data for comments table
			$data['comment_id'] = $comment_id;
			$data['name'] = htmlspecialchars(trim($this->input->post('name')), ENT_QUOTES, config_item('charset'));
			$data['subject'] = htmlspecialchars(trim($this->input->post('subject')), ENT_QUOTES, config_item('charset'));
			$data['comment_body_value'] = trim($this->input->post('comment_body_value', true));
				if ($data['subject'] == null) {$data['subject'] = mb_strimwidth(strip_tags($this->input->post('comment_body_value')), 0, 70, '...');}
			$data['email'] = trim($this->input->post('email'));
				if ($data['email'] == null) {$data['email'] = null;}
			$data['homepage'] = strip_tags(trim($this->input->post('homepage')));
				if ($data['homepage'] == null) {$data['homepage'] = null;} else {$data['homepage'] = prep_url($data['homepage']);}
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('name', 'lang:comment_name', 'trim|required|xss_clean');
			$this->form_validation->set_rules('comment_body_value', 'lang:comment_comment', 'trim|required|xss_clean');
			$this->form_validation->set_rules('email', 'lang:comment_email', 'trim|valid_email|xss_clean');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save result
				$result = $this->comments_model->edit($data);
				if ($result === true) {
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					
					// go back
					$this->load->library('user_agent');
					if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
						redirect($this->agent->referrer());
					} else {
						redirect('site-admin/comment');
					}
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('comment_comment'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/comment/comment_e_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('comment_perm', 'comment_viewall_perm') != true) {redirect('site-admin');}
		
		// sort, orders, search for views.
		$output['orders'] = strip_tags(trim($this->input->get('orders')));
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'desc' ? 'asc' : 'desc');
		$output['q'] = htmlspecialchars(trim($this->input->get('q')), ENT_QUOTES, config_item('charset'));
		$output['filter'] = htmlspecialchars(trim($this->input->get('filter')), ENT_QUOTES, config_item('charset'));
		$output['filter_val'] = htmlspecialchars(trim($this->input->get('filter_val')), ENT_QUOTES, config_item('charset'));
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list item
		if ($this->input->get('orders') == null && $this->input->get('sort') == null) {
			$_GET['orders'] = 'comment_id';
			$_GET['sort'] = 'desc';
		}
		$output['list_item'] = $this->comments_model->listComment('', 'flat', 'admin');
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('comment_comment'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/comment/comment_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		//
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/comment');}
		
		$act = trim($this->input->post('act'));
		
		if ($act == 'approve') {
			// check permission
			if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_approve_unapprove_perm')) {redirect('site-admin/comment');}
			foreach ($id as $an_id) {
				$data['comment_id'] = $an_id;
				$data['comment_status'] = '1';
				$this->comments_model->edit($data);
			}
		} elseif ($act == 'unapprove') {
			// check permission
			if (!$this->account_model->checkAdminPermission('comment_perm', 'comment_approve_unapprove_perm')) {redirect('site-admin/comment');}
			foreach ($id as $an_id) {
				$data['comment_id'] = $an_id;
				$data['comment_status'] = '0';
				$this->comments_model->edit($data);
			}
		} elseif ($act == 'del') {
			$confirm = $this->input->post('confirm');
			if ($confirm == 'yes') {
				// confirmed delete.
				// check permission
				if ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm') != true && $this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm') != true) {redirect('site-admin/comment');}
				
				foreach ($id as $an_id) {
					$this->db->where('comment_id', $an_id);
					$query = $this->db->get('comments');
					
					// comment not found? skip it.
					if ($query->num_rows() <= 0) {$query->free_result(); continue;}
					
					$row = $query->row();
					$query->free_result();
					
					// check permissions-----------------------------------------------------------
					if ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_own_perm') === false && $row->account_id == $my_account_id) {
						// user has NO permission to edit own and editing own.
						unset($row, $my_account_id, $query);
						continue;
					} elseif ($this->account_model->checkAdminPermission('comment_perm', 'comment_delete_other_perm') === false && $row->account_id != $my_account_id) {
						// user has NO permission to edit others and editing others.
						unset($row, $my_account_id, $query);
						continue;
					} 
					// end check permissions-----------------------------------------------------------
					
					// delete
					$this->comments_model->delete($an_id);
					
					// update total comment in posts table
					$this->load->model('posts_model');
					$this->posts_model->updateTotalComment($row->post_id);
				}
			} else {
				// show confirm delete view
				$output['act'] = $act;
				$output['input_ids'] = '';
				
				foreach ($this->input->post('id') as $an_id) {
					$output['input_ids'] .= '<input type="hidden" name="id[]" value="'.$an_id.'" />';
					$data['comment_id'] = $an_id;
					$comment = $this->comments_model->getCommentDataDb($data);
					if ($comment != null) {
						$row = $comment;
						$output['list_comments'][$row->comment_id]['subject'] = $row->subject;
						$output['list_comments'][$row->comment_id]['comment_body_value'] = $row->comment_body_value;
					}
				}
				
				// head tags output ##############################
				$output['page_title'] = $this->html_model->gen_title($this->lang->line('comment_comment'));
				// meta tags
				// link tags
				// script tags
				// end head tags output ##############################
				
				// output
				$this->generate_page('site-admin/templates/comment/comment_delete_view', $output);
				
				return;
			}
		} else {
			// some other action? send to modules plug to do it.
			$this->modules_plug->do_action('comment_admin_process', array('act' => $act, 'id' => $id));
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			if ($this->agent->referrer() != current_url()) {
				redirect($this->agent->referrer());
			} else {
				redirect('site-admin/comment');
			}
		} else {
			redirect('site-admin/comment');
		}
		
	}// process_bulk
	
	
}

// EOF