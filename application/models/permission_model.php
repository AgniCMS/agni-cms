<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
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

class permission_model extends CI_Model 
{
	
	
	private $app_admin;
	private $mx_path;
	
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->app_admin = APPPATH.'controllers/site-admin/';// always end with slash trail.
		$this->mx_path = MODULE_PATH;// always end with slash trail.
	}// __construct
	
	
	/**
	 * fetch permissions
	 * @return array 
	 */
	public function fetchPermissions() 
	{
		$permission_array = array();
		
		// fetch _define_permission from application controllers admin
		if (is_dir($this->app_admin)) {
			if ($dh = opendir($this->app_admin)) {
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..' && (filetype($this->app_admin.$file) == 'file')) {
						if ($file != 'account_permission'.EXT) {
							// prevent re-declare class
							include_once($this->app_admin.$file);
						}
						
						$file_to_class = str_replace(EXT, '', $file);
						$obj = new $file_to_class;
						
						if (method_exists($obj, '_define_permission')) {
							$permission_array = array_merge($permission_array, $obj->_define_permission());
						}
						unset($obj, $file_to_class);
					}
				}
			}
		}
		
		// fetch _define_permission from modules
		// ปรับแต่งใหม่จาก Web Start ให้โหลดค่า _define_permission จากโมดูลที่เปิดใช้งานเท่านั้น.
		/*if (is_dir($this->mx_path)) {
			$this->db->join('module_sites', 'module_sites.module_id = modules.module_id', 'inner');
			$this->db->where('module_sites.module_enable', '1');
			$this->db->order_by('module_system_name', 'asc');
			$query = $this->db->get('modules');
			
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					if (file_exists($this->mx_path.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php')) {
						include_once($this->mx_path.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php');
						$file_to_class = $row->module_system_name.'_admin';
						$obj = new $file_to_class;
						
						if (method_exists($obj, '_define_permission')) {
							$permission_array = array_merge($permission_array, $obj->_define_permission());
						}
						unset($obj, $file_to_clas);
					}
				}
			}
			$query->free_result();
		}*/ // change permission modules into each module permission page.
		
		return $permission_array;
	}// fetchPermissions
	
	
	/**
	 * fetch permissions module
	 * @param string $module_system_name
	 * @return array
	 */
	public function fetchPermissionsModule($module_system_name = '') 
	{
		if ($module_system_name == null) {return false;}
		
		$permission_array = array();
		
		// fetch _define_permission from module
		if (is_dir($this->mx_path)) {
			$this->db->join('module_sites', 'module_sites.module_id = modules.module_id', 'inner');
			$this->db->where('module_sites.module_enable', '1')
				   ->where('modules.module_system_name', $module_system_name);
			$query = $this->db->get('modules');
			
			if ($query->num_rows() > 0) {
				if (file_exists($this->mx_path.$module_system_name.'/controllers/'.$module_system_name.'_admin.php')) {
					include_once($this->mx_path.$module_system_name.'/controllers/'.$module_system_name.'_admin.php');
					
					$file_to_class = $module_system_name.'_admin';
					$obj = new $file_to_class;
					
					if (method_exists($obj, '_define_permission')) {
						$permission_array = array_merge($permission_array, $obj->_define_permission());
					}
					unset($obj, $file_to_class);
				}
			}
			
			$query->free_result();
			unset($query);
		}
		
		return $permission_array;
	}// fetchPermissionsModule
	
	
	/**
	 * has permission
	 * check if specific module has permission
	 * @param string $module_system_name
	 * @return boolean
	 */
	public function hasPermission($module_system_name = '') 
	{
		if ($module_system_name == null) {return false;}
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// check cached
		if (false === $has_permission = $this->cache->get('ismodhaspermission_'.$module_system_name)) {
			// not cached, check if module has permission.
			if (file_exists($this->mx_path.$module_system_name.'/controllers/'.$module_system_name.'_admin.php')) {
				include_once($this->mx_path.$module_system_name.'/controllers/'.$module_system_name.'_admin.php');

				$file_to_class = $module_system_name.'_admin';
				$obj = new $file_to_class;

				if (method_exists($obj, '_define_permission')) {
					$this->cache->save('ismodhaspermission_'.$module_system_name, 'true', 2678400);// 31 days
					
					unset($obj, $file_to_class, $module_system_name);
					
					return true;
				}
			}

			$this->cache->save('ismodhaspermission_'.$module_system_name, 'false', 2678400);// 31 days
			
			return false;
		}
		
		// if cached.
		if ($has_permission == 'true') {
			return true;
		} else {
			return false;
		}
	}// hasPermission
	
	
	/**
	 * list permissions check
	 * for check permission settings
	 * @return array 
	 */
	public function listPermissionsCheck($data = array()) 
	{
		$output = array();
		
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('account_level_permission');
		
		foreach ($query->result() as $row) {
			$output[$row->permission_id][$row->permission_page][$row->permission_action] = $row->level_group_id;
		}
		
		return $output;
	}// listPermissionsCheck
	
	
	/**
	 * reset permissions
	 * @return boolean 
	 */
	public function resetPermissions() 
	{
		$this->config_model->deleteCache('check_admin_permission_');
		
		// empty permissions db
		$this->db->truncate('account_level_permission');
		
		// system log
		$log['sl_type'] = 'permission';
		$log['sl_message'] = 'Reset permission';
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		return true;
	}// resetPermissions
	
	
	/**
	 * save permissions
	 * @param array $data
	 * @return boolean
	 */
	public function savePermissions($data = array()) 
	{
		foreach ($data['level_group_id'] as $key => $lv_groups) {
			foreach ($lv_groups as $level_group_id) {
				// check if permission is in db or not.
				$this->db->where('level_group_id', $level_group_id)
					   ->where('permission_page', $data['permission_page'][$key])
					   ->where('permission_action', $data['permission_action'][$key]);
				
				if ($this->db->count_all_results('account_level_permission') <= 0) {
					// not in db. insert it.
					$this->db->set('level_group_id', $level_group_id)
						   ->set('permission_page', $data['permission_page'][$key])
						   ->set('permission_action', $data['permission_action'][$key])
						   ->insert('account_level_permission');
				}
			}
		}
		
		// clear unused variables to free memory
		unset($key, $lv_groups, $level_group_id);
		
		// now remove permission in db that was not checked. ---------------------------------------------------------------
		foreach ($data['permission_action'] as $key => $permission_action) {
			if (isset($data['permission_page'][$key]) && isset($data['level_group_id'][$key])) {
				$this->db->where('permission_page', $data['permission_page'][$key])
					   ->where('permission_action', $permission_action);

				$query = $this->db->get('account_level_permission');

				foreach ($query->result() as $row) {
					if (!in_array($row->level_group_id, $data['level_group_id'][$key])) {
						$this->db->delete('account_level_permission', array('permission_id' => $row->permission_id));
					}
				}

				$query->free_result();
			}
		}
		
		// clear unused variables to free memory
		unset($key, $permission_action, $query, $row, $data);
		// now remove permission in db that was not checked. ---------------------------------------------------------------
		
		return true;
	}// savePermissions
	
	
}

