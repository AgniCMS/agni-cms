<?php
/**
 * Module Name: Akismet.
 * Module URL: 
 * Description: Akismet spam prevention.
 * Author: vee w.
 * Author URL: http://www.okvee.net
  */
class akismet_module 
{
	
	
	public function __construct() 
	{
		
	}// __construct
	
	
	public function _get_akismet_api() 
	{
		// DO NOT get api config value from config_model because it maybe access to cache from browser.
		$ci =& get_instance();
		$ci->db->where('config_name', 'akismet_api');
		$query = $ci->db->get('config');
		if ($query->num_rows() <= 0) {
			// did not install?????
			$query->free_result();
			return null;
		} else {
			$row = $query->row();
			$query->free_result();
			if ($row->config_value == null) {
				// did not configured properly.
				return null;
			}
			return $row->config_value;
		}
	}// _get_akismet_api
	
	
	public function comment_admin_index_options() 
	{
		$ci =& get_instance();
		$output = '';
		if ($ci->account_model->checkAdminPermission('akismet_perm', 'akismet_set_spam_perm')) {
			$output .= '<option value="spam">'.$ci->lang->line('akismet_spam').'</option>';
		}
		if ($ci->account_model->checkAdminPermission('akismet_perm', 'akismet_set_notspam_perm')) {
			$output .= '<option value="notspam">'.$ci->lang->line('akismet_not_spam').'</option>';
		}
		$output .= '<option value="">---------</option>';
		return $output;
	}// comment_admin_index_options
	
	
	public function comment_admin_index_top() 
	{
		$ci =& get_instance();
		// load language
		$ci->lang->load('akismet/akismet');
		//
		$count = $ci->db->where('comment_status', '0')->where('comment_spam_status', 'spam')->count_all_results('comments');
		return '| '.anchor(current_url().'?filter=comment_spam_status&amp;filter_val=spam', sprintf(lang('akismet_total_spam'), $count));
	}// comment_admin_index_top
	
	
	public function comment_admin_process($cmds = array()) 
	{
		if (!is_array($cmds['id']) || !isset($cmds['act'])) {return;}
		$ci =& get_instance();
		//
		if ($cmds['act'] == 'spam') {
			// check permission
			if (!$ci->account_model->checkAdminPermission('akismet_perm', 'akismet_set_spam_perm')) {return;}
			// get akismet api
			$akismet_api = $this->_get_akismet_api();
			foreach ($cmds['id'] as $an_id) {
				if ($akismet_api != null) {
					// submit to akismet that this is spam
					$ci->db->join('posts', 'posts.post_id = comments.post_id', 'left');
					$ci->db->where('comment_id', $an_id);
					$query = $ci->db->get('comments');
					if ($query->num_rows() > 0) {
						$row = $query->row();
						$query->free_result();
						// load akismet class
						include_once dirname(__FILE__).'/libraries/Akismet.class.php';
						$akismet = new Akismet(site_url(), $akismet_api);
						if ($akismet->isKeyValid()) {
							$akismet->setCommentAuthor($row->name);
							$akismet->setCommentContent($row->comment_body_value);
							$akismet->setPermalink(site_url('post/'.$row->post_uri_encoded));
							$akismet->submitSpam();
						}
					}
				}
				//
				$ci->db->where('comment_id', $an_id);
				$ci->db->set('comment_status', '0');
				$ci->db->set('comment_spam_status', 'spam');
				$ci->db->set('comment_update', time());
				$ci->db->set('comment_update_gmt', local_to_gmt(time()));
				$ci->db->update('comments');
			}
		} elseif ($cmds['act'] == 'notspam') {
			// check permission
			if (!$ci->account_model->checkAdminPermission('akismet_perm', 'akismet_set_notspam_perm')) {return;}
			// get akismet api
			$akismet_api = $this->_get_akismet_api();
			foreach ($cmds['id'] as $an_id) {
				if ($akismet_api != null) {
					// submit to akismet that this is NOT spam
					$ci->db->join('posts', 'posts.post_id = comments.post_id', 'left');
					$ci->db->where('comment_id', $an_id);
					$query = $ci->db->get('comments');
					if ($query->num_rows() > 0) {
						$row = $query->row();
						$query->free_result();
						// load akismet class
						include_once dirname(__FILE__).'/libraries/Akismet.class.php';
						$akismet = new Akismet(site_url(), $akismet_api);
						if ($akismet->isKeyValid()) {
							$akismet->setCommentAuthor($row->name);
							$akismet->setCommentContent($row->comment_body_value);
							$akismet->setPermalink(site_url('post/'.$row->post_uri_encoded));
							$akismet->submitHam();
						}
					}
				}
				//
				$ci->db->where('comment_id', $an_id);
				$ci->db->set('comment_spam_status', 'normal');
				$ci->db->set('comment_update', time());
				$ci->db->set('comment_update_gmt', local_to_gmt(time()));
				$ci->db->update('comments');
			}
		}
	}// comment_admin_process
	
	
	public function comment_spam_check($data = array()) 
	{
		$ci =& get_instance();
		
		// get akismet api
		$akismet_api = $this->_get_akismet_api();
		
		if ($akismet_api == null) {return $data;}
		
		// load akismet class
		include dirname(__FILE__).'/libraries/Akismet.class.php';
		
		$akismet = new Akismet(site_url(), $akismet_api);
		
		if (!$akismet->isKeyValid()) {
			// invalid key.
			return $data;
		}
		
		$akismet->setCommentAuthor($data['name']);
		$akismet->setCommentContent($data['comment_body_value']);
		$akismet->setPermalink($data['permalink_url']);
		
		if($akismet->isCommentSpam()) {
			return 'spam';
		} else {
			return 'normal';
		}
	}// comment_spam_check
	
	
}
