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

class updater extends admin_controller 
{


	public function __construct() 
	{
		parent::__construct();
		
		// load model
		$this->load->model(array('queue_model'));
		
		// load helper
		$this->load->helper(array('form'));
		
		// load library
		$this->load->library(array('agni_update', 'session'));
		
		// load language
		$this->lang->load('updater');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('updater_perm' => array('updater_update_core_perm'/*, 'updater_update_modules_perm'*/));
	}// _define_permission
	
	
	private function _no_core_update() 
	{
		$this->load->library('session');
		$this->session->unset_userdata('global_status');
		// show no update available.
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('updater_updater'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/updater/updater_no_core_update_view', $output);
	}// _no_core_update
	
	
	public function index() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('updater_perm', 'updater_update_core_perm') != true) {redirect('site-admin');}
		
		// check if this site enable auto update.
		$cfg = $this->config_model->load(array('angi_auto_update'));
		if (!isset($cfg['angi_auto_update']['value']) || (isset($cfg['angi_auto_update']['value']) && $cfg['angi_auto_update']['value'] == '0')) {redirect('site-admin');}
		unset($cfg);
		
		// load library
		$this->load->library(array('encrypt'));
		
		// get queue data of agni core update.
		$queue = $this->queue_model->getQueueData(array('queue_name' => $this->agni_update->agni_update_core_name));
		
		// if no queue.
		if ($queue == null) {
			// no queue. show no update.
			return $this->_no_core_update();
		}
		
		$queue_data = unserialize($queue->queue_data);
		
		// if no update available.
		if (!isset($queue_data['update_available']) || (isset($queue_data['update_available']) && $queue_data['update_available'] == '0')) {
			return $this->_no_core_update();
		}
		
		// get current agni cms version and all required configuration values
		$cfg = $this->config_model->load(array('agni_version', 'ftp_host', 'ftp_username', 'ftp_password', 'ftp_port', 'ftp_passive', 'ftp_basepath'));
		$output['current_version'] = (isset($cfg['agni_version']['value']) ? $cfg['agni_version']['value'] : null);
		$output['ftp_host'] = (isset($cfg['ftp_host']['value']) ? $cfg['ftp_host']['value'] : null);
		$output['ftp_username'] = (isset($cfg['ftp_username']['value']) ? $cfg['ftp_username']['value'] : null);
		$output['ftp_password'] = (isset($cfg['ftp_password']['value']) ? $this->encrypt->decode($cfg['ftp_password']['value']) : null);
		$output['ftp_port'] = (isset($cfg['ftp_port']['value']) ? $cfg['ftp_port']['value'] : null);
		$output['ftp_passive'] = (isset($cfg['ftp_passive']['value']) ? $cfg['ftp_passive']['value'] : null);
		$output['ftp_basepath'] = (isset($cfg['ftp_basepath']['value']) ? $cfg['ftp_basepath']['value'] : null);
		
		// send queue data to views.
		$output['queue'] = $queue;
		$output['queue_data'] = $queue_data;
		
