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
 
class category extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('posts_model', 'taxonomy_model'));
		
		// set post type and taxonomy type
		$this->posts_model->post_type = 'article';
		$this->taxonomy_model->tax_type = 'category';
		
		// load helper
		$this->load->helper(array('date', 'language'));
		
		// load language
		$this->lang->load('post');
	}// __construct
	
	
	public function _remap($att1 = '', $att2 = '') 
	{
		$this->index($att1, $att2);
	}// _remap
	
	
	public function index($att1 = '', $att2 = '') 
	{
		
		// get the last attribute as uri (if exists).
		if (empty($att2)) {
			$t_uri = $att1;
		} else {
			$t_uri = $att2[count($att2)-1];
		}
		
		// load category for title, metas
		$data['t_uri_encoded'] = $t_uri;
		$data['language'] = $this->lang->get_current_lang();
		$row = $this->taxonomy_model->getTaxonomyTermDataDb($data);
		unset($data);
		
		if ($row == null) {
			// not found category
			show_404();
			exit;
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
			
			if ($row_seg != null) {
				$breadcrumb[] = array('text' => $row_seg->t_name, 'url' => $row_seg->t_uris);
			}
		}
		
		$output['breadcrumb'] = $breadcrumb;
		$row_seg = null;
		unset($breadcrumb, $row_seg);
		// set breadcrumb ----------------------------------------------------------------------------------------------------------------------
		
		// set cat (category) object for use in views
		$output['cat'] = $row;
		
		// if has theme setting.
		if ($row->theme_system_name != null) {
			// set theme
			$this->theme_path = base_url().config_item('agni_theme_path').$row->theme_system_name.'/';// for use in css
			$this->theme_system_name = $row->theme_system_name;// for template file.
		}
		
		// list posts---------------------------------------------------------------
		$_GET['tid'] = $row->tid;
		$output['list_item'] = $this->posts_model->listPost('front');
		
		if (is_array($output['list_item'])) {
			$output['pagination'] = $this->pagination->create_links();
		}
		// endlist posts---------------------------------------------------------------
		
		// head tags output ##############################
		if ($row->meta_title != null) {
			$output['page_title'] = $row->meta_title;
		} else {
			$output['page_title'] = $this->html_model->gen_title($row->t_name);
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
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('front/templates/taxterm/category_view', $output);
	}// index
	
	
}

// EOF