<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 * ------------------------------------------------------------------
 * this model works with url. eg: check allowed and disallowed uri in taxterm or posts, manage url redirect.
 */
 
class url_model extends CI_Model {
	
	
	public $c_type;
	public $language;
	
	
	function __construct() {
		parent::__construct();
		// set language
		$this->language = $this->lang->get_current_lang();
	}// __construct
	
	
	/**
	 * add_redirect
	 * @param array $data
	 * @return mixed 
	 */
	function add_redirect( $data = array() ) {
		if ( !is_array( $data ) ) {return false;}
		// re-check uri
		$data['uri'] = $this->nodup_uri( $data['uri'] );
		// insert
		$this->db->set( 'c_type', $this->c_type );
		$this->db->set( 'uri', $data['uri'] );
		$this->db->set( 'uri_encoded', urlencode_except_slash( $data['uri'] ) );
		$this->db->set( 'redirect_to', $data['redirect_to'] );
		$this->db->set( 'redirect_to_encoded', $this->encode_redirect_to( $data['redirect_to'] ) );
		$this->db->set( 'redirect_code', $data['redirect_code'] );
		$this->db->set( 'language', $this->language );
		$this->db->insert( 'url_alias' );
		// get insert id
		$output['id'] = $this->db->insert_id();
		$output['result'] = true;
		return $output;
	}// add_redirect
	
	
	/**
	 * delete_redirect
	 * @param integer $alias_id 
	 * @return boolean
	 */
	function delete_redirect( $alias_id = '' ) {
		$this->db->where( 'c_type', $this->c_type );
		$this->db->where( 'alias_id', $alias_id );
		$this->db->delete( 'url_alias' );
		return true;
	}// delete_redirect
	
	
	/**
	 * edit_redirect
	 * @param array $data
	 * @return mixed 
	 */
	function edit_redirect( $data = array() ) {
		if ( !is_array( $data ) ) {return false;}
		// re-check uri
		$data['uri'] = $this->nodup_uri( $data['uri'], true, $data['alias_id'] );
		// insert
		$this->db->set( 'uri', $data['uri'] );
		$this->db->set( 'uri_encoded', urlencode_except_slash( $data['uri'] ) );
		$this->db->set( 'redirect_to', $data['redirect_to'] );
		$this->db->set( 'redirect_to_encoded', $this->encode_redirect_to( $data['redirect_to'] ) );
		$this->db->set( 'redirect_code', $data['redirect_code'] );
		$this->db->where( 'c_type', $this->c_type );
		$this->db->where( 'language', $this->language );
		$this->db->where( 'alias_id', $data['alias_id'] );
		$this->db->update( 'url_alias' );
		$output['result'] = true;
		return $output;
	}// edit_redirect
	
	
	/**
	 * encode_redirect_to
	 * @param string $redirect_to
	 * @return string 
	 */
	function encode_redirect_to( $redirect_to = '' ) {
		if ( $redirect_to == null ) {return null;}
		return urlencode_except_slash( $redirect_to );
	}// encode_redirect_to
	
	
	function list_item( $list_for = 'admin' ) {
		$sql = 'select * from '.$this->db->dbprefix( 'url_alias' );
		$sql .= ' where c_type = '.$this->db->escape( $this->c_type );
		$sql .= ' and language = '.$this->db->escape( $this->language );
		$q = htmlspecialchars( trim( $this->input->get( 'q' ) ) );
		if ( $q != null ) {
			$sql .= ' and (';
			$sql .= " uri like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or uri_encoded like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or redirect_to like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or redirect_to_encoded like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or redirect_code like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= ')';
		}
		// order and sort
		$orders = strip_tags( trim( $this->input->get( 'orders' ) ) );
		$orders = ( $orders != null ? $orders : 'uri' );
		$sort = strip_tags( trim( $this->input->get( 'sort' ) ) );
		$sort = ( $sort != null ? $sort : 'asc' );
		$sql .= ' order by '.$orders.' '.$sort;
		// query for count total
		$query = $this->db->query( $sql );
		$total = $query->num_rows();
		$query->free_result();
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		if ( $list_for == 'admin' ) {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).( $q != null ?'&amp;q='.$q : '' );
			$config['per_page'] = 20;
		} else {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?'.( $q != null ?'q='.$q : '' );
			$config['per_page'] = $this->config_model->load_single( 'content_items_perpage' );
		}
		$config['total_rows'] = $total;
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
		$sql .= ' limit '.( $this->input->get( 'per_page' ) == null ? '0' : $this->input->get( 'per_page' ) ).', '.$config['per_page'].';';
		$query = $this->db->query( $sql);
		if ( $query->num_rows() > 0 ) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		$query->free_result();
		return null;
	}// list_item
	
	
	/**
	 * nodup_uri
	 * @param string $uri
	 * @param boolean $editmode
	 * @param integer $id
	 * @return string 
	 */
	function nodup_uri( $uri, $editmode = false, $id = '' ) {
		$uri = $this->validate_allow_url( $uri );
		// prevent url_title cut slash out (/)------------------------------------------------
		$uri_raw = explode( '/', $uri );
		if ( !is_array( $uri_raw ) ) {return null;}
		foreach ( $uri_raw as $uri ) {
			$uri = url_title( $uri );
			$output[] = $uri;
		}
		unset( $uri_raw );
		// got array. merge it to string
		if ( isset( $output ) && is_array( $output ) ) {
			$return = '';
			foreach ( $output as $a_output ) {
				$return .= $a_output;
				if ( $a_output != end( $output ) ) {
					$return .= '/';
				}
			}
			$uri = $return;
			unset( $return, $output, $a_output );
		}
		// end prevent url_title cut slash out (/)------------------------------------------------
		// start checking
		if ( $editmode == true ) {
			if ( !is_numeric( $id ) ) {return null;}
			// no duplicate uri edit mode
			$this->db->where( 'language', $this->language );
			$this->db->where( 'c_type', $this->c_type );
			$this->db->where( 'uri', $uri );
			$this->db->where( 'alias_id', $id );
			if ( $this->db->count_all_results( 'url_alias' ) > 0 ) {
				// nothing change, return old value
				return $uri;
			}
		}
		// loop check
		$found = true;
		$count = 0;
		$uri = ( $uri == null ? 'rdr' : $uri );
		do {
			$new_uri = ($count === 0 ? $uri : $uri . "-" . $count);
			$this->db->where( 'language', $this->language );
			$this->db->where( 'c_type', $this->c_type );
			$this->db->where( 'uri', $new_uri );
			if ( $this->db->count_all_results( 'url_alias' ) > 0 ) {
				$found = true;
			} else {
				$found = false;
			}
			$count++;
		} while ( $found === true );
		unset( $found, $count );
		return $new_uri;
	}// nodup_uri
	
	
	/**
	 * validate_allow_url
	 * @param string $uri
	 * @return string 
	 */
	function validate_allow_url( $uri = '' ) {
		if ( $uri == null ) {return null;}
		// any disallowed uri list here as array
		$disallowed_url = array(
			'account',
			'account/changeemail2',
			'account/confirm-register',
			'account/edit-profile',
			'account/forgotpw',
			'account/login',
			'account/logout',
			'account/register',
			'account/resend-activate',
			'account/resetpw2',
			'account/view-logins',
			
			'site-admin',
			'site-admin/',
			
			'area',
			'area/demo',
			
			'author',
			
			'category',
			
			'comment',
			'comment/comment_view',
			'comment/delete',
			'comment/edit',
			'comment/list_comments',
			'comment/post_comment',
			
			'index',
			
			'post',
			'post/preview',
			'post/revision',
			'post/view',
			
			'search',
			
			'tag',
			
			'modules',
			'modules/core',
			
			'public',
			'public/css-fw',
			'public/images',
			'public/js',
			'public/themes',
			'public/upload',
			
			'index.php'
		);
		// start to check
		if ( in_array( $uri, $disallowed_url ) ) {
			return 'disallowed-uri';
		}
		// not found in disallowed uri
		return $uri;
	}// validate_allow_url
	
	
}

// EOF