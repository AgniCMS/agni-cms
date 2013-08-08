<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 * ------------------------------------------------------------------
 * this model works with url. eg: check allowed and disallowed uri in taxterm or posts, manage url redirect.
 */
 
class url_model extends CI_Model 
{
	
	
	public $c_type;
	public $language;
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// set language
		$this->language = $this->lang->get_current_lang();
	}// __construct
	
	
	/**
	 * add url redirect
	 * @param array $data
	 * @return mixed 
	 */
	public function addUrlRedirect($data = array()) 
	{
		if (!is_array($data)) {return false;}
		
		// re-check uri
		$data['uri'] = $this->noDupUrlUri($data['uri']);
		
		// additional data for insert
		$data['c_type'] = $this->c_type;
		$data['uri_encoded'] = urlencode_except_slash($data['uri']);
		$data['redirect_to_encoded'] = $this->encodeRedirectTo($data['redirect_to']);
		$data['language'] = $this->language;
		
		// insert
		$this->db->insert('url_alias', $data);
		
		// get insert id and set result
		$output['id'] = $this->db->insert_id();
		$output['result'] = true;
		return $output;
	}// addUrlRedirect
	
	
	/**
	 * delete url redirect
	 * @param integer $alias_id 
	 * @return boolean
	 */
	public function deleteUrlRedirect($alias_id = '') 
	{
		$this->db->where('c_type', $this->c_type);
		$this->db->where('alias_id', $alias_id);
		$this->db->delete('url_alias');
		
		return true;
	}// deleteUrlRedirect
	
	
	/**
	 * edit url redirect
	 * @param array $data
	 * @return mixed 
	 */
	public function editUrlRedirect($data = array()) 
	{
		if (!is_array($data)) {return false;}
		
		// re-check uri
		$data['uri'] = $this->noDupUrlUri($data['uri'], true, $data['alias_id']);
		
		// additional data for update
		$data['uri_encoded'] = urlencode_except_slash($data['uri']);
		$data['redirect_to_encoded'] = $this->encodeRedirectTo($data['redirect_to']);
		
		// insert
		$this->db->where('c_type', $this->c_type);
		$this->db->where('language', $this->language);
		$this->db->where('alias_id', $data['alias_id']);
		$this->db->update('url_alias', $data);
		
		$output['result'] = true;
		return $output;
	}// editUrlRedirect
	
	
	/**
	 * encode url redirect to
	 * @param string $redirect_to
	 * @return string 
	 */
	public function encodeRedirectTo($redirect_to = '') 
	{
		if ($redirect_to == null) {return null;}
		
		return urlencode_except_slash($redirect_to);
	}// encodeRedirectTo
	
	
	/**
	 * get url alias data from db
	 * @param array $data
	 * @return mixed
	 */
	public function getUrlAliasDataDb($data = array()) 
	{
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('url_alias');
		
		return $query->row();
	}// getUrlAliasDataDb
	
	
	/**
	 * list url item
	 * @param array $data
	 * @return mixed
	 */
	public function listUrlItem($data = array()) 
	{
		$this->db->where('c_type', $this->c_type);
		$this->db->where('language', $this->language);
		$q = trim($this->input->get('q'));
		if ($q != null) {
			$like_data[0]['field'] = 'url_alias.uri';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'url_alias.uri_encoded';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'url_alias.redirect_to';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'url_alias.redirect_to_encoded';
			$like_data[3]['match'] = $q;
			$like_data[4]['field'] = 'url_alias.redirect_code';
			$like_data[4]['match'] = $q;
			$this->db->like_group($like_data);
			unset($like_data);
		}
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'uri');
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'asc');
		$this->db->order_by($orders, $sort);
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('url_alias');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		// html encode for links.
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
		
		$query = $this->db->get('url_alias');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listUrlItem
	
	
	/**
	 * nodup uri
	 * @param string $uri
	 * @param boolean $editmode
	 * @param integer $id
	 * @return string 
	 */
	public function noDupUrlUri($uri, $editmode = false, $id = '') 
	{
		$uri = $this->validateAllowUrl($uri);
		
		// prevent url_title cut slash out (/)------------------------------------------------
		$uri_raw = explode('/', $uri);
		
		if (!is_array($uri_raw)) {return null;}
		
		foreach ($uri_raw as $uri) {
			$uri = url_title($uri);
			$output[] = $uri;
		}
		unset($uri_raw);
		
		// got array. merge it to string
		if (isset($output) && is_array($output)) {
			$return = '';
			
			foreach ($output as $a_output) {
				$return .= $a_output;
				if ($a_output != end($output)) {
					$return .= '/';
				}
			}
			
			$uri = $return;
			unset($return, $output, $a_output);
		}
		// end prevent url_title cut slash out (/)------------------------------------------------
		
		// start checking
		if ($editmode == true) {
			if (!is_numeric($id)) {return null;}
			
			// no duplicate uri edit mode
			$this->db->where('language', $this->language);
			$this->db->where('c_type', $this->c_type);
			$this->db->where('uri', $uri);
			$this->db->where('alias_id', $id);
			
			if ($this->db->count_all_results('url_alias') > 0) {
				// nothing change, return old value
				return $uri;
			}
		}
		// loop check
		$found = true;
		$count = 0;
		$uri = ($uri == null ? 'rdr' : $uri);
		
		do {
			$new_uri = ($count === 0 ? $uri : $uri . "-" . $count);
			$this->db->where('language', $this->language);
			$this->db->where('c_type', $this->c_type);
			$this->db->where('uri', $new_uri);
			
			if ($this->db->count_all_results('url_alias') > 0) {
				$found = true;
			} else {
				$found = false;
			}
			
			$count++;
		} while ($found === true);
		
		unset($found, $count);
		return $new_uri;
	}// noDupUrlUri
	
	
	/**
	 * validate allow url
	 * @param string $uri
	 * @return string 
	 */
	public function validateAllowUrl($uri = '') 
	{
		if ($uri == null) {return null;}
		
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
		    
			'cron',
			
			'index',
			
			'post',
			'post/preview',
			'post/revision',
			'post/view',
			
			'search',
			
			'tag',
		    
			'update',
			
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
		if (in_array($uri, $disallowed_url)) {
			return 'disallowed-uri';
		}
		
		// not found in disallowed uri
		return $uri;
	}// validateAllowUrl
	
	
}

// EOF