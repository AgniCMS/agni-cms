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
 
class blog_model extends CI_Model 
{
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function add($data = array()) 
	{
		// get account_id
		$ca_account = $this->account_model->getAccountCookie('admin');
		$this->db->set('account_id', $ca_account['id']);
		$this->db->set('blog_title', $data['blog_title']);
		$this->db->set('blog_content', $data['blog_content']);
		$this->db->set('blog_date', time());
		$this->db->insert('blog');
		
		return true;
	}// add
	
	
	public function delete($blog_id = '') 
	{
		$this->db->where('blog_id', $blog_id);
		$this->db->delete('blog');
		
		return true;
	}// delete
	
	
	public function edit($data = array()) 
	{
		$this->db->set('blog_title', $data['blog_title']);
		$this->db->set('blog_content', $data['blog_content']);
		$this->db->where('blog_id', $data['blog_id']);
		$this->db->update('blog');
		
		return true;
	}// edit
	
	
	public function list_item($list_for = 'front') 
	{
		$sql = 'select * from '.$this->db->dbprefix('blog').' as b';
		$sql .= ' left join '.$this->db->dbprefix('accounts').' as a';
		$sql .= ' on b.account_id = a.account_id';
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'blog_id');
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'desc');
		$sql .= ' order by ' . $orders . ' ' . $sort;
		
		// query for count total
		$query = $this->db->query($sql);
		$total = $query->num_rows();
		$query->free_result();
		
		// pagination-----------------------------
		$this->load->library('pagination');
		if ($list_for == 'admin') {
			$config['base_url'] = site_url($this->uri->uri_string()).'?orders='.htmlspecialchars($orders).'&amp;sort='.htmlspecialchars($sort);
			$config['per_page'] = 20;
		} else {
			$config['base_url'] = site_url($this->uri->uri_string()).'?';
			$config['per_page'] = $this->config_model->loadSingle('content_items_perpage');
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
		$this->pagination->initialize($config);
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		
		$sql .= ' limit '.($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')).', '.$config['per_page'].';';
		
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// list_item
	
	
}

// EOF