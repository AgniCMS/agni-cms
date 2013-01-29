<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class admin_controller extends MY_Controller {

	
	function __construct() {
		parent::__construct();
		
		// check admin login
		if ( ! $this->account_model->is_admin_login() ) {redirect( 'site-admin/login?rdr='.urlencode( current_url() ) );}
		
		// load model
		$this->load->model( array( 'modules_model' ) );
		
		// load helper
		$this->load->helper( array( 'language' ) );
		
		// load language
		$this->lang->load( 'admin' );
		
		// get default admin theme name and set new theme_path
		$theme_system_name = $this->themes_model->get_default_theme( 'admin' );
		$this->theme_path = $this->base_url.config_item( 'agni_theme_path' ).$theme_system_name.'/';
		$this->theme_system_name = $theme_system_name;
		unset( $theme_system_name );
	}// __construct
	
	
	/**
	 * generate admin page template+content
	 * สร้างเพจสำหรับหน้า admin โดยรับไฟล์สำหรับหน้า admin นั้นๆเข้ามาแล้ว generate ออกไปพร้อม template.
	 * ต้องวางไว้ตรงนี้ เพราะเอาไปไว้ใน model ไม่ได้. ถ้าไว้ใน model views จะเรียก $this->property ใน MY_Controller ไม่ได้
	 * @param string $page
	 * @param string $output 
	 */
	function generate_page( $page = '', $output = '' ) {
		//
		$output['page_content'] = $this->load->view( $page, $output, true );
		$output['cookie'] = $this->account_model->get_account_cookie( 'admin' );
		$this->load->view( 'site-admin/template', $output );
	}// generate_page
	

}

