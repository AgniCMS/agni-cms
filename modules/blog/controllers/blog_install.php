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

class blog_install extends admin_controller 
{
	
	
	public $module_system_name = 'blog';

	
	public function __construct() 
	{
		parent::__construct();
	}
	
	
	public function index() 
	{
		// install module table.
		if (!$this->db->table_exists('blog')) {
			$sql = 'CREATE TABLE `'.$this->db->dbprefix('blog').'` (
			`blog_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			`account_id` INT(11) NOT NULL ,
			`blog_title` VARCHAR(255) NULL DEFAULT NULL ,
			`blog_content` TEXT NULL DEFAULT NULL ,
			`blog_date` BIGINT NULL DEFAULT NULL
			) ENGINE = InnoDB;';
			$this->db->query($sql);
		}
		
		// done
		$this->load->library('session');
		$this->session->set_flashdata(
			'form_status',
			array(
				'form_status' => 'success',
				'form_status_message' => $this->lang->line('blog_install_completed')
			)
		);
		
		// go back
		redirect('site-admin/module');
	}

	
}

