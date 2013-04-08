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
 
class akismet_install extends admin_controller {
	
	
	public $module_system_name = 'akismet';
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function index() {
		// install config name
		$this->db->where( 'config_name', 'akismet_api' );
		$query = $this->db->get( 'config' );
		if ( $query->num_rows() <= 0 ) {
			$this->db->set( 'config_name', 'akismet_api' );
			$this->db->set( 'config_value', null );
			$this->db->set( 'config_description', 'Store akismet api key' );
			$this->db->insert( 'config' );
		}
		$query->free_result();
		
		// done
		$this->load->library( 'session' );
		$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.$this->lang->line( 'akismet_install_completed' ).'</div>' );
		// go back
		redirect( 'site-admin/module' );
	}// index
	
	
}

// EOF