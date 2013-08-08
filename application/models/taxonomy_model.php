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

class taxonomy_model extends CI_Model 
{
	
	
	public $language;
	public $tax_type; // taxonomy type. category, tag, ...
	
	
	public function __construct() 
	{
		parent::__construct();
		// set language
		$this->language = $this->lang->get_current_lang();
		
		// for do some very hard thing like nlevel
		$this->fields = array('id'     => 'tid', 'parent' => 'parent_id');
	}// __construct
	
	
	/**
	 * add
	 * @param array $data
	 * @return boolean 
	 */
	public function add($data = array()) 
	{
		if (empty($data)) {return false;}
		
		// check uri
		$data['t_uri'] = $this->noDupTaxonomyUri($data['t_uri']);
		
		// additional data for insert to db
		$data['language'] = $this->language;
		$data['t_type'] = $this->tax_type;
		$data['t_uri_encoded'] = urlencode($data['t_uri']);
		
		// insert
		$this->db->insert('taxonomy_term_data', $data);
		
		// get insert id
		$tid = $this->db->insert_id();
		
		// set t_uris (it is taxonomy tree)
		$this->db->set('t_uris', $this->showTaxTermUriTree($tid));
		$this->db->where('tid', $tid);
		$this->db->update('taxonomy_term_data');
		
		// rebuild tree level.
		$this->reBuildTaxTerm();
		
		// additional data for url_alias
		$data_alias['c_type'] = $this->tax_type;
		$data_alias['c_id'] = $tid;
		$data_alias['uri'] = $data['t_uri'];
		$data_alias['uri_encoded'] = urlencode($data['t_uri']);
		$data_alias['language'] = $this->language;
		
		// insert to url alias
		$this->db->insert('url_alias', $data_alias);
		return true;
	}// add
	
	
	/**
	 * delete
	 * @param integer $tid
	 * @return boolean 
	 */
	public function delete($tid) 
	{
		if (!is_numeric($tid)) {return false;}
		
		// delete from menu items ------------------------------------------------------------------------------------
		// move child of this menu item to upper parent item
		$this->db->where('mi_type', $this->tax_type);
		$this->db->where('type_id', $tid);
		$this->db->where('language', $this->language);
		
		$query = $this->db->get('menu_items');
		
		foreach ($query->result() as $row) {
			$this->db->set('parent_id', $row->parent_id);
			$this->db->where('parent_id', $row->mi_id);
			$this->db->update('menu_items');
		}
		$query->free_result();
		
		// do delete menu item
		$this->db->where('mi_type', $this->tax_type);
		$this->db->where('type_id', $tid);
		$this->db->where('language', $this->language);
		$this->db->delete('menu_items');
		
		// rebuild menu items
		$this->load->model('menu_model');
		$this->menu_model->reBuildMenu();
		// end delete from menu items -------------------------------------------------------------------------------
		
		// delete url alias
		$this->db->where('c_type', $this->tax_type);
		$this->db->where('c_id', $tid);
		$this->db->where('language', $this->language);
		$this->db->delete('url_alias');
		
		// update first child of this category to parent or root
		$this->db->where('tid', $tid);
		$query = $this->db->get('taxonomy_term_data');
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$this->db->where('parent_id', $tid);
			$query2 = $this->db->get('taxonomy_term_data');
			foreach ($query2->result() as $row2) {
				$this->db->set('parent_id', $row->parent_id);// set to parent of current item, current item is the one will be delete.
				$this->db->where('tid', $row2->tid);
				$this->db->update('taxonomy_term_data');
			}
			$query2->free_result();
		}
		$query->free_result();
		
		// delete taxonomy index
		$this->db->where('tid', $tid);
		$this->db->delete('taxonomy_index');
		
		// delete item
		$this->db->where('tid', $tid);
		$this->db->delete('taxonomy_term_data');
		
