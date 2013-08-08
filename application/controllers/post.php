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
 
class post extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('posts_model', 'taxonomy_model'));
		
		// load helper
		$this->load->helper(array('date', 'language'));
		
		// load language
		$this->lang->load('category');
		$this->lang->load('tag');
		$this->lang->load('post');
	}// __construct
	
	
	public function _remap($att1 = '', $att2 = '') 
	{
		if ($att1 == 'preview') {
			return $this->preview();
		} elseif ($att1 == 'revision') {
			return $this->revision($att2);
		} else {
			return $this->view($att1, $att2);
		}
	}// _remap
	
	
	public function preview() 
	{
		if (! $this->input->post()) {return null;}
		
		// get POST data.
		$data['theme_system_name'] = trim($this->input->post('theme_system_name'));
			$data['theme_system_name'] = ($data['theme_system_name'] == null ? null : $data['theme_system_name']);
		$data['post_name'] = htmlspecialchars(trim($this->input->post('post_name')), ENT_QUOTES, config_item('charset'));
		$data['post_uri'] = trim($this->input->post('post_uri'));
		$data['post_comment'] = $this->input->post('post_comment');
			$data['post_comment'] = ($data['post_comment'] != '1' ? '0' : '1');
		$data['post_status'] = $this->input->post('post_status');
			if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm') != true) {$data['post_status'] = '0';}
		$data['meta_title'] = htmlspecialchars(trim($this->input->post('meta_title')), ENT_QUOTES, config_item('charset'));
			$data['meta_title'] = ($data['meta_title'] == null ? null : $data['meta_title']);
		$data['meta_description'] = htmlspecialchars(trim($this->input->post('meta_description')), ENT_QUOTES, config_item('charset'));
			$data['meta_description'] = ($data['meta_description'] == null ? null : $data['meta_description']);
		$data['meta_keywords'] = htmlspecialchars(trim($this->input->post('meta_keywords')), ENT_QUOTES, config_item('charset'));
			$data['meta_keywords'] = ($data['meta_keywords'] == null ? null : $data['meta_keywords']);
		$data['header_value'] = trim($this->input->post('header_value'));
		$data['body_value'] = trim($this->input->post('body_value'));
		
		// send data to output
		$output = $data;
		$row = new stdClass($data);
		foreach ($data as $key => $item) {
			$row->$key = $item;
		}
		$row->post_id = '';
		$row->post_publish_date_gmt = local_to_gmt(time());
		$output['row'] = $row;
		unset($data);
		
		// set custom theme (if specified)---------------------------------
		if ($row->theme_system_name != null) {
			$this->theme_path = base_url().config_item('agni_theme_path').$row->theme_system_name.'/';// for use in css
			$this->theme_system_name = $row->theme_system_name;// for template file.
		}
		
		// load display settings from config
		$content_setting = $this->config_model->load(array('content_show_title', 'content_show_time', 'content_show_author'));
		$output['content_show_title'] = $content_setting['content_show_title']['value'];
		$output['content_show_time'] = $content_setting['content_show_time']['value'];
		$output['content_show_author'] = $content_setting['content_show_author']['value'];
		
		// display settings (override)
		if ($this->input->post('content_show_title') != null)
			$output['content_show_title'] = $this->input->post('content_show_title');
		if ($this->input->post('content_show_time') != null)
			$output['content_show_time'] = $this->input->post('content_show_time');
		if ($this->input->post('content_show_author') != null)
			$output['content_show_author'] = $this->input->post('content_show_author');
		
		// 
		$output['body_value'] = $this->posts_model->modifyPostContent($output['body_value'], 'article');
		$output['list_category'] = '';
		$output['list_tag'] = '';
		$output['comment_allow'] = $output['post_comment'];
		$output['post_publish_date_gmt'] = date('Y-m-d H:i:s');
		$output['post_author'] = lang('post_preview');
		
		// head tags output ##############################
		if ($output['meta_title'] != null) {
			$output['page_title'] = $output['meta_title'];
		} else {
			$output['page_title'] = $this->html_model->gen_title($output['post_name']);
		}
		// meta tags
		$meta = '';
		if ($output['meta_description'] != null) {
			$meta[] = '<meta name="description" content="'.$output['meta_description'].'" />';
		}
		if ($output['meta_keywords'] != null) {
			$meta[] = '<meta name="keywords" content="'.$output['meta_keywords'].'" />';
		}
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		// script tags
		// header value (in_head_elements)
		if ($output['header_value'] != null) {
			$output['in_head_elements'] = $output['header_value'];
		}
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/post/post_view', $output);
	}// preview
	
	
	public function revision($arr = '') 
	{
		if (!isset($arr[0]) && !isset($arr[1])) {redirect();}
		$post_id = $arr[0];
		$revision_id = $arr[1];
		
		// get "revision" data from db.
		$data['post_revision.revision_id'] = $revision_id;
		$data['post_revision.post_id'] = $post_id;
		$row = $this->posts_model->getPostRevisionData($data);
		unset($data);
		
		// not found
		if ($row == null) {
			redirect();
		}
		
		// set row for custom use
		$output['row'] = $row;
		
		// preset values---------------------------------------------------------
		$output['post_name'] = $this->modules_plug->do_filter('post_postname', $row->post_name);
		
		if ($this->modules_plug->has_filter('post_publish_date_gmt')) {
			$output['post_publish_date_gmt'] = $this->modules_plug->do_filter('post_publish_date_gmt', $row->post_publish_date_gmt);
		} else {
			$output['post_publish_date_gmt'] = gmt_date('j F Y', $row->post_publish_date_gmt);
		}
		
		if ($this->modules_plug->has_filter('post_postauthor')) {
			$output['post_author'] = $this->modules_plug->do_filter('post_postauthor', array($row->account_username, $row->account_id));
		} else {
			$output['post_author'] = anchor('author/'.$row->account_username, $row->account_username, array('rel' => 'author'));
		}
		
		$output['body_value'] = $this->posts_model->modifyPostContent($row->body_value, $row->post_type);
		
		// set custom theme (if specified)---------------------------------
		if ($row->theme_system_name != null) {
			$this->theme_path = base_url().config_item('agni_theme_path').$row->theme_system_name.'/';// for use in css
			$this->theme_system_name = $row->theme_system_name;// for template file.
		}
		
		// load default content settings from config----------------------------
		$content_setting = $this->config_model->load(array('content_show_title', 'content_show_time', 'content_show_author'));
		$output['content_show_title'] = $content_setting['content_show_title']['value'];
		$output['content_show_time'] = $content_setting['content_show_time']['value'];
		$output['content_show_author'] = $content_setting['content_show_author']['value'];
		
		// load post's content settings to override.
		if ($row->content_settings != null) {
			// unserialize settings
			$content_setting = unserialize($row->content_settings);
			foreach ($content_setting as $key => $item) {
				if ($item != null) {
					$output[$key] = $item;
				}
			}
		}
		
		// load comment setting------------------------
		$output['comment_allow'] = $row->post_comment;
		$global_comment_setting = $this->config_model->loadSingle('comment_allow');
		if ($global_comment_setting != null) {
			$output['comment_allow'] = $global_comment_setting;
		}
		unset($global_comment_setting);
		
		// list category for this page-------------------------------------------
		$this->taxonomy_model->tax_type = 'category';
		$output['list_category'] = $this->taxonomy_model->listTaxTermIndex($row->post_id, true);
		
		// list tag for this page------------------------------------------------
		$this->taxonomy_model->tax_type = 'tag';
		$output['list_tag'] = $this->taxonomy_model->listTaxTermIndex($row->post_id);
		
		// head tags output ##############################
		if ($row->meta_title != null) {
			$output['page_title'] = $row->meta_title;
		} else {
			$output['page_title'] = $this->html_model->gen_title($row->post_name);
		}
		// meta tags
		$meta = '';
		if ($row->meta_description != null) {
			$meta[] = '<meta name="description" content="'.$row->meta_description.'" />';
		}
		if ($row->meta_keywords != null) {
			$meta[] = '<meta name="keywords" content="'.$row->meta_keywords.'" />';
		}
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		if ($row->post_type == 'article') {
			// article canonical
			$link[] = '<link href="'.site_url('post/'.$row->post_uri_encoded).'" rel="canonical" />';
		} else {
			// page canonical
			$link[] = '<link href="'.site_url($row->post_uri_encoded).'" rel="canonical" />';
		}
		if (isset($link)) {
			$output['page_link'] = $this->html_model->gen_tags($link);
		}
		unset($link);
		// script tags
		// header value (in_head_elements)
		if ($row->header_value != null) {
			$output['in_head_elements'] = $row->header_value;
		}
		// end head tags output ##############################
		
		// output
		if ($row->post_type == 'page') {
			$this->generate_page('front/templates/post/page_view', $output);
		} else {
			$this->generate_page('front/templates/post/post_view', $output);
		}
	}// revision
	
	
	public function view($post_uri = '') 
	{
		// load post from db by uri
		$data['posts.post_uri_encoded'] = $post_uri;
		$data['posts_status'] = '1';
		$row = $this->posts_model->getPostData($data);
		unset($data);
		
		// post not found.
		if ($row == null) {
			show_404();
		}
		
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		$breadcrumb[] = array('text' => $this->lang->line('frontend_home'), 'url' => '/');
		
		// loop each category and all sub or tag
		$segs = $this->uri->segment_array();
		
		foreach ($segs as $segment) {
			$data['t_uri_encoded'] = $segment;
			$data['language'] = $this->lang->get_current_lang();
			$row_seg = $this->taxonomy_model->getTaxonomyTermDataDb($data);
			unset($data);
			
			$last_taxterm_uri = '';
			if ($row_seg != null) {
				$breadcrumb[] = array('text' => $row_seg->t_name, 'url' => $row_seg->t_uris);
				$last_taxterm_uri = $row_seg->t_uris;
			}
		}
		
		$breadcrumb[] = array('text' => $row->post_name, 'url' => $last_taxterm_uri . '/' . $row->post_uri_encoded);
		
		$output['breadcrumb'] = $breadcrumb;
		$row_seg = null;
		unset($breadcrumb, $row_seg);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// set row for custom use
		$output['row'] = $row;
		
		// preset values---------------------------------------------------------
		$output['post_name'] = $this->modules_plug->do_filter('post_postname', $row->post_name);
		
		if ($this->modules_plug->has_filter('post_publish_date_gmt')) {
			$output['post_publish_date_gmt'] = $this->modules_plug->do_filter('post_publish_date_gmt', $row->post_publish_date_gmt);
		} else {
			$output['post_publish_date_gmt'] = gmt_date('j F Y', $row->post_publish_date_gmt);
		}
		
		if ($this->modules_plug->has_filter('post_postauthor')) {
			$output['post_author'] = $this->modules_plug->do_filter('post_postauthor', array($row->account_username, $row->account_id));
		} else {
			$output['post_author'] = anchor('author/'.$row->account_username, $row->account_username, array('rel' => 'author'));
		}
		
		$output['body_value'] = $this->posts_model->modifyPostContent($row->body_value, $row->post_type);
		
		// set custom theme (if specified)---------------------------------
		if ($row->theme_system_name != null) {
			$this->theme_path = base_url().config_item('agni_theme_path').$row->theme_system_name.'/';// for use in css
			$this->theme_system_name = $row->theme_system_name;// for template file.
		} else {
			// use theme from category (inheritance theme)
			$taxterm_uri = $this->uri->segment($this->uri->total_segments()-1);
			$this->db->where('t_uri_encoded', $taxterm_uri);
			$query2 = $this->db->get('taxonomy_term_data');
			if ($query2->num_rows() > 0) {
				$row2 = $query2->row();
				if ($row2->theme_system_name != null) {
					$this->theme_path = base_url().config_item('agni_theme_path').$row2->theme_system_name.'/';// for use in css
					$this->theme_system_name = $row2->theme_system_name;// for template file.
				}
			}
			$query2->free_result();
			unset($taxterm_uri, $query2, $row2);
		}
		
		// load default content settings from config----------------------------
		$content_setting = $this->config_model->load(array('content_show_title', 'content_show_time', 'content_show_author'));
		$output['content_show_title'] = $content_setting['content_show_title']['value'];
		$output['content_show_time'] = $content_setting['content_show_time']['value'];
		$output['content_show_author'] = $content_setting['content_show_author']['value'];
		
		// load post's content settings to override.
		if ($row->content_settings != null) {
			// unserialize settings
			$content_setting = unserialize($row->content_settings);
			foreach ($content_setting as $key => $item) {
				if ($item != null) {
					$output[$key] = $item;
				}
			}
		}
		
		// load comment setting------------------------
		$output['comment_allow'] = $row->post_comment;
		$global_comment_setting = $this->config_model->loadSingle('comment_allow');
		if ($global_comment_setting != null) {
			$output['comment_allow'] = $global_comment_setting;
		}
		unset($global_comment_setting);
		
		// list category and tag
		if ($row->post_type == 'article') {
			// list category for this page-------------------------------------------
			$this->taxonomy_model->tax_type = 'category';
			$output['list_category'] = $this->taxonomy_model->listTaxTermIndex($row->post_id, true);
			
			// list tag for this page------------------------------------------------
			$this->taxonomy_model->tax_type = 'tag';
			$output['list_tag'] = $this->taxonomy_model->listTaxTermIndex($row->post_id);
		}
		
		// add view count.
		$this->db->query('UPDATE ' . $this->db->dbprefix(SITE_TABLE . 'posts') . ' SET view_count = view_count+1 WHERE post_id = ' . $this->db->escape($row->post_id));
		
		// head tags output ##############################
		if ($row->meta_title != null) {
			$output['page_title'] = $row->meta_title;
		} else {
			$output['page_title'] = $this->html_model->gen_title($row->post_name);
		}
		// meta tags
		$meta = '';
		if ($row->meta_description != null) {
			$meta[] = '<meta name="description" content="'.$row->meta_description.'" />';
		}
		if ($row->meta_keywords != null) {
			$meta[] = '<meta name="keywords" content="'.$row->meta_keywords.'" />';
		}
		$output['page_meta'] = $this->html_model->gen_tags($meta);
		unset($meta);
		// link tags
		if ($row->post_type == 'article') {
			// article canonical
			$link[] = '<link href="'.site_url('post/'.$row->post_uri_encoded).'" rel="canonical" />';
		} else {
			// page canonical
			$link[] = '<link href="'.site_url($row->post_uri_encoded).'" rel="canonical" />';
		}
		if (isset($link)) {
			$output['page_link'] = $this->html_model->gen_tags($link);
		}
		unset($link);
		// script tags
		// header value (in_head_elements)
		if ($row->header_value != null) {
			$output['in_head_elements'] = $row->header_value;
		}
		// end head tags output ##############################
		
		// output
		if ($row->post_type == 'page') {
			$this->generate_page('front/templates/post/page_view', $output);
		} else {
			$this->generate_page('front/templates/post/post_view', $output);
		}
	}// view
	
	
}

// EOF