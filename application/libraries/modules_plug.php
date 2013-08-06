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

class modules_plug 
{
	
	
	public $ci;
	public $data;
	public $did_action_result;
	private $modules;// store module enable from db.
	
	
	public function __construct() 
	{
		$this->ci =& get_instance();
		// load modules plug and store in property array for reduce too many connection while calling each action
		if ($this->modules == null) {
			$this->load_modules();
		}
	}// __construct
	
	
	/**
	 * do_action
	 * @param string $action
	 * @param mixed $data
	 * @return mixed 
	 */
	public function do_action($action = '', $data = '', $arg = '') 
	{
		
		// pass arguments to module plug method.
		$args = array();
		
		if (is_array($arg) || is_object($arg)) {
			$args = $arg;
		} else {
			//$args[] = $arg;
			for ($i = 2; $i < func_num_args(); $i++) {
				$args[] = func_get_arg($i);
			}
		}
		
		// if arguments is empty, set to null
		if (empty($args)) {
			$args = null;
		}
		
		// loop load modules plug
		foreach ($this->modules as $key => $item) {
			include_once config_item('agni_plugins_path') . $item['module_system_name'] . '/' . $item['module_system_name'] . '_module.php';
			
			$module_plug = $item['module_system_name'] . '_module';
			
			if (class_exists($module_plug)) {
				$module_plug = new $module_plug;
				
				if (method_exists($module_plug, $action)) {
					// run module plug.
					$this->did_action_result[$action][$item['module_system_name'] . '_module'] = $module_plug->$action($data, $args);
				}
			}
		}
		
		unset($args);
		
		return $this->did_action_result;
	}// do_action
	
	
	/**
	 * do filter
	 * plugin type filter.
	 * sample usage: 
	 * $this->modules_plug->do_filter('filter_name', 'some_data', 'arg1', 'arg2', 'arg3');
	 * $this->modules_plug->do_filter('another_filter_name', 'some_data', array('arg1', 'arg2', 'arg3'));
	 * 
	 * @param string $filter
	 * @param mixed $data
	 * @param mixed $arg additional arguments
	 * @return mixed
	 */
	public function do_filter($filter = '', $data = '', $arg = '') 
	{
		// set $data to property
		$this->data = $data;
		
		// pass arguments to module plug method.
		$args = array();
		
		if (is_array($arg) || is_object($arg)) {
			$args = $arg;
		} else {
			//$args[] = $arg;
			for ($i = 2; $i < func_num_args(); $i++) {
				$args[] = func_get_arg($i);
			}
		}
		
		// if arguments is empty, set to null
		if (empty($args)) {
			$args = null;
		}
		
		// loop load modules plug
		foreach ($this->modules as $key => $item) {
			if (file_exists(config_item('agni_plugins_path') . $item['module_system_name'] . '/' . $item['module_system_name'] . '_module.php')) {
				include_once config_item('agni_plugins_path') . $item['module_system_name'] . '/' . $item['module_system_name'] . '_module.php';

				$module_plug = $item['module_system_name'] . '_module';

				if (class_exists($module_plug)) {
					$module_plug = new $module_plug;

					if (method_exists($module_plug, $filter)) {
						// run module plug.
						$this->data = $module_plug->$filter($this->data, $args);
					}
				}

				unset($module_plug);
			} else {
				// module is not exists anymore.
				// delete this module from db.
				$ci =& get_instance();
				$ci->load->model('modules_model');
				$ci->modules_model->deleteAModule($item['module_system_name']);
			}
		}
		
		unset($args);
		
		// done, return data
		return $this->data;
	}// do_filter
	
	
	/**
	 * has action
	 * @param string $filter
	 * @return int|boolean
	 */
	public function has_action($filter = '')
	{
		return $this->has_filter($filter);
	}
	
	
	/**
	 * has filter
	 * check if module plug has filter
	 * @param string $filter
	 * @return int|boolean
	 */
	public function has_filter($filter = '')
	{
		$i = 0;
		foreach ($this->modules as $key => $item) {
			include_once config_item('agni_plugins_path') . $item['module_system_name'] . '/' . $item['module_system_name'] . '_module.php';
			
			$module_plug = $item['module_system_name'] . '_module';
			
			if (class_exists($module_plug)) {
				$module_plug = new $module_plug;
				
				if (method_exists($module_plug, $filter)) {
					$i++;
				}
			}
			
			unset($module_plug);
		}
		
		if ($i > 0) {
			return $i;
		} else {
			return false;
		}
	}// has_filter
	
	
	/**
	 * load_modules
	 * @return boolean 
	 */
	public function load_modules() 
	{
		$this->ci->load->model('siteman_model');
		$site_id = $this->ci->siteman_model->getSiteId();
		
		$this->ci->db->join('module_sites', 'module_sites.module_id = modules.module_id', 'inner');
		$this->ci->db->where('module_sites.site_id', $site_id);
		$this->ci->db->where('module_sites.module_enable', '1');
		$query = $this->ci->db->get('modules');
		$output = array();
		
		foreach ($query->result() as $row) {
			$output[]['module_system_name'] = $row->module_system_name;
		}
		
		$query->free_result();
		$this->modules = $output;
		return true;
	}// load_modules
	
	
}

?>