		// delete frontpage category
		$this->db->where('tid', $tid);
		$this->db->delete('frontpage_category');
		
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data
	 * @return boolean 
	 */
	public function edit($data = array(), $data_ua = array(), $data_mi = array()) 
	{
		if (empty($data)) {return false;}
		
		// check uri
		$data['t_uri'] = $this->noDupTaxonomyUri($data['t_uri'], true, $data['tid']);
		$data_ua['uri'] = $this->noDupTaxonomyUri($data_ua['uri'], true, $data['tid']);
		
		// additional data for taxonomy_term_data table
		$data['t_uri_encoded'] = urlencode($data['t_uri']);
		$data_ua['uri_encoded'] = urlencode($data_ua['uri']);
		
		// update
		$this->db->where('tid', $data['tid']);
		$this->db->where('language', $this->language);
		$this->db->where('t_type', $this->tax_type);
		$this->db->update('taxonomy_term_data', $data);
		
		// update uris
		$uri_tree = $this->showTaxTermUriTree($data['tid']);
		$this->db->set('t_uris', $uri_tree);
		$this->db->where('tid', $data['tid']);
		$this->db->update('taxonomy_term_data');
		
		// rebuild tree.
		$this->reBuildTaxTerm();
		
		// additional data for url_alias table
		$data['uri_encoded'] = $data['t_uri_encoded'];
		
		// update url alias
		$this->db->where('c_type', $this->tax_type);
		$this->db->where('c_id', $data['tid']);
		$this->db->where('language', $this->language);
		$this->db->update('url_alias', $data_ua);
		
		// additional data for menu_items table
		$data_mi['link_url'] = $uri_tree;
		
		// update menu_items
		$this->db->where('mi_type', $this->tax_type);
		$this->db->where('type_id', $data['tid']);
		$this->db->update('menu_items', $data_mi);
		
		// done
		unset($uri_tree);
		return true;
	}// edit
	
	
	/**
	 * get taxonomy fields from db
	 * @param integer $tid
	 * @param array $data
	 * @return mixed
	 */
	public function getTaxonomyFields($tid = '', $data = array()) 
	{
		if (!is_numeric($tid)) {
			return null;
		}
		
		$this->db->from('taxonomy_fields')
				->where('tid', $tid);
		
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		
		$query->free_result();
		
		return null;
	}// getTaxonomyFields
	
	
	/**
	 * get taxonomy index data.
	 * @param array $data
	 * @return mixed
	 */
	public function getTaxonomyIndexData($data = array()) 
	{
		if (isset($data['post_id'])) {
			$this->db->where('post_id', $data['post_id']);
		}
		if (isset($data['tid'])) {
			$this->db->where('tid', $data['tid']);
		}
		
		$query = $this->db->get('taxonomy_index');
		
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		
		// there is no selected taxonomy_index
		return null;
	}// getTaxonomyIndexData
	
	
	/**
	 * get taxonomy_term_data from db.
	 * @param array $data
	 * @return mixed
	 */
	public function getTaxonomyTermDataDb($data = array()) 
	{
		if ($this->tax_type != null) {
			$this->db->where('t_type', $this->tax_type);
		}
		
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('taxonomy_term_data');
		
		return $query->row();
	}// getTaxonomyTermDataDb
	
	
	/**
	 * list tags
	 * @param array $data
	 * @return mixed 
	 */
	public function listTags($data = array()) 
	{
		$this->db->where('language', $this->language);
		$this->db->where('t_type', $this->tax_type);
		
		// search
		$q = trim($this->input->get('q'));
		if ($q != null && $q != 'none') {
			$like_data[0]['field'] = 'taxonomy_term_data.t_name';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'taxonomy_term_data.t_description';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'taxonomy_term_data.t_uri';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'taxonomy_term_data.meta_title';
			$like_data[3]['match'] = $q;
			$like_data[4]['field'] = 'taxonomy_term_data.meta_description';
			$like_data[4]['match'] = $q;
			$like_data[5]['field'] = 'taxonomy_term_data.meta_keywords';
			$like_data[5]['match'] = $q;
			$like_data[6]['field'] = 'taxonomy_term_data.theme_system_name';
			$like_data[6]['match'] = $q;
			$this->db->like_group($like_data);
			unset($like_data);
		}
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 't_name');
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'asc');
		$this->db->order_by($orders, $sort);
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('taxonomy_term_data');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		//html encode search keyword for create links
		$q = urlencode(htmlspecialchars($q));
		
		// pagination-----------------------------
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->uri->uri_string()).'?' . generate_querystring_except(array('per_page'));
		if (isset($data['per_page']) && is_numeric($data['per_page'])) {
			$config['per_page'] = $data['per_page'];
		} else {
			$config['per_page'] = (isset($data['list_for']) && $data['list_for'] == 'admin' ? 20 : $this->config_model->loadSingle('content_items_perpage'));
		}
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
		
		$query = $this->db->get('taxonomy_term_data');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listTags
	
	
	/**
	 * list taxonomy term
	 * @return mixed 
	 * 
	 * create array object from the code of arnaud576875
	 * @link http://stackoverflow.com/questions/4843945/php-tree-structure-for-categories-and-sub-categories-without-looping-a-query
	 */
	public function listTaxTerm() 
	{
		$this->db->where('language', $this->language);
		$this->db->where('t_type', $this->tax_type);
		$this->db->order_by('t_name', 'asc');
		
		$query = $this->db->get('taxonomy_term_data');
		
		if ($query->num_rows() > 0) {
			$output = array();
			
			foreach ($query->result() as $row)
				$output[$row->parent_id][] = $row;
			
			foreach ($query->result() as $row) if (isset($output[$row->tid]))
				$row->childs = $output[$row->tid];
			
			$output = $output[0];// this is important for prevent duplicate items
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listTaxTerm
	
	
	/**
	 * alias of method listTaxTerm.
	 */
	public function list_item() 
	{
		return $this->listTaxTerm();
	}// list_item
	
	
	/**
	 *list taxonomy term index upto post
	 * @param type $post_id
	 * @return null 
	 */
	public function listTaxTermIndex($post_id = '', $nohome_category = false) 
	{
		$home_category_id = $this->config_model->loadSingle('content_frontpage_category', $this->lang->get_current_lang());
		
		//
		$this->db->join('taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'inner');
		$this->db->where('post_id', $post_id);
		if ($nohome_category && $home_category_id != null) {
			$this->db->where('taxonomy_term_data.tid !=', $home_category_id);
		}
		$this->db->where('t_type', $this->tax_type);
		$this->db->where('language', $this->language);
		$this->db->group_by('taxonomy_term_data.tid');
		$this->db->order_by('t_name', 'asc');
		
		$query = $this->db->get('taxonomy_index');
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		
		$query->free_result();
		
		return null;
	}// listTaxTermIndex
	
	
	/**
	 * list taxonomy term total
	 * get total taxonomy from list taxonomy term method.
	 * @return integer
	 */
	public function listTaxTermTotal() 
	{
		// copy these query from listTaxTerm() method
		$this->db->where('language', $this->language);
		$this->db->where('t_type', $this->tax_type);
		$this->db->order_by('t_name', 'asc');
		
		// run query
		$query = $this->db->get('taxonomy_term_data');
		
		return $query->num_rows();
	}// listTaxTermTotal
	
	
	/**
	 * no duplicate taxonomy uri
	 * @param string $uri
	 * @param boolean $editmode
	 * @param integer $id
	 * @return string 
	 */
	public function noDupTaxonomyUri($uri, $editmode = false, $id = '') 
	{
		$uri = url_title($uri);
		
		// load url model for check disallowed uri
		$this->load->model('url_model');
		$uri = $this->url_model->validateAllowUrl($uri);
		
		//
		if ($editmode == true) {
			if (!is_numeric($id)) {return null;}
			
			// no duplicate uri edit mode
			$this->db->where('language', $this->language);
			$this->db->where('t_type', $this->tax_type);
			$this->db->where('t_uri', $uri);
			$this->db->where('tid', $id);
			
			if ($this->db->count_all_results('taxonomy_term_data') > 0) {
				// nothing change, return old value
				return $uri;
			}
		}
		
		// loop check
		$found = true;
		$count = 0;
		$uri = ($uri == null ? 't' : $uri);
		do {
			$new_uri = ($count === 0 ? $uri : $uri . "-" . $count);
			
			$this->db->where('language', $this->language);
			$this->db->where('t_type', $this->tax_type);
			$this->db->where('t_uri', $new_uri);
			
			if ($this->db->count_all_results('taxonomy_term_data') > 0) {
				$found = true;
			} else {
				$found = false;
			}
			
			$count++;
		} while ($found === true);
		
		unset($found, $count);
		return $new_uri;
	}// noDupTaxonomyUri
	
	
	/**
	 * show taxterm info
	 * @param mixed $check_val
	 * @param string $check_field
	 * @param string $return_field
	 * @return string 
	 */
	public function showTaxTermInfo($check_val = '', $check_field = 'tid', $return_field = 't_name') 
	{
		$this->db->where('language', $this->language);
		$this->db->where('t_type', $this->tax_type);
		$this->db->where($check_field, $check_val);
		$query = $this->db->get('taxonomy_term_data');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			return $row->$return_field;
		}
		
		$query->free_result();
		
		return null;
	}// showTaxTermInfo
	
	
	/**
	 * show uri tree
	 * @param type $tid
	 * @return string 
	 */
	public function showTaxTermUriTree($tid = '') 
	{
		$end_depth = 'no';
		do {
			$this->db->where('tid', $tid);
			$this->db->where('language', $this->language);
			$this->db->where('t_type', $this->tax_type);
			$query = $this->db->get('taxonomy_term_data');
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$query->free_result();
				$output[] = $row->t_uri_encoded;
				$tid = $row->parent_id;
				if ($row->parent_id == '0') {
					$end_depth = 'yes';
				}
			} else {
				$query->free_result();
				$end_depth = 'yes';
			}
		} while ($end_depth == 'no');
		
		// reverse array
		$output = array_reverse($output);
		
		$uri = '';
		foreach ($output as $key => $item) {
			$uri .= $item;
			if (end($output) != $item) {
				$uri .= '/';
			}
		}
		
		// remove junk var
		unset($end_depth, $query, $row, $output);
		return $uri;
	}// showTaxTermUriTree
	
	
	/**
	 * update total post
	 * @param integer $tid
	 * @return boolean 
	 */
	public function updateTotalPost($tid = '') 
	{
		if (!is_numeric($tid)) {return false;}
		$this->db->where('tid', $tid);
		$total = $this->db->count_all_results('taxonomy_index');
		
		// update total posts in tax.term
		$this->db->set('t_total', $total);
		$this->db->where('tid', $tid);
		$this->db->update('taxonomy_term_data');
		
		return true;
	}// updateTotalPost
	
	
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
	public function _generateTreeData(&$arr, $id, $level) 
	{
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
	public function _getFields() 
	{
		return array($this->fields['id'], $this->fields['parent'], 't_type', 'nlevel');
	}
	
	
	/**
	 * Fetch the tree data, nesting within each node references to the node's children
	 *
	 * @return  array       The tree with the node's child data
	 */
	public function _getTreeWithChildren() 
	{
		$idField = $this->fields['id'];
		$parentField = $this->fields['parent'];

		$query = sprintf('select %s from %s', join(',', $this->_getFields()), $this->db->dbprefix('taxonomy_term_data'));
		$query .= ' where t_type = '.$this->db->escape($this->tax_type).'';
		$query .= ' and language = '.$this->db->escape($this->lang->get_current_lang()).'';

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
	public function reBuildTaxTerm() 
	{
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

			$query = sprintf('update %s set nlevel = %d where %s = %d', $this->db->dbprefix('taxonomy_term_data'), $row->nlevel, $this->fields['id'], $id);
			$this->db->query($query);
		}
	}// reBuildTaxTerm
	
	
}

