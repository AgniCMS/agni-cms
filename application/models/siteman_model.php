<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

class siteman_model extends CI_Model 
{
	
	
	// this is core tables that require to copy when create new site.
	public $core_tables = array(
						'account_level',// this table require data.
						'account_level_group', // this table require base level data.
						'account_level_permission',

						'blocks',

						'comments',
						'comment_fields',

						'config', // this table require base config data

						'frontpage_category',

						'menu_groups',
						'menu_items',

						'posts',
						'post_fields',
						'post_revision',

						'taxonomy_fields',
						'taxonomy_index',
						'taxonomy_term_data',

						'url_alias'
					);
	// site wide tables is tables that do not copy when new site created.
	public $site_wide_tables = array(
						'accounts',
						'account_fields',
						'account_logins',
						'account_sites',

						'ci_sessions',
	    
						'files',

						'modules',
						'module_sites',
	    
						'queue',

						'sites',
	    
						'syslog',

						'themes',
						'theme_sites'
					);


	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * add new site
	 * @param array $data
	 * @return mixed
	 */
	public function addSite($data = array()) 
	{
		// additional data for inserting
		$data['site_create'] = time();
		$data['site_create_gmt'] = local_to_gmt(time());
		$data['site_update'] = time();
		$data['site_update_gmt'] = local_to_gmt(time());
		
		// insert into db.
		$this->db->insert('sites', $data);
		
		// get site_id
		$site_id = $this->db->insert_id();
		
		// start copy tables
		$this->copyNewSiteTable($site_id);
		
		// add new theme to new site -------------------------------------------------------------------------------------------------
		$this->load->model('themes_model');
		
		$default_theme = $this->themes_model->getDefaultTheme();
		$default_theme_admin = $this->themes_model->getDefaultTheme('admin');
		
		$this->themes_model->setDefaultTheme($default_theme, 'front', $site_id);
		$this->themes_model->setDefaultTheme($default_theme_admin, 'admin', $site_id);
		
		unset($default_theme, $default_theme_admin);
		// add new theme to new site -------------------------------------------------------------------------------------------------
		
		// set config for new site.
		$config_site['config_value'] = $data['site_name'];
		$this->db->where('config_name', 'site_name');
		$this->db->update($this->db->dbprefix($site_id.'_config'), $config_site);
		unset($config_site);
		
		// system log
		$log['sl_type'] = 'multisite';
		$log['sl_message'] = 'Add new site';
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		return true;
	}// addSite
	
	
	/**
	 * copy new site table
	 * copy tables for new website
	 * @param integer $site_id
	 * @return boolean
	 */
	public function copyNewSiteTable($site_id = '') 
	{
		foreach ($this->core_tables as $table) {
			if ($table == 'account_level' || $table == 'account_level_group' || $table == 'config') {
				// this table needs to copy data
				$sql = 'CREATE TABLE IF NOT EXISTS '.$this->db->dbprefix($site_id.'_'.$table).' SELECT * FROM '.$this->db->dbprefix($table);
			} else {
				$sql = 'CREATE TABLE IF NOT EXISTS '.$this->db->dbprefix($site_id.'_'.$table).' LIKE '.$this->db->dbprefix($table);
			}
			$this->db->query($sql);
		}
		
		// change all accounts level to member (except admin and guest).
		$this->db->where('account_id != 0');
		$this->db->where('account_id != 1');
		$this->db->set('level_group_id', '3');
		$this->db->update($this->db->dbprefix($site_id.'_account_level'));
		
		// done
		return true;
	}// copyNewSiteTable
	
	
	/**
	 * delete site
	 * @param integer $site_id
	 * @return boolean
	 */
	public function deleteSite($site_id = '') 
	{
		// do not allow admin/user delete first site.
		if ($site_id == '1') {
			return false;
		}
		
		// delete related _sites table ----------------------------------------------------------------------------------------------------
		// delete from account_sites table
		$this->db->where('site_id', $site_id)->delete('account_sites');
		
		// delete from module_sites table
		$this->db->where('site_id', $site_id)->delete('module_sites');
		
		// delete from syslog table
		$this->db->where('site_id', $site_id)->delete('syslog');
		
		// delete from theme_sites table
		$this->db->where('site_id', $site_id)->delete('theme_sites');
		// delete related _sites table ----------------------------------------------------------------------------------------------------
		
		// drop siteNumber_ tables ------------------------------------------------------------------------------------------------------
		$this->load->dbforge();
		
		// drop site tables
		foreach ($this->core_tables as $table) {
			$this->dbforge->drop_table($site_id.'_'.$table);
		}
		// drop siteNumber_ tables ------------------------------------------------------------------------------------------------------
		
		// delete site from db
		$this->db->delete('sites', array('site_id' => $site_id));
		
		// system log
		$log['sl_type'] = 'multisite';
		$log['sl_message'] = 'Delete site';
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		// done 
		return true;
	}// deleteSite
	
	
	/**
	 * edit site
	 * @param array $data
	 * @return mixed
	 */
	public function editSite($data = array()) 
	{
		// additional data for updating
		$data['site_update'] = time();
		$data['site_update_gmt'] = local_to_gmt(time());
		
		// filter data before update
		if ($data['site_id'] == '1') {
			// site 1 always enabled.
			$data['site_status'] = '1';
		}
		
		// update to db
		$this->db->where('site_id', $data['site_id']);
		$this->db->update('sites', $data);
		
		// set config for new site.
		$config_site['config_value'] = $data['site_name'];
		if ($data['site_id'] == '1') {
			$config_table = 'config';
		} else {
			$config_table = $data['site_id'].'_config';
		}
		$this->db->where('config_name', 'site_name');
		$this->db->update($this->db->dbprefix($config_table), $config_site);
		unset($config_site);
		
		// system log
		$log['sl_type'] = 'multisite';
		$log['sl_message'] = 'Update site';
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		// done
		return true;
	}// editSite
	
	
	/**
	 * get site data from db.
	 * @param array $data
	 * @return mixed
	 */
	public function getSiteDataDb($data = array()) 
	{
		if (!empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('sites');
		
		if ($query->num_rows() > 0) {
			return $query->row();
		}
		
		$query->free_result();
		return null;
	}// getSiteDataDb
	
	
	/**
	 * get site id
	 * @param boolean $enabled_only
	 * @return integer
	 */
	public function getSiteId($enabled_only = true) 
	{
		$site_domain = $this->input->server('HTTP_HOST');
		
		// get site info from db
		$data['site_domain'] = $site_domain;
		if ($enabled_only === true) {
			$data['site_status'] = '1';
		}
		$site = $this->getSiteDataDb($data);
		unset($data);
		
		if ($site != null) {
			return $site->site_id;
		}
		
		return '1';
	}// getSiteId
	
	
	/**
	 * alias of method getSiteId.
	 */
	public function get_site_id($enabled_only = true) 
	{
		return $this->getSiteId($enabled_only);
	}// get_site_id
	
	
	/**
	 * list websites
	 * @param array $data
	 * @param array $datacond data condition.
	 * @return mixed
	 */
	public function listWebsites($data = array(), $datacond = array()) 
	{
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		$q = trim($this->input->get('q'));
		if ($q != null && $q != 'none') {
			$like_data[0]['field'] = 'sites.site_id';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'sites.site_name';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'sites.site_domain';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'sites.site_status';
			$like_data[3]['match'] = $q;
			$this->db->like_group($like_data);
			unset($like_data);
		}
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'site_id');
		$orders = (!in_array($orders, array('site_id', 'site_name', 'site_domain', 'site_status')) ? 'site_id' : $orders);
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'asc');
		$this->db->order_by($orders, $sort);
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('sites');
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
		if (isset($datacond['per_page']) && is_numeric($datacond['per_page'])) {
			$config['per_page'] = $datacond['per_page'];
		} else {
			$config['per_page'] = (isset($datacond['list_for']) && $datacond['list_for'] == 'admin' ? 20 : $this->config_model->loadSingle('content_items_perpage'));
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
		
		$query = $this->db->get('sites');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listWebsites
	
	
	/**
	 * list websites all
	 * @param array $data
	 * @return mixed
	 */
	public function listWebsitesAll($data = array()) 
	{
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'site_name');
		$orders = (!in_array($orders, array('site_id', 'site_name', 'site_domain', 'site_status')) ? 'site_name' : $orders);
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'asc');
		$this->db->order_by($orders, $sort);
		
		$query = $this->db->get('sites');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $query->num_rows();
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listWebsitesAll


}