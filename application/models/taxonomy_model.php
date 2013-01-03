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

class taxonomy_model extends CI_Model {
	
	
	public $language;
	public $tax_type; // taxonomy type. category, tag, ...
	
	
	function __construct() {
		parent::__construct();
		// set language
		$this->language = $this->lang->get_current_lang();
		// for do some very hard thing like nlevel
		$this->fields = array('id'     => 'tid', 'parent' => 'parent_id' );
	}// __construct
	
	
	/**
	 * add
	 * @param array $data
	 * @return boolean 
	 */
	function add( $data = array() ) {
		if ( empty( $data ) ) {return false;}
		// check uri
		$data['t_uri'] = $this->nodup_uri( $data['t_uri'] );
		// insert
		$this->db->set( 'parent_id', $data['parent_id'] );
		$this->db->set( 'language', $this->language );
		$this->db->set( 't_type', $this->tax_type );
		$this->db->set( 't_name', $data['t_name'] );
		$this->db->set( 't_description', $data['t_description'] );
		$this->db->set( 't_uri', $data['t_uri'] );
		$this->db->set( 't_uri_encoded', urlencode( $data['t_uri'] ) );
		$this->db->set( 'meta_title', $data['meta_title'] );
		$this->db->set( 'meta_description', $data['meta_description'] );
		$this->db->set( 'meta_keywords', $data['meta_keywords'] );
		$this->db->set( 'theme_system_name', $data['theme_system_name'] );
		$this->db->insert( 'taxonomy_term_data' );
		// get insert id
		$tid = $this->db->insert_id();
		$this->db->set( 't_uris', $this->show_uri_tree( $tid ) );
		$this->db->where( 'tid', $tid );
		$this->db->update( 'taxonomy_term_data' );
		$this->rebuild();
		// insert to url alias
		$this->db->set( 'c_type', $this->tax_type );
		$this->db->set( 'c_id', $tid );
		$this->db->set( 'uri', $data['t_uri'] );
		$this->db->set( 'uri_encoded', urlencode( $data['t_uri'] ) );
		$this->db->set( 'language', $this->language );
		$this->db->insert( 'url_alias' );
		return true;
	}// add
	
	
	/**
	 * delete
	 * @param integer $tid
	 * @return boolean 
	 */
	function delete( $tid ) {
		if ( !is_numeric( $tid ) ) {return false;}
		// delete from menu items ------------------------------------------------------------------------------------
		// move child of this menu item to upper parent item
		$this->db->where( 'mi_type', $this->tax_type );
		$this->db->where( 'type_id', $tid );
		$this->db->where( 'language', $this->language );
		$query = $this->db->get( 'menu_items' );
		foreach ( $query->result() as $row ) {
			$this->db->set( 'parent_id', $row->parent_id );
			$this->db->where( 'parent_id', $row->mi_id );
			$this->db->update( 'menu_items' );
		}
		$query->free_result();
		// do delete
		$this->db->where( 'mi_type', $this->tax_type );
		$this->db->where( 'type_id', $tid );
		$this->db->where( 'language', $this->language );
		$this->db->delete( 'menu_items' );
		// rebuild menu items
		$this->load->model( 'menu_model' );
		$this->menu_model->rebuild();
		// end delete from menu items -------------------------------------------------------------------------------
		// delete url alias
		$this->db->where( 'c_type', $this->tax_type );
		$this->db->where( 'c_id', $tid );
		$this->db->where( 'language', $this->language );
		$this->db->delete( 'url_alias' );
		// update first child of this category to parent or root
		$this->db->where( 'tid', $tid );
		$query = $this->db->get( 'taxonomy_term_data' );
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			$this->db->where( 'parent_id', $tid );
			$query2 = $this->db->get( 'taxonomy_term_data' );
			foreach ( $query2->result() as $row2 ) {
				$this->db->set( 'parent_id', $row->parent_id );// set to parent of current item, current item is the one will be delete.
				$this->db->where( 'tid', $row2->tid );
				$this->db->update( 'taxonomy_term_data' );
			}
			$query2->free_result();
		}
		$query->free_result();
		// delete taxonomy index
		$this->db->where( 'tid', $tid );
		$this->db->delete( 'taxonomy_index' );
		// delete item
		$this->db->where( 'tid', $tid );
		$this->db->delete( 'taxonomy_term_data' );
		// delete frontpage category
		$this->db->where( 'tid', $tid );
		$this->db->delete( 'frontpage_category' );
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	function edit( $data = array() ) {
		if ( empty( $data ) ) {return false;}
		// check uri
		$data['t_uri'] = $this->nodup_uri( $data['t_uri'], true, $data['tid'] );
		// update
		$this->db->set( 'parent_id', $data['parent_id'] );
		$this->db->set( 't_name', $data['t_name'] );
		$this->db->set( 't_description', $data['t_description'] );
		$this->db->set( 't_uri', $data['t_uri'] );
		$this->db->set( 't_uri_encoded', urlencode( $data['t_uri'] ) );
		$this->db->set( 'meta_title', $data['meta_title'] );
		$this->db->set( 'meta_description', $data['meta_description'] );
		$this->db->set( 'meta_keywords', $data['meta_keywords'] );
		$this->db->set( 'theme_system_name', $data['theme_system_name'] );
		$this->db->where( 'tid', $data['tid'] );
		$this->db->where( 'language', $this->language );
		$this->db->where( 't_type', $this->tax_type );
		$this->db->update( 'taxonomy_term_data' );
		// update uris
		$uri_tree = $this->show_uri_tree( $data['tid'] );
		$this->db->set( 't_uris', $uri_tree );
		$this->db->where( 'tid', $data['tid'] );
		$this->db->update( 'taxonomy_term_data' );
		$this->rebuild();
		// update url alias
		$this->db->where( 'c_type', $this->tax_type );
		$this->db->where( 'c_id', $data['tid'] );
		$this->db->set( 'uri', $data['t_uri'] );
		$this->db->set( 'uri_encoded', urlencode( $data['t_uri'] ) );
		$this->db->where( 'language', $this->language );
		$this->db->update( 'url_alias' );
		// update menu_items
		$this->db->where( 'mi_type', $this->tax_type );
		$this->db->where( 'type_id', $data['tid'] );
		$this->db->set( 'link_url', $uri_tree );
		$this->db->set( 'link_text', $data['t_name'] );
		$this->db->update( 'menu_items' );
		//
		unset( $uri_tree );
		return true;
	}// edit
	
	
	/**
	 * get taxonomy index data.
	 * @param array $data
	 * @return mixed
	 */
	function get_taxonomy_index_data( $data = array() ) {
		if ( isset( $data['post_id'] ) ) {
			$this->db->where( 'post_id', $data['post_id'] );
		}
		if ( isset( $data['tid'] ) ) {
			$this->db->where( 'tid', $data['tid'] );
		}
		
		$query = $this->db->get( 'taxonomy_index' );
		
		if ( $query->num_rows() > 0 ) {
			return $query->row();
		}
		
		// there is no selected taxonomy_index
		return null;
	}// get_taxonomy_index_data
	
	
	/**
	 * list_item
	 * @return mixed 
	 * 
	 * create array object from the code of arnaud576875
	 * @link http://stackoverflow.com/questions/4843945/php-tree-structure-for-categories-and-sub-categories-without-looping-a-query
	 */
	function list_item() {
		// query sql
		$sql = 'select * from ' . $this->db->dbprefix( 'taxonomy_term_data' );
		$sql .= ' where language = ' . $this->db->escape( $this->language );
		$sql .= ' and t_type = ' . $this->db->escape( $this->tax_type );
		$sql .= ' order by t_name asc';
		$query = $this->db->query( $sql );
		if ( $query->num_rows() > 0 ) {
			$output = array();
			foreach ( $query->result() as $row )
				$output[$row->parent_id][] = $row;
			foreach ( $query->result() as $row ) if ( isset( $output[$row->tid] ) )
				$row->childs = $output[$row->tid];
			$output = $output[0];// this is important for prevent duplicate items
			return $output;
		}
		$query->free_result();
		return null;
	}// list_item
	
	
	/**
	 * list_tags
	 * @param admin|front $list_for
	 * @return mixed 
	 */
	function list_tags( $list_for = 'front' ) {
		$sql = 'select * from ' . $this->db->dbprefix( 'taxonomy_term_data' );
		$sql .= ' where language = ' . $this->db->escape( $this->language );
		$sql .= ' and t_type = ' . $this->db->escape( $this->tax_type );
		$q = htmlspecialchars( trim( $this->input->get( 'q' ) ) );
		if ( $q != null && $q != 'none' ) {
			$sql .= ' and (';
			$sql .= " t_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or t_description like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or t_uri like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or meta_title like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or meta_description like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or meta_keywords like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= " or theme_system_name like '%" . $this->db->escape_like_str( $q ) . "%'";
			$sql .= ')';
		}
		// order and sort
		$orders = strip_tags( trim( $this->input->get( 'orders' ) ) );
		$orders = ( $orders != null ? $orders : 't_name' );
		$sort = strip_tags( trim( $this->input->get( 'sort' ) ) );
		$sort = ( $sort != null ? $sort : 'asc' );
		$sql .= ' order by '.$orders.' '.$sort;
		// query for count total
		$query = $this->db->query( $sql );
		$total = $query->num_rows();
		$query->free_result();
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).( $q != null ?'&amp;q='.$q : '' );
		$config['total_rows'] = $total;
		$config['per_page'] = ( $list_for == 'admin' ? 20 : $this->config_model->load_single( 'content_items_perpage' ) );
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
	}// list_tags
	
	
	/**
	 *list taxonomy term index upto post
	 * @param type $post_id
	 * @return null 
	 */
	function list_taxterm_index( $post_id = '', $nohome_category = false ) {
		$home_category_id = $this->config_model->load_single( 'content_frontpage_category', $this->lang->get_current_lang() );
		
		//
		$this->db->join( 'taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'inner' );
		$this->db->where( 'post_id', $post_id );
		if ( $nohome_category && $home_category_id != null ) {
			$this->db->where( 'taxonomy_term_data.tid !=', $home_category_id );
		}
		$this->db->where( 't_type', $this->tax_type );
		$this->db->where( 'language', $this->language );
		$this->db->group_by( 'taxonomy_term_data.tid' );
		$this->db->order_by( 't_name', 'asc' );
		
		$query = $this->db->get( 'taxonomy_index' );
		
		if ( $query->num_rows() > 0 ) {
			return $query->result();
		}
		
		$query->free_result();
		
		return null;
	}// list_taxterm_index
	
	
	/**
	 * nodup_uri
	 * @param string $uri
	 * @param boolean $editmode
	 * @param integer $id
	 * @return string 
	 */
	function nodup_uri( $uri, $editmode = false, $id = '' ) {
		$uri = url_title( $uri );
		// load url model for check disallowed uri
		$this->load->model( 'url_model' );
		$uri = $this->url_model->validate_allow_url( $uri );
		//
		if ( $editmode == true ) {
			if ( !is_numeric( $id ) ) {return null;}
			// no duplicate uri edit mode
			$this->db->where( 'language', $this->language );
			$this->db->where( 't_type', $this->tax_type );
			$this->db->where( 't_uri', $uri );
			$this->db->where( 'tid', $id );
			if ( $this->db->count_all_results( 'taxonomy_term_data' ) > 0 ) {
				// nothing change, return old value
				return $uri;
			}
		}
		// loop check
		$found = true;
		$count = 0;
		$uri = ( $uri == null ? 't' : $uri );
		do {
			$new_uri = ($count === 0 ? $uri : $uri . "-" . $count);
			$this->db->where( 'language', $this->language );
			$this->db->where( 't_type', $this->tax_type );
			$this->db->where( 't_uri', $new_uri );
			if ( $this->db->count_all_results( 'taxonomy_term_data' ) > 0 ) {
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
	 * show_taxterm_info
	 * @param mixed $check_val
	 * @param string $check_field
	 * @param string $return_field
	 * @return string 
	 */
	function show_taxterm_info( $check_val = '', $check_field = 'tid', $return_field = 't_name' ) {
		$this->db->where( 'language', $this->language );
		$this->db->where( 't_type', $this->tax_type );
		$this->db->where( $check_field, $check_val );
		$query = $this->db->get( 'taxonomy_term_data' );
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			$query->free_result();
			return $row->$return_field;
		}
		$query->free_result();
		return null;
	}// show_taxterm_info
	
	
	/**
	 * show_uri_tree
	 * @param type $tid
	 * @return string 
	 */
	function show_uri_tree( $tid = '' ) {
		$end_depth = 'no';
		do {
			$this->db->where( 'tid', $tid );
			$this->db->where( 'language', $this->language );
			$this->db->where( 't_type', $this->tax_type );
			$query = $this->db->get( 'taxonomy_term_data' );
			if ( $query->num_rows() > 0 ) {
				$row = $query->row();
				$query->free_result();
				$output[] = $row->t_uri_encoded;
				$tid = $row->parent_id;
				if ( $row->parent_id == '0' ) {
					$end_depth = 'yes';
				}
			} else {
				$query->free_result();
				$end_depth = 'yes';
			}
		} while ( $end_depth == 'no' );
		// reverse array
		$output = array_reverse( $output );
		$uri = '';
		foreach ( $output as $key => $item ) {
			$uri .= $item;
			if ( end($output) != $item ) {
				$uri .= '/';
			}
		}
		// remove junk var
		unset( $end_depth, $query, $row, $output );
		return $uri;
	}// show_uri_tree
	
	
	/**
	 * update_total_post
	 * @param integer $tid
	 * @return boolean 
	 */
	function update_total_post( $tid = '' ) {
		if ( !is_numeric( $tid ) ) {return false;}
		$this->db->where( 'tid', $tid );
		$total = $this->db->count_all_results( 'taxonomy_index' );
		// update total posts in tax.term
		$this->db->set( 't_total', $total );
		$this->db->where( 'tid', $tid );
		$this->db->update( 'taxonomy_term_data' );
		return true;
	}// update_total_post
	
	
	######################################################################
	/**
	 *@link http://www.phpriot.com/articles/nested-trees-2 
	 */
	
	
	/**
	 * Generate the tree data. A single call to this generates the n-values for
	 * 1 node in the tree. This function assigns the passed in n value as the
	 * node's nleft value. It then processes all the node's children (which
	 * in turn recursively processes that node's children and so on), and when
	 * it is finally done, it takes the update n-value and assigns it as its
	 * nright value. Because it is passed as a reference, the subsequent changes
	 * in subrequests are held over to when control is returned so the nright
	 * can be assigned.
	 *
	 * @param   array   &$arr   A reference to the data array, since we need to
	 *                          be able to update the data in it
	 * @param   int     $id     The ID of the current node to process
	 * @param   int     $level  The nlevel to assign to the current node
	 * @param   int     &$n     A reference to the running tally for the n-value
	 */
	function _generateTreeData(&$arr, $id, $level) {
		$arr[$id]->nlevel = $level;

		// loop over the node's children and process their data
		// before assigning the nright value
		foreach ($arr[$id]->children as $child_id) {
			$this->_generateTreeData($arr, $child_id, $level + 1);
		}
	}
	
	
	/**
	 * A utility function to return an array of the fields
	 * that need to be selected in SQL select queries
	 *
	 * @return  array   An indexed array of fields to select
	 */
	function _getFields() {
		return array($this->fields['id'], $this->fields['parent'], 't_type', 'nlevel');
	}
	
	
	/**
	 * Fetch the tree data, nesting within each node references to the node's children
	 *
	 * @return  array       The tree with the node's child data
	 */
	function _getTreeWithChildren() {
		$idField = $this->fields['id'];
		$parentField = $this->fields['parent'];

		$query = sprintf('select %s from %s', join(',', $this->_getFields()), $this->db->dbprefix( 'taxonomy_term_data' ));
		$query .= ' where t_type = '.$this->db->escape( $this->tax_type ).'';
		$query .= ' and language = '.$this->db->escape( $this->lang->get_current_lang() ).'';

		$result = $this->db->query($query);

		// create a root node to hold child data about first level items
		$root = new stdClass;
		$root->$idField = 0;
		$root->children = array();

		$arr = array($root);

		// populate the array and create an empty children array
		foreach ($result->result() as $row) {
			$arr[$row->$idField] = $row;
			$arr[$row->$idField]->children = array();
		}

		// now process the array and build the child data
		foreach ($arr as $id => $row) {
			if (isset($row->$parentField))
				$arr[$row->$parentField]->children[$id] = $id;
		}

		return $arr;
	}
	
	
	/**
	 * Rebuilds the tree data and saves it to the database
	 */
	function rebuild() {
		$data = $this->_getTreeWithChildren();
		
		$level = 0; // need a variable to hold the running level tally
		// invoke the recursive function. Start it processing
		// on the fake "root node" generated in getTreeWithChildren().
		// because this node doesn't really exist in the database, we
		// give it an initial nleft value of 0 and an nlevel of 0.
		$this->_generateTreeData($data, 0, 0);

		// at this point the the root node will have nleft of 0, nlevel of 0
		// and nright of (tree size * 2 + 1)

		foreach ($data as $id => $row) {

			// skip the root node
			if ($id == 0)
				continue;

			$query = sprintf('update %s set nlevel = %d where %s = %d', $this->db->dbprefix( 'taxonomy_term_data' ), $row->nlevel, $this->fields['id'], $id);
			$this->db->query($query);
		}
	}
	
	
}

