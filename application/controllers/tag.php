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
		$this->load->model( array( 'posts_model' ) );
		// load helper
		$this->load->helper( array( 'date', 'language' ) );
		// load language
		$this->lang->load( 'post' );
	}// __construct
	
	
	function _remap( $att1 = '', $att2 = '' ) {
		$this->index( $att1, $att2 );
	}// _remap
	
	
	function index( $uri = '', $att2 = '' ) {
		if ( !empty( $att2 ) ) {show_404(); exit;}// prevent duplicate content (localhost/tag/tagname and localhost/tag/tagname/aaa can be same result, just 404 it). good for seo.
		// load category for title, metas
		$this->db->where( 't_uri_encoded', $uri );
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
		$query->free_result();
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
		$sql = 'select * from '.$this->db->dbprefix( 'posts' ).' as p';
		$sql .= ' left outer join '.$this->db->dbprefix( 'taxonomy_index' ).' as ti';
		$sql .= ' on p.post_id = ti.post_id';
		$sql .= ' left join '.$this->db->dbprefix( 'accounts' ).' as a';
		$sql .= ' on p.account_id = a.account_id';
		$sql .= ' inner join '.$this->db->dbprefix( 'post_revision' ).' as pr';
		$sql .= ' on p.post_id = pr.post_id';
		$sql .= ' where post_type = '.$this->db->escape( 'article' );
		$sql .= ' and language = '.$this->db->escape( $this->lang->get_current_lang() );
		$sql .= ' and post_status = 1';
		$sql .= ' and ti.tid = '.$this->db->escape( $row->tid );
		$sql .= ' group by p.post_id';
		// order and sort
		$sql .= ' order by position desc, post_update desc';
		// query for count total
		$query = $this->db->query( $sql );
		$total = $query->num_rows();
		$query->free_result();
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		$config['base_url'] = site_url( $this->uri->uri_string() ).'?';
		$config['per_page'] = $this->config_model->load_single( 'content_items_perpage' );
		$config['total_rows'] = $total;
		$config['query_string_segment'] = 'start';
		$config['num_links'] = 5;
		$config['page_query_string'] = true;
		$config['full_tag_open'] = '<div class="pagination">';
		$config['full_tag_close'] = "</div>\n";
		$config['first_tag_close'] = '';
		$config['last_tag_open'] = '';
		$config['first_link'] = '|&lt;';
		$config['last_link'] = '&gt;|';
		$this->pagination->initialize( $config );
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		$sql .= ' limit '.( $this->input->get( 'start' ) == null ? '0' : $this->input->get( 'start' ) ).', '.$config['per_page'].';';
		$query = $this->db->query( $sql);
		if ( $query->num_rows() > 0 ) {
			$output['list_item']['items'] = $query->result();
			$output['pagination'] = $this->pagination->create_links();
			$query->free_result();
		}
		$query->free_result();
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
		$this->generate_page( 'front/templates/tag_view', $output );
	}// index
	
	
}

// EOF