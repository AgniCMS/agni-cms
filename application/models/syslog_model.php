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

class syslog_model extends CI_Model 
{


	public function __construct() 
	{
		parent::__construct();
		
		// purge old log
		$this->purgeOldLog();
	}// __construct
	
	
	/**
	 * add new log
	 * @param array $data
	 * @return boolean
	 */
	public function addNewLog($data = array()) 
	{
		// if not set account_id, get it from cookie.
		if (!isset($data['account_id'])) {
			$ca_account = $this->account_model->getAccountCookie('admin');
			if (!isset($ca_account['id'])) {
				$cm_account = $this->account_mode->getAccountCookie('member');
				if (!isset($cm_account['id'])) {
					$data['account_id'] = '0';
				} else {
					$data['account_id'] = $cm_account['id'];
				}
			} else {
				$data['account_id'] = $ca_account['id'];
			}
			unset($ca_account, $cm_account);
		}
		
		// if not set site_id
		if (!isset($data['site_id'])) {
			$this->load->model('siteman_model');
			$site_id = $this->siteman_model->getSiteId();
			
			$data['site_id'] = $site_id;
			unset($site_id);
		}
		
		// if not set sl_variables, collect all data and set
		if (!isset($data['sl_variables'])) {
			$variables['server_variables'] = $_SERVER;
			$variables['method_get_data'] = $_GET;
			$variables['method_post_data'] = $_POST;
			$variables['upload_file_data'] = $_FILES;
			$variables['user_agent'] = $this->input->user_agent();
			
			$data['sl_variables'] = serialize($variables);
			unset($variables);
		}
		
		// if not set url, set it
		if (!isset($data['sl_url'])) {
			$data['sl_url'] = current_url().($this->input->server('QUERY_STRING')  != null ? '?'.$this->input->server('QUERY_STRING') : '');
		}
		
		// if not set referer
		if (!isset($data['sl_referer'])) {
			$this->load->library('user_agent');
			$data['sl_referer'] = $this->agent->referrer();
		}
		
		// if not set ip address
		if (!isset($data['sl_ipaddress'])) {
			$data['sl_ipaddress'] = $this->input->ip_address();
		}
		
		// if not set date time
		if (!isset($data['sl_datetime'])) {
			$data['sl_datetime'] = time();
		}
		
		// insert into db
		$this->db->insert('syslog', $data);
		
		return true;
	}// addNewLog
	
	
	/**
	 * purge old log
	 * @param integer $day_old
	 * @return boolean
	 */
	public function purgeOldLog($day_old = 90) 
	{
		if ($day_old < 90 || !is_numeric($day_old)) {
			$day_old = 90;
		}
		
		$sql = 'DELETE FROM '.$this->db->dbprefix('syslog').' WHERE sl_datetime < unix_timestamp(now() - interval '.$day_old.' day)';
		$this->db->query($sql);
		unset($sql);
		
		// system log
		$log['sl_type'] = 'syslog';
		$log['sl_message'] = 'Purge old log';
		$this->addNewLog($log);
		unset($log);
		
		return true;
	}// purgeOldLog


}
