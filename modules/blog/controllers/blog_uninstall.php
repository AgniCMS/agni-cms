<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 * _uninstall is fixed suffix name of module for use in auto uninstall
 * this auto uninstall will run silently.
 * 
 */

class blog_uninstall extends admin_controller {
	
	
	public $module_system_name = 'blog';

	
	function __construct() {
		parent::__construct();
	}
	
	
	function index() {
		// uninstall module table
		if ( $this->db->table_exists( 'blog' ) ) {
			$sql = 'DROP TABLE `'.$this->db->dbprefix('blog').'`;';
			$this->db->query( $sql );
		}
		
		// disable module is the last step and required.
		$this->load->model( 'modules_model' );
		$this->modules_model->do_deactivate( $this->module_system_name, $this->input->get( 'site_id' ) );
		
		// done
		$this->load->library( 'session' );
		$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.$this->lang->line( 'blog_uninstall_completed' ).'</div>' );
		redirect( 'site-admin/module' );
	}
	

}

