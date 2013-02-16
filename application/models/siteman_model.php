<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class siteman_model extends CI_Model {
	
	
	// this is core tables that require to copy when create new site.
	public $core_tables = array(
						'account_level',// this table require data.
						'account_level_group', // this table require base level data.
						'account_level_permission',

						'blocks',

						'comments',

						'config', // this table require base config data

						'frontpage_category',

						'menu_groups',
						'menu_items',

						'posts',
						'post_fields',
						'post_revision',

						'taxonomy_index',
						'taxonomy_term_data',

						'url_alias'
					);
	// site wide tables is tables that do not copy when new site created.
	public $site_wide_tables = array(
						'accounts',
						'account_logins',
						'ci_sessions',
						'files',
						'modules',
						'module_sites',
						'sites',
						'themes',
						'theme_sites'
					);


	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * add new site
	 * @param array $data
	 * @return mixed
	 */
	function add_site( $data = array() ) {
		// additional data for inserting
		$data['site_create'] = time();
		$data['site_create_gmt'] = local_to_gmt( time() );
		$data['site_update'] = time();
		$data['site_update_gmt'] = local_to_gmt( time() );
		
		// insert into db.
		$this->db->insert( 'sites', $data );
		
		// get site_id
		$site_id = $this->db->insert_id();
		
		// start copy tables
		$this->copy_newsite_table( $site_id );
		
		// set config for new site.
		$config_site['config_value'] = $data['site_name'];
		$this->db->where( 'config_name', 'site_name' );
		$this->db->update( $this->db->dbprefix( $site_id.'_config' ), $config_site );
		unset( $config_site );
		
		return true;
	}// add_site
	
	
	/**
	 * copy_newsite_table
	 * copy tables for new website
	 * @param integer $site_id
	 * @return boolean
	 */
	function copy_newsite_table( $site_id = '' ) {
		foreach ( $this->core_tables as $table ) {
			if ( $table == 'account_level' || $table == 'account_level_group' || $table == 'config' ) {
				// this table needs to copy data
				$sql = 'CREATE TABLE IF NOT EXISTS '.$this->db->dbprefix( $site_id.'_'.$table ).' SELECT * FROM '.$this->db->dbprefix( $table );
			} else {
				$sql = 'CREATE TABLE IF NOT EXISTS '.$this->db->dbprefix( $site_id.'_'.$table ).' LIKE '.$this->db->dbprefix( $table );
			}
			$this->db->query( $sql );
		}
		
		// change all accounts level to member (except admin and guest).
		$this->db->where( 'account_id != 0' );
		$this->db->where( 'account_id != 1' );
		$this->db->set( 'level_group_id', '3' );
		$this->db->update( $this->db->dbprefix( $site_id.'_account_level' ) );
		
		// done
		return true;
	}// copy_newsite_table
	
	
	function delete_site( $site_id = '' ) {
		// do not allow admin/user delete first site.
		if ( $site_id == '1' ) {
			return false;
		}
		
		$this->load->dbforge();
		
		// drop site tables
		foreach ( $this->core_tables as $table ) {
			$this->dbforge->drop_table( $site_id.'_'.$table );
		}
		
		// delete site from db
		$this->db->delete( 'sites', array( 'site_id' => $site_id ) );
		
		// done 
		return true;
	}// delete_site
	
	
	/**
	 * edit site
	 * @param array $data
	 * @return mixed
	 */
	function edit_site( $data = array() ) {
		// additional data for inserting
		$data['site_update'] = time();
		$data['site_update_gmt'] = local_to_gmt( time() );
		
		// update to db
		$this->db->where( 'site_id', $data['site_id'] );
		$this->db->update( 'sites', $data );
		
		// set config for new site.
		$config_site['config_value'] = $data['site_name'];
		$this->db->where( 'config_name', 'site_name' );
		$this->db->update( $this->db->dbprefix( $data['site_id'].'_config' ), $config_site );
		unset( $config_site );
		
		// done
		return true;
	}// edit_site
	
	
	/**
	 * get site data from db.
	 * @param array $data
	 * @return mixed
	 */
	function get_site_data_db( $data = array() ) {
		if ( !empty( $data ) ) {
			$this->db->where( $data );
		}
		
		$query = $this->db->get( 'sites' );
		
		if ( $query->num_rows() > 0 ) {
			return $query->row();
		}
		
		$query->free_result();
		return null;
	}// get_site_data_db
	
	
	/**
	 * list websites
	 * @param array $data
	 * @return mixed
	 */
	function list_websites( $data = array() ) {
		$q = trim( $this->input->get( 'q' ) );
		if ( $q != null && $q != 'none' ) {
			$like_data[0]['field'] = 'sites.site_id';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'sites.site_name';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'sites.site_domain';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'sites.site_status';
			$like_data[3]['match'] = $q;
			$this->db->like_group( $like_data );
			unset( $like_data );
		}
		
		// order and sort
		$orders = strip_tags( trim( $this->input->get( 'orders' ) ) );
		$orders = ( $orders != null ? $orders : 'site_id' );
		$sort = strip_tags( trim( $this->input->get( 'sort' ) ) );
		$sort = ( $sort != null ? $sort : 'asc' );
		$this->db->order_by( $orders, $sort );
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get( 'sites' );
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset( $this_db );
		
		// html encode for links.
		$q = urlencode( htmlspecialchars( $q ) );
		
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).( $q != null ?'&amp;q='.$q : '' );
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
		$this->pagination->initialize( $config );
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		
		// limit query
		$this->db->limit( $config['per_page'], ( $this->input->get( 'per_page' ) == null ? '0' : $this->input->get( 'per_page' ) ) );
		
		$query = $this->db->get( 'sites' );
		
		if ( $query->num_rows() > 0 ) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// list_websites


}