		// form submitted
		if (strtolower($this->input->server('REQUEST_METHOD')) == 'post') {
			$update_filesys = trim($this->input->post('update_filesys'));
			$output['update_filesys'] = $update_filesys;
			
			if ($update_filesys == 'filesys') {
				// admin choose to rewrite file by filesystem
				$this->session->set_userdata('rewrite_method', 'filesys');
				
				redirect('site-admin/updater/step2');
			} elseif ($update_filesys == 'ftp') {
				// admin choose to rewrite file by ftp
				// test if ftp config is valid.
				$this->load->library('ftp');
				
				// get data from form.
				$data['ftp_host'] = trim($this->input->post('ftp_host'));
				$data['ftp_username'] = trim($this->input->post('ftp_username'));
				$data['ftp_password'] = trim($this->input->post('ftp_password'));
				$data['ftp_port'] = trim($this->input->post('ftp_port'));
				$data['ftp_passive'] = ($this->input->post('ftp_passive') == 'true' ? true : false);
				$data['ftp_basepath'] = trim($this->input->post('ftp_basepath'));
				
				// set config for ftp connect
				$config['hostname'] = $data['ftp_host'];
				$config['username'] = $data['ftp_username'];
				$config['password'] = $data['ftp_password'];
				$config['port'] = $data['ftp_port'];
				$config['passive'] = $data['ftp_passive'];
				
				// test connect ftp
				$result = $this->ftp->connect($config);
				
				if ($result === false) {
					$this->ftp->close();
					$output['form_status'] = 'error';
					$output['form_status_message'] = $this->lang->line('updater_incorrect_ftp_configuration');
				} else {
					$result = $this->ftp->list_files($data['ftp_basepath']);
					$this->ftp->close();
					
					// if base path is not correct
					if (!is_array($result) || (is_array($result) && (!in_array($data['ftp_basepath'].'/application', $result) && !in_array($data['ftp_basepath'].'application', $result) && !in_array('application', $result) && !in_array('/application', $result)))) {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $this->lang->line('updater_incorrect_ftp_basepath');
					} else {
						// all setting are correct
						$this->session->set_userdata('rewrite_method', 'ftp');
						
						$this->session->set_userdata('ftp_config', $data);
						
						unset($config, $data, $result);
						
						redirect('site-admin/updater/step2');
					}
				}
				
				// re-populate form ftp
				$output = array_merge($output, $data);
				
				unset($config, $data, $result);
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('updater_updater'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/updater/updater_view', $output);
	}// index
	
	
	public function step2() 
	{
		// check permission
		if ($this->account_model->checkAdminPermission('updater_perm', 'updater_update_core_perm') != true) {redirect('site-admin');}
		
		// check if this site enable auto update.
		$cfg = $this->config_model->load(array('angi_auto_update'));
		if (!isset($cfg['angi_auto_update']['value']) || (isset($cfg['angi_auto_update']['value']) && $cfg['angi_auto_update']['value'] == '0')) {redirect('site-admin');}
		unset($cfg);
		
		// check rewrite method (file system or ftp)
		$rewrite_method = $this->session->userdata('rewrite_method');
		if ($rewrite_method == null) {redirect('site-admin/updater');}
		
		// get queue data of agni core update. -------------------------------------------------------------------------------------
		$queue = $this->queue_model->getQueueData(array('queue_name' => $this->agni_update->agni_update_core_name));
		
		// if no queue.
		if ($queue == null) {
			// no queue. show no update.
			return $this->_no_core_update();
		}
		
		$queue_data = unserialize($queue->queue_data);
		
		// if no update available.
		if (!isset($queue_data['update_available']) || (isset($queue_data['update_available']) && $queue_data['update_available'] == '0')) {
			return $this->_no_core_update();
		}
		
		// remove un use variables
		unset($queue, $queue_data);
		// get queue data of agni core update. -------------------------------------------------------------------------------------
		
		// update process ------------------------------------------------------------------------------------------------------------------
		// start to download update file.
		$result = $this->agni_update->update_core_download();
		
		// if update step 2 complete (download update).
		if (isset($result['result']) && $result['result'] !== false) {
			$data['downloaded_file'] = $result['downloaded_file'];
			unset($result);
			
			// download completed, start extract files.
			$result = $this->agni_update->update_core_extract($data['downloaded_file']);
			
			// if extract complete.----------------------------------
			if (isset($result['result']) && $result['result'] !== false) {
				$data['unzip_path'] = $result['unzip_path'];
				unset($result);
				
				// extract file completed (unzip completed), start copy files.
				$result = $this->agni_update->update_core_copy_files($data);
				
				// if copy files complete.----------------------------------
				if ($result === true) {
					// copy new files to replace old files completed, start update db.
					$result = $this->agni_update->update_core_database();
					
					// if update sql complete.----------------------------------
					if ($result === true) {
						// update db completed
						// update version in config
						$this->agni_update->update_core_version();
						
						// delete queue
						$this->queue_model->deleteQueue(array('queue_name' => $this->agni_update->agni_update_core_name));
						
						// delete global_status and all other sessions.
						$this->session->unset_userdata('global_status');
						$this->session->unset_userdata('rewrite_method');
						$this->session->unset_userdata('ftp_config');
						
						// remove cron cached
						$site_id = $this->siteman_model->getSiteId();
						$this->cache->delete('site_id_'.$site_id.'_agnicms_checked_queue_update_core_cron_controller');
						$this->cache->delete('site_id_'.$site_id.'_agnicms_had_run_cron_controller');
						
						// success, go to step3
						redirect('site-admin/updater/step3');
					} else {
						$output['form_status'] = 'error';
						$output['form_status_message'] = $this->lang->line('updater_unable_to_update_database');
					}
				} else {
					$output['form_status'] = 'error';
					$output['form_status_message'] = $this->lang->line('updater_unable_to_copy_update_files');
				}
			} else {
				$output['form_status'] = 'error';
				$output['form_status_message'] = $this->lang->line('updater_unable_to_unzip_file');
			}
		} else {
			$output['form_status'] = 'error';
			$output['form_status_message'] = $this->lang->line('updater_unable_to_download_update_file');
		}
		// update process ------------------------------------------------------------------------------------------------------------------
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('updater_updater'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/updater/updater_step2_view', $output);
	}// step2
	
	
	public function step3() 
	{
		$cfg = $this->config_model->load(array('agni_version'));
		
		$output['agni_version'] = (isset($cfg['agni_version']['value']) ? $cfg['agni_version']['value'] : '');
		
		// head tags output ##############################
		$output['page_title'] = $this->html_model->gen_title($this->lang->line('updater_updater'));
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('site-admin/templates/updater/updater_step3_view', $output);
	}// step3


}
