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

class article extends admin_controller 
{

	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('posts_model', 'taxonomy_model'));
		
		// load helper
		$this->load->helper(array('category', 'date', 'form'));
		
		// load language
		$this->lang->load('post');
		
		// set post_type
		$this->posts_model->post_type = 'article';
	}// __construct
	
	
	public function _define_permission() 
	{
		return array(
				'post_article_perm' => 
					array(
						'post_article_viewall_perm', 
						'post_article_add_perm', 
						'post_article_publish_unpublish_perm', 
						'post_article_edit_own_perm', 
						'post_article_edit_other_perm', 
						'post_article_delete_own_perm', 
						'post_article_delete_other_perm', 
						'post_article_sort_perm',
						'post_revert_revision',
						'post_delete_revision'
					) 
			);
	}// _define_permission
	
	
	public function add() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_add_perm') != true) {redirect('site-admin');}
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
		// list categories for check
		$this->taxonomy_model->tax_type = 'category';
		$output['list_category'] = $this->taxonomy_model->listTaxTerm();
		
		// preset settings and values
		$output['post_comment'] = '1';
		$output['post_status'] = '1';
		
		// save action
		if ($this->input->post()) {
			
			$data_tax_index['tid'] = $this->input->post('tid');// categories
			$data_tax_index['tagid'] = $this->input->post('tagid');// tags
			
			// posts table
			$data_posts['theme_system_name'] = trim($this->input->post('theme_system_name'));
				if ($data_posts['theme_system_name'] == null) {$data_posts['theme_system_name'] = null;}
			$data_posts['post_name'] = htmlspecialchars(trim($this->input->post('post_name')), ENT_QUOTES, config_item('charset'));
			$data_posts['post_uri'] = trim($this->input->post('post_uri'));
			$data_posts['post_feature_image'] = trim($this->input->post('post_feature_image'));
				if ($data_posts['post_feature_image'] == null || !is_numeric($data_posts['post_feature_image'])) {$data_posts['post_feature_image'] = null;}
			$data_posts['post_comment'] = (int) $this->input->post('post_comment');
			$data_posts['post_status'] = (int) $this->input->post('post_status');
				if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm') != true) {$data_posts['post_status'] = '0';}
			$data_posts['post_add'] = time();
			$data_posts['post_add_gmt'] = local_to_gmt(time());
			$data_posts['post_update'] = time();
			$data_posts['post_update_gmt'] = local_to_gmt(time());
			$data_posts['post_publish_date'] = time();
			$data_posts['post_publish_date_gmt'] = local_to_gmt(time());
			$data_posts['meta_title'] = htmlspecialchars(trim($this->input->post('meta_title')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_title'] == null) {$data_posts['meta_title'] = null;}
			$data_posts['meta_description'] = htmlspecialchars(trim($this->input->post('meta_description')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_description'] == null) {$data_posts['meta_description'] = null;}
			$data_posts['meta_keywords'] = htmlspecialchars(trim($this->input->post('meta_keywords')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_keywords'] == null) {$data_posts['meta_keywords'] = null;}
			
			// posts table > content settings
			if ($this->input->post('content_show_title') == null && $this->input->post('content_show_time') == null && $this->input->post('content_show_author') == null) {
				$data_posts['content_settings'] = null;
			} else {
				$setting['content_show_title'] = $this->input->post('content_show_title');
				$setting['content_show_time'] = $this->input->post('content_show_time');
				$setting['content_show_author'] = $this->input->post('content_show_author');
				$data_posts['content_settings'] = serialize($setting);
				unset($setting);
			}
			
			// revision table
			$data_post_revision['header_value'] = trim($this->input->post('header_value'));
				if ($data_post_revision['header_value'] == null) {$data_post_revision['header_value'] = null;}
			$data_post_revision['body_value'] = trim($this->input->post('body_value'));
			$data_post_revision['body_summary'] = trim($this->input->post('body_summary'));
				if ($data_post_revision['body_summary'] == null) {$data_post_revision['body_summary'] = null;}
			//$data['new_revision'] = $this->input->post('new_revision');// no need new_revision setting while add.
			$data_post_revision['log'] = htmlspecialchars(trim($this->input->post('revision_log')), ENT_QUOTES, config_item('charset'));
				if ($data_post_revision['log'] == null) {$data_post_revision['log'] = null;}
			$data_post_revision['revision_date'] = time();
			$data_post_revision['revision_date_gmt'] = local_to_gmt(time());
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('post_name', 'lang:post_article_name', 'trim|required');
			$this->form_validation->set_rules('body_value', 'lang:post_content', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save result
				$result = $this->posts_model->add($data_posts, $data_post_revision, $data_tax_index);
				if ($result === true) {
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					redirect('site-admin/article');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output = array_merge($output, $data_posts);
			$output = array_merge($output, $data_post_revision);
			$output = array_merge($output, $data_tax_index);
			
			// content settings
			$output['content_show_title'] = ($this->input->post('content_show_title') != '1' && $this->input->post('content_show_title') != '0' ? null : $this->input->post('content_show_title'));
			$output['content_show_time'] = ($this->input->post('content_show_time') != '1' && $this->input->post('content_show_time') != '0' ? null : $this->input->post('content_show_time'));
			$output['content_show_author'] = ($this->input->post('content_show_author') != '1' && $this->input->post('content_show_author') != '0' ? null : $this->input->post('content_show_author'));
			
			// revision values
			$output['header_value'] = htmlspecialchars($data_post_revision['header_value'], ENT_QUOTES, config_item('charset'));
			$output['body_value'] = htmlspecialchars($data_post_revision['body_value'], ENT_QUOTES, config_item('charset'));
			$output['body_summary'] = htmlspecialchars($data_post_revision['body_summary'], ENT_QUOTES, config_item('charset'));
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/article/article_ae_view', $output);
	}// add
	
	
	public function ajax_nameuri() 
	{
		if ($this->input->post() && $this->input->is_ajax_request()) {
			
			$post_name = trim($this->input->post('post_name'));
			$nodupedit = trim($this->input->post('nodupedit'));
			$nodupedit = ($nodupedit == 'true' ? true : false);
			$id = intval($this->input->post('id'));
			
			$output['post_uri'] = $this->posts_model->noDupPostUri($post_name, $nodupedit, $id);
			
			// output
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
			
		}
	}// ajax_nameuri
	
	
	public function ajax_searchtag() 
	{
		$_GET['q'] = trim($this->input->get('term'));
		
		$this->taxonomy_model->tax_type = 'tag';
		$list_tags = $this->taxonomy_model->listTags(array('list_for' => 'admin'));
		
		$output = '';
		if (isset($list_tags['items']) && is_array($list_tags['items'])) {
			$i = 0;// important. can't use other number in array key. because jqueryui autocomplete count from 0 and +1 for each array
			foreach ($list_tags['items'] as $row) {
				$output[$i]['id'] = $row->tid;
				$output[$i]['value'] = $row->t_name;
				$i++;
			}
		}
		
		// clear unused items
		unset($list_tags, $i, $row);
		
		// output
		$this->output->set_content_type('application/json');
		$this->output->set_output(json_encode($output));
	}// ajax_searchtag
	
	
	public function del_rev($post_id = '', $revision_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_delete_revision') != true) {redirect('site-admin');}
		
		if (!is_numeric($post_id) || !is_numeric($revision_id)) {redirect('site-admin/article');}
		
		if (!$this->input->post()) {
			
			// head tags output ##############################
			$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
			// meta tags
			// link tags
			// script tags
			// end head tags output ##############################
			
			// output
			$this->generate_page('site-admin/templates/post/del_rev_view', $output);
		} else {
			
			// check if revision_id match post_id in revision table and not current
			$this->db->join('posts', 'posts.post_id = post_revision.post_id', 'left');
			$this->db->where('post_revision.post_id', $post_id)->where('post_revision.revision_id', $revision_id);
			$this->db->where('posts.revision_id !=', $revision_id);
			$query = $this->db->get('post_revision');
			if ($query->num_rows() <= 0) {
				$query->free_result();
				unset($query);
				redirect('site-admin/article/edit/'.$post_id);
			}
			$query->free_result();
			
			// delete revision
			$this->db->where('post_id', $post_id)->where('revision_id', $revision_id);
			$this->db->delete('post_revision');
			
			// go back
			redirect('site-admin/article/edit/'.$post_id);
		}
	}// del_rev
	
	
	public function delete($post_id = '') 
	{
		// check permission (both canNOT delete own and delete other => get out)
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_own_perm') != true && $this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// open posts table for check permission and delete.
		$this->db->join('post_fields', 'posts.post_id = post_fields.post_id', 'left outer');
		$this->db->join('accounts', 'posts.account_id = accounts.account_id', 'left');
		$this->db->join('post_revision', 'posts.revision_id = post_revision.revision_id', 'inner');
		$this->db->where('post_type', $this->posts_model->post_type);
		$this->db->where('language', $this->posts_model->language);
		$this->db->where('posts.post_id', $post_id);
		$query = $this->db->get('posts');
		if ($query->num_rows() <= 0) {$query->free_result(); redirect('site-admin/article');}// not found
		$row = $query->row();
		
		// check permissions-----------------------------------------------------------
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_own_perm') === false && $row->account_id == $my_account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('post_you_have_no_permission_edit_yours')
				)
			);
			redirect('site-admin/article');
		} elseif ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_other_perm') === false && $row->account_id != $my_account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('post_you_have_no_permission_edit_others')
				)
			);
			redirect('site-admin/article');
		}
		// end check permissions-----------------------------------------------------------
		
		// redirect back value
		$this->load->library('user_agent');
		if ($this->input->get('rdr') == null) {
			$output['rdr'] = $this->agent->referrer();
		} else {
			$output['rdr'] = trim($this->input->get('rdr'));
		}
		
		// send row for other use.
		$output['row'] = $row;
		$query->free_result();
		
		// save action
		if ($this->input->post()) {
			if ($this->input->post('confirm') == 'yes') {
				$this->posts_model->delete($post_id);
				// go back
				if ($this->input->get('rdr') != null) {
					redirect($this->input->get('rdr'));
				} else {
					redirect('site-admin/article');
				}
			}
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/article/article_delete_view', $output);
	}// delete
	
	
	public function edit($post_id = '') 
	{
		// check permission (both canNOT edit own and edit other => get out)
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_own_perm') != true && $this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_other_perm') != true) {redirect('site-admin');}
		
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// open posts table for check permission and edit.
		$data['post_id'] = $post_id;
		$row = $this->posts_model->getPostData($data);
		unset($data['post_id']);
		
		// if selected post id is not exists.
		if ($row == null) {
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('post_there_is_no_selected_article')
				)
			);
			redirect('site-admin/article');
		}
		
		// check permissions-----------------------------------------------------------
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_own_perm') === false && $row->account_id == $my_account_id) {
			// user has NO permission to edit own and editing own.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('post_you_have_no_permission_edit_yours')
				)
			);
			redirect('site-admin/article');
		} elseif ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_other_perm') === false && $row->account_id != $my_account_id) {
			// user has NO permission to edit others and editing others.
			unset($row, $my_account_id);
			// flash error permission message
			$this->load->library('session');
			$this->session->set_flashdata(
				'form_status',
				array(
					'form_status' => 'error',
					'form_status_message' => $this->lang->line('post_you_have_no_permission_edit_others')
				)
			);
			redirect('site-admin/article');
		}
		// end check permissions-----------------------------------------------------------
		
		// list themes for select
		$output['list_theme'] = $this->themes_model->listEnabledThemes();
		
		// list categories for check
		$this->taxonomy_model->tax_type = 'category';
		$output['list_category'] = $this->taxonomy_model->listTaxTerm();
		
		// preset settings and values---------------------------------------------------------
		$output['post_id'] = $post_id;
		$output['post_comment'] = '1';
		$output['post_status'] = '1';
		
		// load settings and values from db for edit.---------------------------------------
		$this->taxonomy_model->tax_type = 'tag';
		$ttlist = $this->taxonomy_model->listTaxTermIndex($post_id);
		$tid = array();
		if (!empty($ttlist)) {
			foreach ($ttlist as $atid) {
				$tid[] = $atid->tid;
			}
		}
		unset($ttlist);
		$output['tagid'] = $tid;
		
		// load categories of this post for edit.
		$this->taxonomy_model->tax_type = 'category';
		$ttlist = $this->taxonomy_model->listTaxTermIndex($post_id);
		$tid = array();
		if (!empty($ttlist)) {
			foreach ($ttlist as $atid) {
				$tid[] = $atid->tid;
			}
		}
		unset($ttlist);
		$output['tid'] = $tid;
		
		// store output data for form
		$output['theme_system_name'] = $row->theme_system_name;
		$output['post_name'] = $row->post_name;
		$output['post_uri'] = $row->post_uri;
		$output['post_feature_image'] = $row->post_feature_image;
		$output['post_comment'] = $row->post_comment;
		$output['post_status'] = $row->post_status;
		$output['meta_title'] = $row->meta_title;
		$output['meta_description'] = $row->meta_description;
		$output['meta_keywords'] = $row->meta_keywords;
		
		// content settings
		$content_settings = unserialize($row->content_settings);
		
		$output['content_show_title'] = $content_settings['content_show_title'];
		$output['content_show_time'] = $content_settings['content_show_time'];
		$output['content_show_author'] = $content_settings['content_show_author'];
		
		// revision table
		$output['revision_id'] = $row->revision_id;
		$output['header_value'] = htmlspecialchars($row->header_value, ENT_QUOTES, config_item('charset'));
		$output['body_value'] = htmlspecialchars($row->body_value, ENT_QUOTES, config_item('charset'));
		$output['body_summary'] = htmlspecialchars($row->body_summary, ENT_QUOTES, config_item('charset'));
		
		// send row for other use.
		$output['row'] = $row;
		
		// list revision
		$condition['post_id'] = $post_id;
		$revision = $this->posts_model->listRevision($condition);
		$output['count_revision'] = $revision['total'];
		$output['list_revision'] = $revision['items'];
		unset($revision, $condition);
		
		// save action
		if ($this->input->post()) {
			
			// for taxonomy_index table
			$data_tax_index['tid'] = $this->input->post('tid');// categories
			$data_tax_index['tagid'] = $this->input->post('tagid');// tags
			
			// for posts table
			$data_posts['post_id'] = $post_id;
			$data_posts['theme_system_name'] = trim($this->input->post('theme_system_name'));
				if ($data_posts['theme_system_name'] == null) {$data_posts['theme_system_name']= null;}
			$data_posts['post_name'] = htmlspecialchars(trim($this->input->post('post_name')), ENT_QUOTES, config_item('charset'));
			$data_posts['post_uri'] = trim($this->input->post('post_uri'));
			$data_posts['post_feature_image'] = trim($this->input->post('post_feature_image'));
				if ($data_posts['post_feature_image'] == null || !is_numeric($data_posts['post_feature_image'])) {$data_posts['post_feature_image'] = null;}
			$data_posts['post_comment'] = (int) $this->input->post('post_comment');
			if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm')) {
				$data_posts['post_status'] = (int) $this->input->post('post_status');
				$data_posts['post_status'] = ($data_posts['post_status'] == '1' ? '1' : '0');
			}
			$data_posts['post_update'] = time();
			$data_posts['post_update_gmt'] = local_to_gmt(time());
			$data_posts['post_publish_date'] = time();
			$data_posts['post_publish_date_gmt'] = local_to_gmt(time());
			$data_posts['meta_title'] = htmlspecialchars(trim($this->input->post('meta_title')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_title'] == null) {$data_posts['meta_title']= null;}
			$data_posts['meta_description'] = htmlspecialchars(trim($this->input->post('meta_description')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_description'] == null) {$data_posts['meta_description']= null;}
			$data_posts['meta_keywords'] = htmlspecialchars(trim($this->input->post('meta_keywords')), ENT_QUOTES, config_item('charset'));
				if ($data_posts['meta_keywords'] == null) {$data_posts['meta_keywords']= null;}
			
			// content settings
			if ($this->input->post('content_show_title') == null && $this->input->post('content_show_time') == null && $this->input->post('content_show_author') == null) {
				$data_posts['content_settings'] = null;
			} else {
				$setting['content_show_title'] = $this->input->post('content_show_title');
				$setting['content_show_time'] = $this->input->post('content_show_time');
				$setting['content_show_author'] = $this->input->post('content_show_author');
				$data_posts['content_settings'] = serialize($setting);
				unset($setting);
			}
			
			// revision table
			$data_post_revision['header_value'] = trim($this->input->post('header_value'));
				if ($data_post_revision['header_value'] == null) {$data_post_revision['header_value']= null;}
			$data_post_revision['body_value'] = trim($this->input->post('body_value'));
			$data_post_revision['body_summary'] = trim($this->input->post('body_summary'));
				if ($data_post_revision['body_summary'] == null) {$data_post_revision['body_summary']= null;}
			$data['new_revision'] = $this->input->post('new_revision');
			$data_post_revision['log'] = htmlspecialchars(trim($this->input->post('revision_log')), ENT_QUOTES, config_item('charset'));
				$data_post_revision['log'] = ($data_post_revision['log'] == null || $data['new_revision'] != '1' ? null : $data_post_revision['log']);
			$data_post_revision['revision_date'] = time();
			$data_post_revision['revision_date_gmt'] = local_to_gmt(time());
			
			// load form validation
			$this->load->library('form_validation');
			$this->form_validation->set_rules('post_name', 'lang:post_article_name', 'trim|required');
			$this->form_validation->set_rules('body_value', 'lang:post_content', 'trim|required');
			
			if ($this->form_validation->run() == false) {
				$output['form_status'] = 'error';
				$output['form_status_message'] = '<ul>'.validation_errors('<li>', '</li>').'</ul>';
			} else {
				// save result
				$result = $this->posts_model->edit($data_posts, $data_post_revision, $data_tax_index, $data);
				if ($result === true) {
					$this->load->library('session');
					$this->session->set_flashdata(
						'form_status',
						array(
							'form_status' => 'success',
							'form_status_message' => $this->lang->line('admin_saved')
						)
					);
					redirect('site-admin/article');
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $result;
				}
			}
			
			// re-populate form
			$output = array_merge($output, $data_posts);
			$output = array_merge($output, $data_post_revision);
			$output = array_merge($output, $data_tax_index);
			if (isset($data_posts['post_status'])) {
				$output['post_status'] = $data_posts['post_status'];
			}
			
			// content settings
			$output['content_show_title'] = ($this->input->post('content_show_title') != '1' && $this->input->post('content_show_title') != '0' ? null : $this->input->post('content_show_title'));
			$output['content_show_time'] = ($this->input->post('content_show_time') != '1' && $this->input->post('content_show_time') != '0' ? null : $this->input->post('content_show_time'));
			$output['content_show_author'] = ($this->input->post('content_show_author') != '1' && $this->input->post('content_show_author') != '0' ? null : $this->input->post('content_show_author'));
			
			// revision values
			$output['header_value'] = htmlspecialchars($data_post_revision['header_value'], ENT_QUOTES, config_item('charset'));
			$output['body_value'] = htmlspecialchars($data_post_revision['body_value'], ENT_QUOTES, config_item('charset'));
			$output['body_summary'] = htmlspecialchars($data_post_revision['body_summary'], ENT_QUOTES, config_item('charset'));
			$output['new_revision'] = $data['new_revision'];
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/article/article_ae_view', $output);
	}// edit
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_viewall_perm') != true) {redirect('site-admin');}
		
		// list category for select filter
		$this->taxonomy_model->tax_type = 'category';
		$output['list_category'] = $this->taxonomy_model->listTaxTerm();
		
		// sort, orders, search, tid
		$output['orders'] = strip_tags(trim($this->input->get('orders')));
		$output['sort'] = ($this->input->get('sort') == null || $this->input->get('sort') == 'desc' ? 'asc' : 'desc');
		$output['q'] = htmlspecialchars(trim($this->input->get('q')));
		$output['tid'] = strip_tags(trim($this->input->get('tid')));
		
		// load session for flashdata
		$this->load->library('session');
		$form_status = $this->session->flashdata('form_status');
		if (isset($form_status['form_status']) && isset($form_status['form_status_message'])) {
			$output['form_status'] = $form_status['form_status'];
			$output['form_status_message'] = $form_status['form_status_message'];
		}
		unset($form_status);
		
		// list item
		$output['list_item'] = $this->posts_model->listPost('admin');
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		
		// my account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$output['my_account_id'] = $ca_account['id'];
		unset($ca_account);
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/article/article_view', $output);
	}// index
	
	
	public function process_bulk() 
	{
		// get account id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$my_account_id = $ca_account['id'];
		unset($ca_account);
		
		// get id and acttion
		$id = $this->input->post('id');
		if (!is_array($id)) {redirect('site-admin/article');}
		$act = trim($this->input->post('act'));
		
		if ($act == 'publish') {
			// check permission
			if (!$this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm')) {redirect('site-admin/article');}
			
			foreach ($id as $an_id) {
				// open for check
				$this->db->where('post_id', $an_id);
				$query = $this->db->get('posts');
				if ($query->num_rows() <= 0) {$query->free_result(); continue;}
				$row = $query->row();
				$query->free_result();
				
				// update
				$this->db->where('post_id', $an_id);
				$this->db->set('post_status', '1');
				$this->db->set('post_update', time());
				$this->db->set('post_update_gmt', local_to_gmt(time()));
				if ($row->post_publish_date == null && $row->post_publish_date_gmt == null) {
					$this->db->set('post_publish_date', time());
					$this->db->set('post_publish_date_gmt', local_to_gmt(time()));
					
					// publish plugin
					$this->modules_plug->do_action('post_published_byid', $an_id);
				}
				$this->db->update('posts');
			}
		} elseif($act == 'unpublish') {
			// check permission
			if (!$this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm')) {redirect('site-admin/article');}
			
			foreach ($id as $an_id) {
				$this->db->where('post_id', $an_id);
				$this->db->set('post_status', '0');
				$this->db->set('post_update', time());
				$this->db->set('post_update_gmt', local_to_gmt(time()));
				$this->db->update('posts');
			}
		} elseif ($act == 'del') {
			// check both permission
			if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_own_perm') != true && $this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_other_perm') != true) {redirect('site-admin/article');}
			
			foreach ($id as $an_id) {
				// get data for check
				$this->db->where('post_id', $an_id);
				$query = $this->db->get('posts');
				if ($query->num_rows() <= 0) {$query->free_result(); continue;}
				$row = $query->row();
				$query->free_result();
				
				// check permissions-----------------------------------------------------------
				if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_own_perm') === false && $row->account_id == $my_account_id) {
					// user has NO permission to edit own and editing own.
					unset($row, $my_account_id);
					continue;
				} elseif ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_delete_other_perm') === false && $row->account_id != $my_account_id) {
					// user has NO permission to edit others and editing others.
					unset($row, $my_account_id);
					continue;
				} 
				// end check permissions-----------------------------------------------------------
				
				$this->posts_model->delete($an_id);
			}
		}
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral()) {
			redirect($this->agent->referrer());
		} else {
			redirect('site-admin/article');
		}
	}// process_bulk
	
	
	public function reorder($post_id = '', $tid = '', $move = '') 
	{
		// check permission
		if (!$this->account_model->checkAdminPermission('post_article_perm', 'post_article_sort_perm')) {redirect('site-admin/article');}
		
		//
		if (!is_numeric($post_id) || !is_numeric($tid) || ($move != 'up' && $move != 'dn')) {redirect('site-admin/article');}
		
		$this->load->library('user_agent');
		
		// get current position
		$condition['post_id'] = $post_id;
		$condition['tid'] = $tid;
		$row = $this->taxonomy_model->getTaxonomyIndexData($condition);
		if ($row == null) {
			// not found
			redirect('site-admin/article');
		}
		
		if ($move == 'up') {
			// check if there is higher position
			$this->db->where('tid', $tid)->where('position >', $row->position);
			$this->db->order_by('position', 'asc');
			$query2 = $this->db->get('taxonomy_index');
			if ($query2->num_rows() <= 0) {$query2->free_result(); redirect($this->agent->referrer());}// not found. this is heighest position, no more up.
			$row2 = $query2->row();
			$query2->free_result();
			
			// update heigher to -1
			if ($row2->position-$row->position > 1) {
				$position = $row->position;
			} else {
				$position = $row2->position-1;
			}
			$this->db->set('position', $position);
			$this->db->where('index_id', $row2->index_id);
			$this->db->update('taxonomy_index');
			
			// update current position to heigher
			$this->db->set('position', $row->position+1);
			$this->db->where('index_id', $row->index_id);
			$this->db->update('taxonomy_index');
		} elseif ($move == 'dn') {
			// check if there is lower position
			$this->db->where('tid', $tid)->where('position <', $row->position);
			$this->db->order_by('position', 'desc');
			$query2 = $this->db->get('taxonomy_index');
			if ($query2->num_rows() <= 0) {$query2->free_result(); redirect($this->agent->referrer());}// not found. this is heighest position, no more up.
			$row2 = $query2->row();
			$query2->free_result();
			
			// update lower to +1
			$this->db->set('position', $row2->position+1);
			$this->db->where('index_id', $row2->index_id);
			$this->db->update('taxonomy_index');
			
			// update current position to lower
			if ($row->position-$row2->position > 1) {
				$position = $row2->position;
			} else {
				$position = $row->position-1;
			}
			$this->db->set('position', $position);
			$this->db->where('index_id', $row->index_id);
			$this->db->update('taxonomy_index');
		}
		
		unset($position, $query, $query2, $row, $row2);
		
		redirect($this->agent->referrer());
	}// reorder
	
	
	public function revert($post_id = '', $revision_id = '') 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('post_article_perm', 'post_revert_revision') != true) {redirect('site-admin');}
		
		if (!is_numeric($post_id) || !is_numeric($revision_id)) {redirect('site-admin/article');}
		
		if (!$this->input->post()) {
			// head tags output ##############################
			$output['page_title'] = $this->html_model->gen_title($this->lang->line('post_articles'));
			// meta tags
			// link tags
			// script tags
			// end head tags output ##############################
			
			// output
			$this->generate_page('site-admin/templates/post/revert_view', $output);
		} else {
			// check if revision_id match post_id in revision table
			$this->db->where('post_id', $post_id)->where('revision_id', $revision_id);
			$query = $this->db->get('post_revision');
			if ($query->num_rows() <= 0) {
				$query->free_result();
				unset($query);
				redirect('site-admin/article/edit/'.$post_id);
			}
			$query->free_result();
			
			// update revision id to posts table
			$this->db->set('revision_id', $revision_id);
			$this->db->where('post_id', $post_id);
			$this->db->update('posts');
			
			// go back
			redirect('site-admin/article/edit/'.$post_id);
		}
	}// revert
	

}

