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
		if (! $this->account_model->isAdminLogin()) {
			redirect('site-admin/login?rdr='.urlencode(current_url()));
		}
		
		// load model
		$this->load->model(array('modules_model', 'siteman_model'));
		
		// load helper
		$this->load->helper(array('language'));
		
		// load language
		$this->lang->load('admin');
		
		// call cron controller. --------------------------------------------------------------------------------------------------------------
		$cfg = $this->config_model->load(array('agni_system_cron', 'angi_auto_update'));
		
		// if use agni system cron instead of real cron job.
		if (isset($cfg['agni_system_cron']['value']) && $cfg['agni_system_cron']['value'] === '1') {
			// call cron controller.
			$this->load->module('cron');
			$this->cron->index();
			
			// check update queue for admin to click update.
			if ($this->account_model->checkAdminPermission('updater_perm', 'updater_update_core_perm') === true 
			&& (isset($cfg['angi_auto_update']['value']) && $cfg['angi_auto_update']['value'] == '1') 
			) {
				$this->cron->check_queue_update_core();
			}
		}
		unset($cfg);
		// call cron controller. --------------------------------------------------------------------------------------------------------------
		
		// get default admin theme name and set new theme_path
		$theme_system_name = $this->themes_model->getDefaultTheme('admin');
		$this->theme_path = $this->base_url.config_item('agni_theme_path').$theme_system_name.'/';
		$this->theme_system_name = $theme_system_name;
		unset($theme_system_name);
		
		// module plug working at admin start point
		$this->modules_plug->do_action('admin_core_controller_constructor', $this);
	}// __construct
	
	
	/**
	 * generate admin page template+content
	 * สร้างเพจสำหรับหน้า admin โดยรับไฟล์สำหรับหน้า admin นั้นๆเข้ามาแล้ว generate ออกไปพร้อม template.
	 * ต้องวางไว้ตรงนี้ เพราะเอาไปไว้ใน model ไม่ได้. ถ้าไว้ใน model views จะเรียก $this->property ใน MY_Controller ไม่ได้
	 * @param string $page
	 * @param string $output 
	 */
	function generate_page($page = '', $output = '') {
		// get sites to list in admin page 
		$sdata['site_status'] = '1';
		$list_sites = $this->siteman_model->listWebsitesAll($sdata);
		if (isset($list_sites['total']) && $list_sites['total'] > 1) {
			$output['agni_list_sites'] = $list_sites;
		}
		unset($sdata, $list_sites);
		
		// show global alert message. ---------------------------------------------------------------------------------------------------
		$this->load->library('session');
		// to use global status, use the code sample as below this line.
		// $this->session->set_userdata('global_status', array('msg' => 'this is status message.', 'status' => 'error'));// sample global status.
		// the 'msg' is message (text or html). 'status' is warning, info, error, success (choose one).
		//
		// to remove global status, use the code below
		// $this->session->unset_userdata('global_status');
		//
		// show global status, alert
		$output['global_status'] = $this->session->userdata('global_status');
		// end show global alert message. --------------------------------------------------------------------------------------------
		
		//
		$output['page_content'] = $this->load->view($page, $output, true);
		$output['cookie'] = $this->account_model->getAccountCookie('admin');
		$this->load->view('site-admin/template', $output);
	}// generate_page
	

}

