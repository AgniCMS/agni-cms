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
 
class search extends MY_Controller {
	
	
	function __construct() {
		parent::__construct();
		// load model
		$this->load->model( array( 'posts_model' ) );
		// load helper
		$this->load->helper( array( 'date', 'language' ) );
		// load language
		$this->lang->load( 'post' );
		$this->lang->load( 'search' );
	}// __construct
	
	
	function index() {
		$q = trim( $this->input->get( 'q' ) );
		$output['q'] = htmlspecialchars( $q, ENT_QUOTES, config_item( 'charset' ) );
		if ( mb_strlen( $q ) > 1 ) {
			// search and list post
			$sql = 'select * from '.$this->db->dbprefix( 'posts' ).' as p';
			$sql .= ' left outer join '.$this->db->dbprefix( 'taxonomy_index' ).' as ti';
			$sql .= ' on p.post_id = ti.post_id';
			$sql .= ' left join '.$this->db->dbprefix( 'accounts' ).' as a';
			$sql .= ' on p.account_id = a.account_id';
			$sql .= ' inner join '.$this->db->dbprefix( 'post_revision' ).' as pr';
			$sql .= ' on p.post_id = pr.post_id';
			$sql .= ' and language = '.$this->db->escape( $this->lang->get_current_lang() );
			$sql .= ' and post_status = 1';
			if ( $q != null && $q != 'none' ) {
				$sql .= ' and (';
				$sql .= " post_name like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or post_uri like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or body_value like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or body_summary like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or pr.log like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or meta_title like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or meta_description like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or meta_keywords like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= " or theme_system_name like '%" . $this->db->escape_like_str( $q ) . "%'";
				$sql .= ')';
			}
			$sql .= ' group by p.post_id';
			// order and sort
			$sql .= ' order by post_update desc';
			// query for count total
			$query = $this->db->query( $sql );
			$total = $query->num_rows();
			$query->free_result();
			// pagination-----------------------------
			$this->load->library( 'pagination' );
			$config['base_url'] = site_url( 'search' ).'?q='.htmlspecialchars( trim( $this->input->get( 'q', true ) ), ENT_QUOTES, config_item( 'charset' ) );
			$config['per_page'] = $this->config_model->load_single( 'content_items_perpage' );
			$config['total_rows'] = $total;
			$config['query_string_segment'] = 'start';
			// pagination tags customize for bootstrap css framework
			$config['num_links'] = 3;
			$config['page_query_string'] = true;
			$config['full_tag_open'] = '<div class="pagination"><ul>';
			$config['full_tag_close'] = "</ul></div>\n";
			$config['first_tag_open'] = '<li>';
			$config['first_tag_close'] = '</li>';
			$config['last_tag_open'] = '<li>';
			$config['last_tag_close'] = '</li>';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			$config['cur_tag_open'] = '<li class="active"><a>';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			// end customize for bootstrap
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
		}
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title( $this->lang->line( 'search_search' ) );
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		// output
		$this->generate_page( 'front/templates/search/search_view', $output );
	}// index
	
	
}

// EOF