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

class posts_model extends CI_Model {
	
	
	public $language;
	public $post_type;// article, page, ...
	
	
	function __construct() {
		parent::__construct();
		// set language
		$this->language = $this->lang->get_current_lang();
	}// __construct
	
	
	/**
	 * add post to database.
	 * @param array $data_posts
	 * @param array $data_post_revision
	 * @param array $data_tax_index
	 * @return mixed 
	 */
	function add( $data_posts = array(), $data_post_revision = array(), $data_tax_index = array() ) {
		if ( empty( $data_posts ) || !is_array( $data_posts ) || empty( $data_post_revision ) || !is_array( $data_post_revision ) ) {return false;}
		
		// there are 4 table for add data to article and 3 table for add data to  page
		// 1. posts
		// 2. post_revision
		// 3. url_alias
		// 4. taxonomy_index
		
		// set type and language to array for module plug
		$data_posts['post_type'] = $this->post_type;
		$data_posts['language'] = $this->language;
		
		// get account id from cookie
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$data_posts['account_id'] = $ca_account['id'];
		$data_post_revision['account_id'] = $ca_account['id'];
		
		// re-check post_uri
		$data_posts['post_uri'] = $this->nodup_uri( $data_posts['post_uri'] );
		$data_posts['post_uri_encoded'] = urlencode( $data_posts['post_uri'] );
		
		// insert into posts table #####
		if ( $data_posts['post_status'] == '1' ) {
			$this->db->set( 'post_publish_date', $data_posts['post_publish_date'] );
			$this->db->set( 'post_publish_date_gmt', $data_posts['post_publish_date_gmt'] );
		}
		$this->db->insert( 'posts', $data_posts );
		
		// get insert_id
		$data['post_id'] = $this->db->insert_id();
		$data_post_revision['post_id'] = $data['post_id'];
		
		// insert to post_revision table #####
		$this->db->insert( 'post_revision', $data_post_revision );
		
		// get revision id
		$data['revision_id'] = $this->db->insert_id();
		
		// now, update revision id into posts table #####
		$this->db->set( 'revision_id', $data['revision_id'] );
		$this->db->where( 'post_id', $data['post_id'] );
		$this->db->update( 'posts' );
		
		// add categories taxonimy term
		if ( isset( $data_tax_index['tid'] ) && is_array( $data_tax_index['tid'] ) ) {
			foreach ( $data_tax_index['tid'] as $tid ) {
				$this->db->set( 'post_id', $data['post_id'] );
				$this->db->set( 'tid', $tid );
				$this->db->set( 'position', $this->get_last_tax_position( $tid ) );
				$this->db->set( 'create', time() );
				$this->db->insert( 'taxonomy_index' );
				$this->taxonomy_model->update_total_post( $tid );
			}
		}
		
		// add tag taxonomy term
		if ( isset( $data_tax_index['tagid'] ) && is_array( $data_tax_index['tagid'] ) ) {
			foreach ( $data_tax_index['tagid'] as $tid ) {
				$this->db->set( 'post_id', $data['post_id'] );
				$this->db->set( 'tid', $tid );
				$this->db->set( 'create', time() );
				$this->db->insert( 'taxonomy_index' );
				$this->taxonomy_model->update_total_post( $tid );
			}
		}
		
		// insert to url alias table #####
		$this->db->set( 'c_type', $data_posts['post_type'] );
		$this->db->set( 'c_id', $data['post_id'] );
		$this->db->set( 'uri', $data_posts['post_uri'] );
		$this->db->set( 'uri_encoded', $data_posts['post_uri_encoded'] );
		$this->db->set( 'language', $data_posts['language'] );
		$this->db->insert( 'url_alias' );
		
		// any fields settings add here.
		$this->modules_plug->do_action( 'post_after_add', array( $data, $data_posts, $data_post_revision, $data_tax_index ) );
		if ( $data_posts['post_status'] == '1' ) {
			// publish plugin
			$this->modules_plug->do_action( 'post_published', array( $data, $data_posts, $data_post_revision, $data_tax_index ) );
		}
		
		// done.
		return true;
	}// add
	
	
	/**
	 * delete
	 * @param integer $post_id
	 * @return boolean 
	 */
	function delete( $post_id = '' ) {
		if ( !is_numeric( $post_id ) ) {return false;}
		
		// delete from menu items ------------------------------------------------------------------------------------
		// move child of this menu item to upper parent item
		$this->db->where( 'mi_type', $this->post_type );
		$this->db->where( 'type_id', $post_id );
		$this->db->where( 'language', $this->language );
		$query = $this->db->get( 'menu_items' );
		
		foreach ( $query->result() as $row ) {
			$this->db->set( 'parent_id', $row->parent_id );
			$this->db->where( 'parent_id', $row->mi_id );
			$this->db->update( 'menu_items' );
		}
		
		$query->free_result();
		
		// do delete
		$this->db->where( 'mi_type', $this->post_type );
		$this->db->where( 'type_id', $post_id );
		$this->db->where( 'language', $this->language );
		$this->db->delete( 'menu_items' );
		
		// rebuild menu items
		$this->load->model( 'menu_model' );
		$this->menu_model->rebuild();
		// end delete from menu items -------------------------------------------------------------------------------
		
		// delete from url alias
		$this->db->where( 'c_type', $this->post_type );
		$this->db->where( 'c_id', $post_id );
		$this->db->where( 'language', $this->language );
		$this->db->delete( 'url_alias' );
		
		// delete from comment
		$this->db->where( 'post_id', $post_id );
		$this->db->delete( 'comments' );
		
		// delete from taxonomy_index--------------------------------------------------------------------------------
		// update total posts in taxonomy term
		$this->db->where( 'post_id', $post_id );
		$query = $this->db->get( 'taxonomy_index' );
		$query_result = $query->result();
		// delete
		$this->db->where( 'post_id', $post_id );
		$this->db->delete( 'taxonomy_index' );
		
		// then update
		foreach ( $query_result as $row ) {
			$this->taxonomy_model->update_total_post( $row->tid );
		}
		// end delete from taxonomy_index---------------------------------------------------------------------------
		
		// delete from post_revision
		$this->db->where( 'post_id', $post_id );
		$this->db->delete( 'post_revision' );
		
		// delete from post_fields
		$this->db->where( 'post_id', $post_id );
		$this->db->delete( 'post_fields' );
		// delete from posts
		$this->db->where( 'post_id', $post_id );
		$this->db->delete( 'posts' );
		
		// for modules plug
		$this->modules_plug->do_action( 'post_after_delete', $post_id );
		
		return true;
	}// delete
	
	
	/**
	 * edit
	 * @param array $data_posts
	 * @param array $data_post_revision
	 * @param array $data_tax_index
	 * @param array $data
	 * @return mixed 
	 */
	function edit( $data_posts = array(), $data_post_revision = array(), $data_tax_index = array(), $data = array() ) {
		if ( empty( $data_posts ) || !is_array( $data_posts ) || empty( $data_post_revision ) || !is_array( $data_post_revision ) ) {return false;}
		
		// there are 4 table for add data to article and 3 table for add data to  page
		// 1. posts
		// 2. post_revision
		// 3. url_alias
		// 4. taxonomy_index
		
		// set type and language to array for module plug
		$data_posts['post_type'] = $this->post_type;
		$data_posts['language'] = $this->language;
		
		// load data for check things
		$row = $this->get_post_data( array( 'post_id' => $data_posts['post_id'] ) );
		if ( $row == null ) {return false;}
		
		// get account id from cookie
		$ca_account = $this->account_model->get_account_cookie( 'admin' );
		$data_post_revision['account_id'] = $ca_account['id'];
		
		// re-check post_uri
		$data_posts['post_uri'] = $this->nodup_uri( $data_posts['post_uri'], true, $data_posts['post_id'] );
		$data_posts['post_uri_encoded'] = urlencode( $data_posts['post_uri'] );
		
		// set data for post_revision table
		$data_post_revision['post_id'] = $data_posts['post_id'];
		
		// update posts table-------------------------------------------------------------
		if ( isset( $data_posts['post_status'] ) ) {
			// if this admin has not permission to publish/unpublish, the post_status is not set.
			$this->db->set( 'post_status', $data_posts['post_status'] );
		}
		if ( $row->post_publish_date == null && $row->post_publish_date_gmt == null && ( isset( $data['post_status'] ) && $data['post_status'] == '1' ) ) {
			$this->db->set( 'post_publish_date', $data_posts['post_publish_date'] );
			$this->db->set( 'post_publish_date_gmt', $data_posts['post_publish_date_gmt'] );
			
			// publish plugin
			$this->modules_plug->do_action( 'post_published', $data );
		}
		$this->db->where( 'post_id', $data_posts['post_id'] );
		$this->db->update( 'posts', $data_posts );
		
		// insert/update revision table---------------------------------------------------
		if ( $data['new_revision'] == '1' ) {
			// insert new revision#####
			$this->db->insert( 'post_revision', $data_post_revision );
			
			// get revision id
			$data['revision_id'] = $this->db->insert_id();
			
			// update revision id to posts table
			$this->db->set( 'revision_id', $data['revision_id'] );
			$this->db->where( 'post_id', $data_posts['post_id'] );
			$this->db->update( 'posts' );
		} else {
			// remove unwanted data
			unset( $data_post_revision['account_id'], $data_post_revision['revision_date'], $data_post_revision['revision_date_gmt'], $data_post_revision['log'] );
			
			// update current revision related to posts#####
			$this->db->where( 'revision_id', $row->revision_id );
			$this->db->where( 'post_id', $data_posts['post_id'] );
			$this->db->update( 'post_revision', $data_post_revision );
		}
		
		// update categories----------------------------------------------------------------
		if ( isset( $data_tax_index['tid'] ) && is_array( $data_tax_index['tid'] ) ) {
			foreach ( $data_tax_index['tid'] as $tid ) {
				$this->db->where( 'tid', $tid );
				$this->db->where( 'post_id', $data_posts['post_id'] );
				$query2 = $this->db->get( 'taxonomy_index' );
				if ( $query2->num_rows() > 0 ) {
					// exists, nothing to do
				} else {
					// not exists, insert taxonomy term
					$this->db->set( 'post_id', $data_posts['post_id'] );
					$this->db->set( 'tid', $tid );
					$this->db->set( 'position', $this->get_last_tax_position( $tid ) );
					$this->db->set( 'create', time() );
					$this->db->insert( 'taxonomy_index' );
					$this->taxonomy_model->update_total_post( $tid );
				}
				$query2->free_result();
			}
			
			// loop for delete uncheck taxonomy term
			$this->db->join( 'taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'left' );
			$this->db->where( 'post_id', $data_posts['post_id'] );
			$query2 = $this->db->get( 'taxonomy_index' );
			foreach ( $query2->result() as $row2 ) {
				if ( !in_array( $row2->tid, $data_tax_index['tid'] ) && $row2->t_type == 'category' ) {
					$this->db->delete( 'taxonomy_index', array( 'index_id' => $row2->index_id ) );
				}
			}
			$query2->free_result();
		} else {
			// no term select, delete all related to this post_id
			$this->db->join( 'taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'left' );
			$this->db->where( 't_type', 'category' )->where( 'post_id', $data_posts['post_id'] );
			$query2 = $this->db->get( 'taxonomy_index' );
			foreach ( $query2->result() as $row2 ) {
				$this->db->delete( 'taxonomy_index', array( 'tid' => $row2->tid, 'post_id' => $data_posts['post_id'] ) );
			}
			$query2->free_result();
		}
		
		// update tags-----------------------------------------------------------------------
		if ( isset( $data_tax_index['tagid'] ) && is_array( $data_tax_index['tagid'] ) ) {
			foreach ( $data_tax_index['tagid'] as $tid ) {
				$this->db->where( 'tid', $tid );
				$this->db->where( 'post_id', $data_posts['post_id'] );
				$query2 = $this->db->get( 'taxonomy_index' );
				
				if ( $query2->num_rows() > 0 ) {
					// exists, nothing to do
				} else {
					// not exists, insert taxonomy term
					$this->db->set( 'post_id', $data_posts['post_id'] );
					$this->db->set( 'tid', $tid );
					$this->db->set( 'create', time() );
					$this->db->insert( 'taxonomy_index' );
					$this->taxonomy_model->update_total_post( $tid );
				}
				
				$query2->free_result();
			}
			// loop for delete uncheck taxonomy term
			$this->db->join( 'taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'left' );
			$this->db->where( 'post_id', $data_posts['post_id'] );
			
			$query2 = $this->db->get( 'taxonomy_index' );
			
			foreach ( $query2->result() as $row2 ) {
				if ( !in_array( $row2->tid, $data_tax_index['tagid'] ) && $row2->t_type == 'tag' ) {
					$this->db->delete( 'taxonomy_index', array( 'index_id' => $row2->index_id ) );
				}
			}
			
			$query2->free_result();
		} else {
			// no term select, delete all related to this post_id
			$this->db->join( 'taxonomy_term_data', 'taxonomy_index.tid = taxonomy_term_data.tid', 'left' );
			$this->db->where( 't_type', 'tag' );
			$this->db->where( 'post_id', $data_posts['post_id'] );
			
			$query2 = $this->db->get( 'taxonomy_index' );
			
			foreach ( $query2->result() as $row2 ) {
				$this->db->delete( 'taxonomy_index', array( 'tid' => $row2->tid, 'post_id' => $data_posts['post_id'] ) );
			}
			
			$query2->free_result();
		}
		
		// any fields settings add here.
		
		// update to url alias
		$this->db->where( 'c_type', $this->post_type );
		$this->db->where( 'c_id', $data_posts['post_id'] );
		$this->db->set( 'uri', $data_posts['post_uri'] );
		$this->db->set( 'uri_encoded', $data_posts['post_uri_encoded'] );
		$this->db->where( 'language', $this->language );
		$this->db->update( 'url_alias' );
		
		// update menu_items
		$this->db->where( 'mi_type', $this->post_type );
		$this->db->where( 'type_id', $data_posts['post_id'] );
		$this->db->set( 'link_url', $data_posts['post_uri_encoded'] );
		$this->db->set( 'link_text', $data_posts['post_name'] );
		$this->db->update( 'menu_items' );
		
		// module plug
		$this->modules_plug->do_action( 'post_after_edit', $data );
		
		// done.
		return true;
	}// edit
	
	
	/**
	 * get_last_tax_position
	 * @param integer $tid
	 * @return integer 
	 */
	function get_last_tax_position( $tid = '' ) {
		if ( !is_numeric( $tid ) ) {return false;}
		
		$this->db->where( 'tid', $tid );
		$this->db->order_by( 'position', 'desc' );
		
		$query = $this->db->get( 'taxonomy_index' );
		
		if ( $query->num_rows() > 0 ) {
			$row = $query->row();
			$query->free_result();
			unset( $query );
			return ($row->position+1);
		}
		
		$query->free_result();
		
		unset( $query, $row );
		return '1';
	}// get_last_tax_position
	
	
	/**
	 * get post data
	 * @param array $data
	 * @return mixed
	 */
	function get_post_data( $data = array() ) {
		if ( !is_array( $data ) ) {return null;}
		
		$this->db->join( 'taxonomy_index', 'posts.post_id = taxonomy_index.post_id', 'left outer' );
		$this->db->join( 'post_fields', 'posts.post_id = post_fields.post_id', 'left outer' );
		$this->db->join( 'accounts', 'posts.account_id = accounts.account_id', 'left' );
		$this->db->join( 'post_revision', 'posts.revision_id = post_revision.revision_id', 'inner' );
		$this->db->where( 'post_type', $this->posts_model->post_type );
		$this->db->where( 'language', $this->posts_model->language );
		if ( isset( $data['post_id'] ) ) {
			$this->db->where( 'posts.post_id', $data['post_id'] );
		}
		if ( isset( $data['post_status'] ) ) {
			$this->db->where( 'posts.post_status', $data['post_status'] );
		}
		$this->db->group_by( 'posts.post_id' );
		
		$query = $this->db->get( 'posts' );
		
		if ( $query->num_rows() > 0 ) {
			return $query->row();
		}
		
		// there is no selected post
		return null;
	}// get_post_data
	
	
	/**
	 * is_allow_delete_post
	 * check permission if user allowed to delete post.
	 * @param object $row
	 * @return boolean 
	 */
	function is_allow_delete_post( $row = '' ) {
		if ( !is_object( $row ) || $row == null || !isset( $row->post_type ) ) {return false;}
		
		// get my account id
		$cm_account = $this->account_model->get_account_cookie( 'member' );
		$my_account_id = ( isset( $cm_account['id'] ) ? $cm_account['id'] : 0 );
		
		if ( $row->post_type == 'article' ) {
			if ( ( $this->account_model->check_admin_permission( 'post_article_perm', 'post_article_delete_own_perm' ) && $row->account_id == $my_account_id ) || ( $this->account_model->check_admin_permission( 'post_article_perm', 'post_article_delete_other_perm' ) && $row->account_id != $my_account_id ) ) {
				return true;
			}
			return false;
		} elseif ( $row->post_type == 'page' ) {
			 if ( ( $this->account_model->check_admin_permission( 'post_page_perm', 'post_page_delete_own_perm' ) && $row->account_id == $my_account_id ) || ( $this->account_model->check_admin_permission( 'post_page_perm', 'post_page_delete_other_perm' ) && $row->account_id != $my_account_id ) ) {
				return true;
			}
			return false;
		} else {
			// check other types
			$result = $this->modules_plug->do_action( 'post_is_allow_delete', $row );
			if ( is_bool( $result ) ) {
				return $result;
			}
			return false;
		}
	}// is_allow_delete_post
	
	
	/**
	 * is_allow_edit_post
	 * check permission if user allowed to edit post.
	 * @param object $row
	 * @return boolean 
	 */
	function is_allow_edit_post( $row = '' ) {
		if ( !is_object( $row ) || $row == null || !isset( $row->post_type ) ) {return false;}
		
		// get my account id
		$cm_account = $this->account_model->get_account_cookie( 'member' );
		$my_account_id = ( isset( $cm_account['id'] ) ? $cm_account['id'] : 0 );
		
		if ( $row->post_type == 'article' ) {
			if ( ( $this->account_model->check_admin_permission( 'post_article_perm', 'post_article_edit_own_perm' ) && $row->account_id == $my_account_id ) || ( $this->account_model->check_admin_permission( 'post_article_perm', 'post_article_edit_other_perm' ) && $row->account_id != $my_account_id ) ) {
				return true;
			}
			return false;
		} elseif ( $row->post_type == 'page' ) {
			if ( ( $this->account_model->check_admin_permission( 'post_page_perm', 'post_page_edit_own_perm' ) && $row->account_id == $my_account_id ) || ( $this->account_model->check_admin_permission( 'post_page_perm', 'post_page_edit_other_perm' ) && $row->account_id != $my_account_id ) ) {
				return true;
			}
			return false;
		} else {
			// check other types
			$result = $this->modules_plug->do_action( 'post_is_allow_edit', $row );
			if ( is_bool( $result ) ) {
				return $result;
			}
			return false;
		}
	}// is_allow_edit_post
	
	
	/**
	 * list item
	 * @param admin|front $list_for
	 * @return mixed 
	 */
	function list_item( $list_for = 'front' ) {
		$this->db->join( 'taxonomy_index', 'taxonomy_index.post_id = posts.post_id', 'left outer' );
		$this->db->join( 'accounts', 'accounts.account_id = posts.account_id', 'left' );
		$this->db->join( 'post_revision', 'post_revision.post_id = posts.post_id', 'inner' );
		$this->db->where( 'post_type', $this->post_type );
		$this->db->where( 'language', $this->language );
		if ( $list_for == 'front' ) {
			$this->db->where( 'post_status', '1' );
		}
		$tid = trim( $this->input->get( 'tid' ) );
		if ( $tid != null && is_numeric( $tid ) ) {
			$this->db->where( 'taxonomy_index.tid', $tid );
		}
		$q = trim( $this->input->get( 'q' ) );
		if ( $q != null && $q != 'none' ) {
			$like_data[0]['field'] = 'posts.post_name';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'posts.post_uri';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'post_revision.body_value';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'post_revision.body_summary';
			$like_data[3]['match'] = $q;
			$like_data[4]['field'] = 'post_revision.log';
			$like_data[4]['match'] = $q;
			$like_data[5]['field'] = 'posts.meta_title';
			$like_data[5]['match'] = $q;
			$like_data[6]['field'] = 'posts.meta_description';
			$like_data[6]['match'] = $q;
			$like_data[7]['field'] = 'posts.meta_keywords';
			$like_data[7]['match'] = $q;
			$like_data[8]['field'] = 'posts.theme_system_name';
			$like_data[8]['match'] = $q;
			$this->db->like_group( $like_data );
			unset( $like_data );
		}
		$this->db->group_by( 'posts.post_id' );
		
		// order and sort
		$orders = strip_tags( trim( $this->input->get( 'orders' ) ) );
		$orders = ( $orders != null ? $orders : 'position' );
		$sort = strip_tags( trim( $this->input->get( 'sort' ) ) );
		$sort = ( $sort != null ? $sort : 'desc' );
		if ( $tid == null && $this->input->get( 'orders' ) == null ) {
			$this->db->order_by( 'post_update', 'desc' );
		} else {
			$this->db->order_by( $orders, $sort );
			$this->db->order_by( 'post_update', 'desc' );
		}
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get( 'posts' );
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset( $this_db );
		
		// html encode for links.
		$q = urlencode( htmlspecialchars( $q ) );
		
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		if ( $list_for == 'admin' ) {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?orders='.htmlspecialchars( $orders ).'&amp;sort='.htmlspecialchars( $sort ).( $q != null ?'&amp;q='.$q : '' ).( $tid != null ? '&amp;tid='.$tid : '' );
			$config['per_page'] = 20;
		} else {
			$config['base_url'] = site_url( $this->uri->uri_string() ).'?'.( $q != null ?'q='.$q : '' );
			$config['per_page'] = $this->config_model->load_single( 'content_items_perpage' );
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
		$this->pagination->initialize( $config );
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		
		// limit query
		$this->db->limit( $config['per_page'], ( $this->input->get( 'per_page' ) == null ? '0' : $this->input->get( 'per_page' ) ) );
		
		$query = $this->db->get( 'posts' );
		
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
	 * list revisions.
	 * @param array $data
	 * @return mixed
	 */
	function list_revision( $data = array() ) {
		$this->db->join( 'accounts', 'post_revision.account_id = accounts.account_id', 'left' );
		if ( isset( $data['post_id'] ) ) {
			$this->db->where( 'post_id', $data['post_id'] );
		}
		$this->db->order_by( 'revision_date', 'desc' );
		
		$query = $this->db->get( 'post_revision' );
		
		// output
		$output['total'] = $query->num_rows();
		$output['items'] = $query->result();
		
		return $output;
	}// list_revision
	
	
	/**
	 * modify_content
	 * @param string $content
	 * @return string 
	 */
	function modify_content( $content = '', $post_type = '' ) {
		if ( $content == null ) {return;}
		
		// modify content by core here.
		
		// modify content by plugin
		$content = $this->modules_plug->do_action( 'post_modifybody_value', $content, $post_type );
		
		// done
		return $content;
	}// modify_content
	
	
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
		
		// check if edit mode?
		if ( $editmode == true ) {
			if ( !is_numeric( $id ) ) {return null;}
			// no duplicate uri edit mode
			$this->db->where( 'language', $this->language );
			$this->db->where( 'post_type', $this->post_type );
			$this->db->where( 'post_uri', $uri );
			$this->db->where( 'post_id', $id );
			if ( $this->db->count_all_results( 'posts' ) > 0 ) {
				// nothing change, return old value
				return $uri;
			}
		}
		
		// loop check for duplicate uri
		$found = true;
		$count = 0;
		$uri = ( $uri == null ? 'p' : $uri );
		do {
			$new_uri = ($count === 0 ? $uri : $uri . "-" . $count);
			$this->db->where( 'language', $this->language );
			$this->db->where( 'post_type', $this->post_type );
			$this->db->where( 'post_uri', $new_uri );
			if ( $this->db->count_all_results( 'posts' ) > 0 ) {
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
	 * update_total_comment
	 * @param integer $post_id
	 * @return boolean 
	 */
	function update_total_comment( $post_id = '' ) {
		if ( !is_numeric( $post_id ) ) {return false;}
		
		$this->db->where( 'post_id', $post_id );
		$total_comment = $this->db->count_all_results( 'comments' );
		
		$this->db->where( 'post_id', $post_id );
		$this->db->set( 'comment_count', $total_comment );
		$this->db->update( 'posts' );
		
		unset( $total_comment );
		return true;
	}// update_total_comment
	
	
}

