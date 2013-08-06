<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 * @since AgniCMS 0.8 Beta
 * call to http://yourdomain.tld/installed_dir/cron in cron job, cron tab.
 * 
 */

class cron extends MY_Controller 
{


	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function check_queue_update_core() 
	{
		// to prevent user call to cron too often, use cache to check.
		// set site_id
		$site_id = $this->siteman_model->getSiteId();
		
		// check last run.
		$this->load->driver('cache', array('adapter' => 'file'));
		if (false === $this->cache->get('site_id_'.$site_id.'_agnicms_checked_queue_update_core_cron_controller')) {
			$this->cache->save('site_id_'.$site_id.'_agnicms_checked_queue_update_core_cron_controller', 'true', 7200);// 86400 seconds is 1 day
			
			$this->load->library('agni_update');
			$this->load->model('queue_model');
			
			// check queue of update core
			$queue = $this->queue_model->getQueueData(array('queue_name' => $this->agni_update->agni_update_core_name));
			
			if ($queue != null) {
				$queue_data = unserialize($queue->queue_data);
				
				if (isset($queue_data['update_version']) && isset($queue_data['update_available']) && $queue_data['update_available'] == true) {
					$this->load->library('session');
					$this->lang->load('updater');
					
					// queue is available, set global status msg to update
					$this->session->set_userdata('global_status', array(
												'msg' => sprintf(lang('updater_agnicms_version_is_available_please_update_now'), $queue_data['update_version'], site_url('site-admin/updater')), 
												'status' => 'warning' 
											));
				}
				
				unset($queue, $queue_data);
			}
		}
		
		unset($cfg, $site_id);
		
		return true;
	}// check_queue_update_core
	
	
	public function index() 
	{
		// to prevent user call to cron too often, use cache to check.
		// set site_id
		$site_id = $this->siteman_model->getSiteId();
		
		// check last run.
		$this->load->driver('cache', array('adapter' => 'file'));
		if (false === $this->cache->get('site_id_'.$site_id.'_agnicms_had_run_cron_controller')) {
			$this->cache->save('site_id_'.$site_id.'_agnicms_had_run_cron_controller', 'true', 7200);// 86400 seconds is 1 day
			
			// system log
			$log['sl_type'] = 'cron';
			$log['sl_message'] = 'Run cron';
			$this->load->model('syslog_model');
			$this->syslog_model->addNewLog($log);
			unset($log);
			
			// get config
			$cfg = $this->config_model->load(array('angi_auto_update'));
			
			// if this site enabled auto update.
			if ($cfg['angi_auto_update']['value'] == '1') {
				// load agni update library.
				$this->load->library('agni_update');

				// any cron job action call from here. ---------------------------------------------------------------------

				// check update agni cms core.
				$this->agni_update->check_update_core();

			} // endif; angi_auto_update == 1
		}
		
		unset($cfg, $site_id);
		
		return true;
	}// index


}
