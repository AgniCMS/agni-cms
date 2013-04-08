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

class modules_plug {
	
	
	public $ci;
	public $data;
	private $modules;// store module enable from db.
	
	
	function __construct() {
		$this->ci =& get_instance();
		// load modules plug and store in property array for reduce too many connection while calling each action
		if ( $this->modules == null ) {
			$this->load_modules();
		}
	}// __construct
	
	
	/**
	 * do_action
	 * @param type $action
	 * @param type $data
	 * @return type 
	 */
	function do_action( $action = '', $data = '' ) {
		// set $data to property
		$this->ci->data = $data;
		
		foreach ( $this->modules as $key => $item ) {
			include_once( config_item( 'agni_plugins_path' ).$item['module_system_name'].'/'.$item['module_system_name'].'_module.php' );
			$module_plug = $item['module_system_name'].'_module';
				if ( class_exists( $module_plug ) ) {
				$module_plug = new $module_plug;
				if ( method_exists( $module_plug, $action ) ) {
					$this->ci->data = $module_plug->$action( $this->ci->data );
				}
			}
		}
		
		return $this->ci->data;
	}// do_action
	
	
	/**
	 * load_modules
	 * @return boolean 
	 */
	function load_modules() {
		$this->ci->load->model( 'siteman_model' );
		$site_id = $this->ci->siteman_model->get_site_id();
		
		$this->ci->db->join( 'module_sites', 'module_sites.module_id = modules.module_id', 'inner' );
		$this->ci->db->where( 'module_sites.site_id', $site_id );
		$this->ci->db->where( 'module_sites.module_enable', '1' );
		$query = $this->ci->db->get( 'modules' );
		$output = array();
		
		foreach ( $query->result() as $row ) {
			$output[]['module_system_name'] = $row->module_system_name;
		}
		
		$query->free_result();
		$this->modules = $output;
		return true;
	}// load_modules
	
	
}

?>
