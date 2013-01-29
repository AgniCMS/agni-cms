<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 * _install is fixed suffix name of module for use in auto install.
 *
 */

class blog_install extends admin_controller {
	
	
	public $module_system_name = 'blog';

	
	function __construct() {
		parent::__construct();
	}
	
	
	function index() {
		$this->db->where( 'module_system_name', $this->module_system_name );
		$query = $this->db->get( 'modules' );
		if ( $query->num_rows() <= 0 ) {
			$query->free_result();
			echo 'Installed.';
			return null;
		}
		
		// install module table.
		if ( !$this->db->table_exists( 'blog' ) ) {
			$sql = 'CREATE TABLE `'.$this->db->dbprefix('blog').'` (
			`blog_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`account_id` INT( 11 ) NOT NULL ,
			`blog_title` VARCHAR( 255 ) NULL DEFAULT NULL ,
			`blog_content` TEXT NULL DEFAULT NULL ,
			`blog_date` BIGINT NULL DEFAULT NULL
			) ENGINE = InnoDB;';
			$this->db->query( $sql );
		}
		
		// install module to system
		$this->db->set( 'module_install', '1' );
		$this->db->where( 'module_system_name', $this->module_system_name );
		$this->db->update( 'modules' );
		
		// done
		$this->load->library( 'session' );
		$this->session->set_flashdata( 'form_status', '<div class="txt_success alert alert-success">'.$this->lang->line( 'blog_install_completed' ).'</div>' );
		// go back
		redirect( 'site-admin/module' );
	}

	
}

