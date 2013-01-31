<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 * model นี้ไม่ได้มีไว้เพื่อเช็คการอนุญาตเมื่อมีการเรียกให้ตรวจตามหน้าต่างๆ แต่มีไว้เพื่อตั้งค่าการอนุญาตของทั้งระบบ.
 *
 */

class permission_model extends CI_Model {
	
	
	private $app_admin;
	private $mx_path;
	
	
	function __construct() {
		parent::__construct();
		
		$this->app_admin = APPPATH.'controllers/site-admin/';// always end with slash trail.
		$this->mx_path = MODULE_PATH;// always end with slash trail.
	}// __construct
	
	
	/**
	 * fetch_permissions
	 * @return array 
	 */
	function fetch_permissions() {
		$permission_array = array();
		
		// fetch _define_permission from application controllers admin
		if ( is_dir( $this->app_admin) ) {
			if ( $dh = opendir( $this->app_admin) ) {
				while ( ( $file = readdir( $dh) ) !== false ) {
					if ( $file != '.' && $file != '..' && ( filetype( $this->app_admin.$file ) == 'file' ) ) {
						if ( $file != 'account_permission'.EXT ) {
							// prevent re-declare class
							include_once( $this->app_admin.$file );
						}
						
						$file_to_class = str_replace(EXT, '', $file );
						$obj = new $file_to_class;
						
						if ( method_exists( $obj, '_define_permission' ) ) {
							$permission_array = array_merge( $permission_array, $obj->_define_permission() );
						}
						unset( $obj, $file_to_class );
					}
				}
			}
		}
		
		// fetch _define_permission from modules
		// ปรับแต่งใหม่จาก Web Start ให้โหลดค่า _define_permission จากโมดูลที่เปิดใช้งานเท่านั้น.
		if ( is_dir( $this->mx_path) ) {
			$this->db->where( 'module_enable', '1' );
			$this->db->order_by( 'module_system_name', 'asc' );
			$query = $this->db->get( 'modules' );
			
			if ( $query->num_rows() > 0 ) {
				foreach ( $query->result() as $row ) {
					if ( file_exists( $this->mx_path.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php' ) ) {
						include_once( $this->mx_path.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php' );
						$file_to_class = $row->module_system_name.'_admin';
						$obj = new $file_to_class;
						
						if ( method_exists( $obj, '_define_permission' ) ) {
							$permission_array = array_merge( $permission_array, $obj->_define_permission() );
						}
						unset( $obj, $file_to_clas );
					}
				}
			}
			$query->free_result();
		}
		
		return $permission_array;
	}// fetch_permissions
	
	
	/**
	 * list_permissions_check
	 * for check permission settings
	 * @return array 
	 */
	function list_permissions_check() {
		$output = array();
		$query = $this->db->get( 'account_level_permission' );
		
		foreach ( $query->result() as $row ) {
			$output[$row->permission_id][$row->permission_page][$row->permission_action] = $row->level_group_id;
		}
		
		return $output;
	}// list_permissions_check
	
	
	/**
	 * reset_permissions
	 * @return boolean 
	 */
	function reset_permissions() {
		$this->config_model->delete_cache( 'check_admin_permission_' );
		
		// empty permissions db
		$this->db->truncate( 'account_level_permission' );
		
		return true;
	}// reset_permission
	
	
}

