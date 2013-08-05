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
 
class menu_model extends CI_Model {
	
	
	public $language;
	
	
	function __construct() {
		parent::__construct();
		
		// set language
		$this->language = $this->lang->get_current_lang();
		
		// for do some very hard thing like nlevel
		$this->fields = array('id'     => 'mi_id', 'parent' => 'parent_id');
	}// __construct
	
	
	/**
	 * add_group
	 * @param array $data
	 * @return boolean 
	 */
	function add_group($data = array()) {
		// set additional data
		$data['language'] = $this->language;
		
		$this->db->insert('menu_groups', $data);
		return true;
	}// add_group
	
	
	/**
	 * add_item
	 * @param array $data 
	 * @return boolean
	 */
	function add_item($data = array()) {
		// set additional data for insert to db.
		$data['position'] = $this->get_mi_newposition($data['mg_id'], $this->language);
		$data['language'] = $this->language;
		
		if (!is_array($data['type_id'])) {
			$data['type_id'] = array($data['type_id']);
			return $this->add_item($data);
		} elseif (is_array($data['type_id'])) {
			
			foreach ($data['type_id'] as $type_id) {
				// set type_id for insert to db.
				$data['type_id'] = $type_id;
				
				// prepare data for menu type (even if it is post, term)
				switch ($data['mi_type']) {
					case 'category':
					case 'tag':
						$this->db->where('t_type', $data['mi_type']);
						$this->db->where('tid', $type_id);
						$query = $this->db->get('taxonomy_term_data');
						if ($query->num_rows() > 0) {
							$row = $query->row();
							$data['link_text'] = $row->t_name;
							$data['link_url'] =$row->t_uris;
						}
						$query->free_result();
						break;
					case 'article':
					case 'page':
						$this->db->where('post_type', $data['mi_type']);
						$this->db->where('post_id', $type_id);
						$query = $this->db->get('posts');
						if ($query->num_rows() > 0) {
							$row = $query->row();
							$data['link_text'] = $row->post_name;
							$data['link_url'] = $row->post_uri_encoded;
						}
						$query->free_result();
						break;
					default:
						break;
				}
				unset($query, $row);
				
				$this->db->insert('menu_items', $data);
			}
			
		}
		
		// done. rebuild nlevel
		$this->rebuild();
		return true;
	}// add_item
	
	
	/**
	 * delete_group
	 * @param integer $mg_id
	 * @return boolean 
	 */
	function delete_group($mg_id = '') {
		if (!is_numeric($mg_id)) {return false;}
		
		// delete from menu items table
		$this->db->where('mg_id', $mg_id);
		$this->db->delete('menu_items');
		
		// delete from menu groups table
		$this->db->where('mg_id', $mg_id);
		$this->db->delete('menu_groups');
		
		return true;
	}// delete_group
	
	
	function delete_item($mi_id = '') {
		// delete children items
		$this->db->where('parent_id', $mi_id);
		$this->db->where('language', $this->language);
		$query = $this->db->get('menu_items');
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $row) {
				$this->delete_item($row->mi_id);
			}
		}
		$query->free_result();
		
		// delete now
		$this->db->where('mi_id', $mi_id);
		$this->db->where('language', $this->language);
		$this->db->delete('menu_items');
		
		// done
		return true;
	}// delete_item
	
	
	/**
	 * edit_group
	 * @param array $data
	 * @return boolean 
	 */
	function edit_group($data = array()) {
		$this->db->where('language', $this->language);
		$this->db->where('mg_id', $data['mg_id']);
		$this->db->update('menu_groups', $data);
		
		return true;
	}// edit_group
	
	
	/**
	 * edit menu item
	 * @param array $data
	 * @return boolean
	 */
	function edit_item($data = array()) {
		if (isset($data['mi_id'])) {
			$this->db->where('mi_id', $data['mi_id']);
		}
		$this->db->update('menu_items', $data);
		
		// done
		return true;
	}// edit_item
	
	
	/**
	 * get menu group data from db
	 * @param array $data
	 * @return mixed
	 */
	function get_mg_data_db($data = array()) {
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('menu_groups');
		
		return $query->row();
	}// get_mg_data_db
	
	
	/**
	 * get menu item data from db
	 * @param array $data
	 * @return mixed
	 */
	function get_mi_data_db($data = array()) {
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('menu_items');
		
		return $query->row();
	}// get_mi_data_db
	
	
	/**
	 * get_mi_newposition
	 * @param integer $mg_id
	 * @param string $language
	 * @return int 
	 */
	function get_mi_newposition($mg_id = '', $language = '') {
		$this->db->where('mg_id', $mg_id);
		$this->db->where('language', $language);
		$this->db->order_by('position', 'desc');
		
		$query = $this->db->get('menu_items');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			return ($row->position+1);
		} else {
			$query->free_result();
			return 1;
		}
	}// get_mi_newposition
	
	
	/**
	 * list_group
	 * @param boolean $limit
	 * @return mixed 
	 */
	function list_group($limit = true) {
		$this->db->where('language', $this->language);
		
		// orders & sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders == null ? 'mg_name' : $orders);
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort == null ? 'asc' : $sort);
		$this->db->order_by($orders, $sort);
		
		if ($limit == true) {
			// clone object before run $this->db->get()
			$this_db = clone $this->db;
			
			// query for count total
			$query = $this->db->get('menu_groups');
			$total = $query->num_rows();
			$query->free_result();
			
			// restore $this->db object
			$this->db = $this_db;
			unset($this_db);
			
			// pagination-----------------------------
			$this->load->library('pagination');
			$config['base_url'] = site_url($this->uri->uri_string()).'?orders='.$orders.'&amp;sort='.$sort;
			$config['per_page'] = 20;
			$config['total_rows'] = $total;
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
			$this->pagination->initialize($config);
			// pagination create links in controller or view. $this->pagination->create_links();
			// end pagination-----------------------------
			
			// limit query
			$this->db->limit($config['per_page'], ($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')));
		}
		
		$query = $this->db->get('menu_groups');
		
		if ($query->num_rows() > 0) {
			if (isset($total)) {$output['total'] = $total;}
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// list_group
	
	
	/**
	 * list_item
	 * @param integer $mg_id
	 * @return mixed 
	 */
	function list_item($mg_id = '') {
		if (!is_numeric($mg_id)) {return null;}
		
		$this->db->where('mg_id', $mg_id);
		$this->db->where('language', $this->language);
		$this->db->order_by('position', 'asc');
		$query = $this->db->get('menu_items');
		
		if ($query->num_rows() > 0) {
			$output = array();
			foreach ($query->result() as $row)
				$output[$row->parent_id][] = $row;
			foreach ($query->result() as $row) if (isset($output[$row->mi_id]))
				$row->childs = $output[$row->mi_id];
			$output = $output[0];// this is important for prevent duplicate items
			return $output;
		}
		
		$query->free_result();
		return null;
	}// list_item
	
	
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
		return array($this->fields['id'], $this->fields['parent'], 'mg_id', 'nlevel');
	}
	
	
	/**
	 * Fetch the tree data, nesting within each node references to the node's children
	 *
	 * @return  array       The tree with the node's child data
	 */
	function _getTreeWithChildren() {
		$idField = $this->fields['id'];
		$parentField = $this->fields['parent'];

		$query = sprintf('select %s from %s', join(',', $this->_getFields()), $this->db->dbprefix('menu_items'));
		$query .= ' where mg_id = '.$this->db->escape($this->uri->segment(4)).'';

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

			$query = sprintf('update %s set nlevel = %d where %s = %d', $this->db->dbprefix('menu_items'), $row->nlevel, $this->fields['id'], $id);
			$this->db->query($query);
		}
	}
	
	
}

// EOF