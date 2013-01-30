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
 
class tag extends MY_Controller {
	
	
	function __construct() {
		parent::__construct();
		
		// load model
		$this->load->model( array( 'posts_model', 'taxonomy_model' ) );
		
		// set post type and taxonomy type
		$this->posts_model->post_type = 'article';
		$this->taxonomy_model->tax_type = 'tag';
		
		// load helper
		$this->load->helper( array( 'date', 'language' ) );
		
		// load language
		$this->lang->load( 'post' );
	}// __construct
	
	
	function _remap( $att1 = '', $att2 = '' ) {
		$this->index( $att1, $att2 );
	}// _remap
	
	
	function index( $uri = '', $att2 = '' ) {
		// prevent duplicate content (localhost/tag/tagname and localhost/tag/tagname/aaa can be same result, just 404 it). good for seo.
		if ( !empty( $att2 ) ) {show_404(); exit;}
		
		// load tag data for title, metas
		$data['t_uri_encoded'] = $uri;
		$data['language'] = $this->lang->get_current_lang();
		$row = $this->taxonomy_model->get_taxonomy_term_data_db( $data );
		unset( $data );
		
		if ( $row == null ) {
			// not found tag
			show_404();
			exit;
		}
		
		/*$this->db->where( 't_uri_encoded', $uri );
		$this->db->where( 'language', $this->lang->get_current_lang() );
		$this->db->where( 't_type', 'tag' );
		$query = $this->db->get( 'taxonomy_term_data' );
		if ( $query->num_rows() <= 0 ) {
			// not found category
			$query->free_result();
			show_404();
			exit;
		}
		$row = $query->row();
		$query->free_result();*/
		
		// set cat (tag) object for use in views
		$output['cat'] = $row;
		
		// if has theme setting.
		if ( $row->theme_system_name != null ) {
			// set theme
			$this->theme_path = base_url().config_item( 'agni_theme_path' ).$row->theme_system_name.'/';// for use in css
			$this->theme_system_name = $row->theme_system_name;// for template file.
		}
		unset( $query );
		
		// list posts---------------------------------------------------------------
		$_GET['tid'] = $row->tid;
		$output['list_item'] = $this->posts_model->list_item( 'front' );
		
		if ( is_array( $output['list_item'] ) ) {
			$output['pagination'] = $this->pagination->create_links();
		}
		// endlist posts---------------------------------------------------------------
		
		// head tags output ##############################
		if ( $row->meta_title != null ) {
			$output['page_title'] = $row->meta_title;
		} else {
			$output['page_title'] = $this->html_model->gen_title( $row->t_name );
		}
		// meta tags
		$meta = '';
		if ( $row->meta_description != null ) {
			$meta[] = '<meta name="description" content="'.$row->meta_description.'" />';
		}
		if ( $row->meta_keywords != null ) {
			$meta[] = '<meta name="keywords" content="'.$row->meta_keywords.'" />';
		}
		$output['page_meta'] = $this->html_model->gen_tags( $meta );
		unset( $meta );
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page( 'front/templates/taxterm/tag_view', $output );
	}// index
	
	
}

// EOF