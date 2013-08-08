<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class account_model extends CI_Model 
{

	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * add account
	 * @param array $data
	 * @return mixed 
	 */
	public function addAccount($data = array()) 
	{
		if (!is_array($data) || empty($data)) {return false;}
		
		// change some variable in $data to other variable to prevent insert/update failed in accounts table
		// accounts table use $data, account_level may use $data2
		$data2['level_group_id'] = $data['level_group_id'];
		unset($data['level_group_id']);
		
		if (!$this->canIAddEditAccount($data2['level_group_id'])) {
			return $this->lang->line('account_cannot_add_account_higher_your_level');
		}
		
		// check duplicate account
		$this->db->where('account_username', $data['account_username']);
		$query = $this->db->select('account_username')->get('accounts');
		if ($query->num_rows() > 0) {
			$query->free_result();
			return $this->lang->line('account_username_already_exists');
		}
		$query->free_result();
		$this->db->where('account_email', $data['account_email']);
		$query = $this->db->select('account_email')->get('accounts');
		if ($query->num_rows() > 0) {
			$query->free_result();
			return $this->lang->line('account_email_already_exists');
		}
		$query->free_result();
		
		// load date helper for gmt
		$this->load->helper('date');
		
		// set value for insert in accounts table
		$data['account_password'] = $this->encryptPassword($data['account_password']);
		$data['account_create'] = date('Y-m-d H:i:s', time());
		$data['account_create_gmt'] = date('Y-m-d H:i:s', local_to_gmt(time()));// แปลงเป็น gmt0 แล้ว เมื่อจะเอามาใช้ก็ gmt_to_local(strtotime('account_create_gmt'), 'account_timezone');
		
		// add to db
		$this->db->insert('accounts', $data);
		
		// get added account id
		$data2['account_id'] = $this->db->insert_id();
		
		// add level
		$this->db->insert('account_level', $data2);
		
		// module plug here.
		$data['level_group_id'] = $data2['level_group_id'];// set for use in plugins
		$this->modules_plug->do_action('account_add', $data);
		
		return true;
	}// addAccount
	
	
	/**
	 * add level group
	 * @param array $data
	 * @return boolean 
	 */
	public function addLevelGroup($data = array()) 
	{
		if (!is_array($data)) {return false;}
		
		// get latest priority
		$this->db->where('level_priority !=', '999');
		$this->db->where('level_priority !=', '1000');
		$this->db->order_by('level_priority', 'desc');
		
		$query = $this->db->get('account_level_group');
		
		$row = $query->row();
		
		$data['level_priority'] = ($row->level_priority+1);
		
		$query->free_result();
		
		// add to db
		$this->db->insert('account_level_group', $data);
		
		return true;
	}// addLevelGroup
	
	
	/**
	 * add multisite login session
	 * @param array $data
	 */
	public function addLoginSession($data = array()) 
	{
		// load model
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();

		$query = $this->db->where('account_id', $data['account_id'])->where('site_id', $site_id)->get('account_sites');
		
		if ($query->num_rows() <= 0) {
			// use insert
			$this->db->set('account_id', $data['account_id'])
				   ->set('site_id', $site_id)
				   ->set('account_last_login', time())
				   ->set('account_last_login_gmt', local_to_gmt(time()));
			
			if (isset($data['session_id'])) {
				$this->db->set('account_online_code', $data['session_id']);
			}
			
			$this->db->insert('account_sites');
		} else {
			// use update
			$this->db->set('account_last_login', time())
				   ->set('account_last_login_gmt', local_to_gmt(time()));
			
			if (isset($data['session_id'])) {
				$this->db->set('account_online_code', $data['session_id']);
			}
			
			$this->db->where('account_id', $data['account_id'])
				   ->where('site_id', $site_id);
			$this->db->update('account_sites');
		}
		
		$query->free_result();
		unset($query);
	}// addLoginSession
	
	
	/**
	 * admin login
	 * @param array $data
	 * @return mixed 
	 */
	public function adminLogIn($data = array()) 
	{
		if (!is_array($data) || empty($data)) {return false;}
		
		$this->db->where('account_username', $data['username']);
		
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			if ($row->account_status == '1') {
				if ($this->checkPassword($data['password'], $row->account_password) === true) {
					if ($this->checkAdminPermission('account_admin_login', 'account_admin_login', $row->account_id) == true) {
						$this->load->library('session');
						$session_id = $this->session->userdata('session_id');

						// update session มีไว้เพื่อกำหนดว่าจะอัปเดท session หรือไม่. กรณีมีการ login จากหน้าสมาชิกด้านหน้าเว็บแล้ว ก็จะไม่ต้องอัปเดทอีก, แต่ถ้ายัง ก็ต้องอัปเดท session เพื่อให้มีรหัส session ในการตรวจสอบ login ซ้อน
						$need_update_session = false;// กำหนดเป็น false ไปก่อน เพื่อให้มีการอัปเดท session (สมมุติว่ายังไม่เคยมีการ login จากหน้าแรกเลย.

						// เอาคุกกี้สำหรับหน้าแรกมา เพื่อหาว่าเคยมีการ login แล้วรึยัง
						$cm_account = $this->getAccountCookie('member');
						if (isset($cm_account['id']) && isset($cm_account['username']) && isset($cm_account['password']) && isset($cm_account['onlinecode']) && $cm_account['id'] == $row->account_id) {
							// เคยมีการ login จากหน้าแรกแล้ว
							// ดึงค่า session, onlinecode จากที่เคย login หน้าแรกมาใช้.
							$set_ca_account['onlinecode'] = $cm_account['onlinecode'];
							$session_id = $cm_account['onlinecode'];
						} else {
							// ยังไม่เคยมีการ login จากหน้าแรก
							// เซ็ทคุกกี้สำหรับหน้าแรกให้เลย.
							$set_cm_account['id'] = $row->account_id;
							$set_cm_account['username'] = $data['username'];
							$set_cm_account['password'] = $row->account_password;
							$set_cm_account['fullname'] = $row->account_fullname;
							$set_cm_account['onlinecode'] = $session_id;
							$set_cm_account = $this->encrypt->encode(serialize($set_cm_account));
							set_cookie('member_account', $set_cm_account, 0);

							// เพราะว่ายังไม่เคยมีการ login จากหน้าแรก จึงต้องให้อัปเดท session
							$need_update_session = true;
						}

						// ตั้งค่าคุกกี้สำหรับหน้า admin
						$set_ca_account['id'] = $row->account_id;
						$set_ca_account['username'] = $data['username'];
						$set_ca_account['password'] = $row->account_password;
						$set_ca_account['onlinecode'] = $session_id;
						$set_ca_account = $this->encrypt->encode(serialize($set_ca_account));
						set_cookie('admin_account', $set_ca_account, 0);

						// ถ้าจะต้องมีการอัพเดท session
						// load date helper for gmt
						$this->load->helper('date');
						if ($need_update_session === true) {
							//$this->db->set('account_online_code', $session_id);// deprecated
							$this->db->set('account_last_login', date('Y-m-d H:i:s', time()));
							$this->db->set('account_last_login_gmt', date('Y-m-d H:i:s', local_to_gmt(time())));
							$this->db->where('account_id', $row->account_id);
							$this->db->update('accounts');

							// add online code for multisite check ------------------------------------------------
							$session_data['account_id'] = $row->account_id;
							$session_data['session_id'] = $session_id;
							$this->addLoginSession($session_data);
							unset($session_data);
							// add online code for multisite check ------------------------------------------------
						} else {
							$this->db->set('account_last_login', date('Y-m-d H:i:s', time()));
							$this->db->set('account_last_login_gmt', date('Y-m-d H:i:s', local_to_gmt(time())));
							$this->db->where('account_id', $row->account_id);
							$this->db->update('accounts');
							// add online code for multisite check ------------------------------------------------
							$session_data['account_id'] = $row->account_id;
							$this->addLoginSession($session_data);
							unset($session_data);
							// add online code for multisite check ------------------------------------------------
						}

						// record log in
						$this->adminLoginRecord($row->account_id, '1', 'Success');
						$query->free_result();

						// module plug here.
						$this->modules_plug->do_action('admin_login_process', $row);

						return true;
					} else {
						// has no permission to login here
						$query->free_result();

						$this->adminLoginRecord($row->account_id, '0', 'Not allow to login to admin page.');

						if (!$this->input->is_ajax_request()) {
							redirect(base_url());
						} else {
							return $this->lang->line('account_not_allow_login_here');
						}
					}
				} else {
					// password not match
					// login failed.
					$this->adminLoginRecord($row->account_id, '0', 'Wrong username or password');
					$query->free_result();
					unset($query, $row);

					return $this->lang->line('account_wrong_username_or_password');
				}
			} else {
				// account disabled
				$this->adminLoginRecord($row->account_id, '0', 'Account was disabed.');
				$query->free_result();
				
				return $this->lang->line('account_disabled') . ': ' . $row->account_status_text;
			}
		}
		
		$query->free_result();
		
		// login failed.
		$query = $this->db->get_where('accounts', array('account_username' => $data['username']));
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$this->adminLoginRecord($row->account_id, '0', 'Wrong username or password');
		}
		
		$query->free_result();
		
		return $this->lang->line('account_wrong_username_or_password');
	}// adminLogIn
	
	
	/**
	 * adminLoginRecord
	 * record log in.
	 * @param integer $account_id
	 * @param integer $attempt
	 * @param string $attempt_text
	 * @return boolean 
	 */
	public function adminLoginRecord($account_id = '', $attempt = '0', $attempt_text = '') 
	{
		if (!is_numeric($account_id) || !is_numeric($attempt)) {return false;}
		
		if ($attempt_text == null) {$attempt_text = null;}
		
		// load model
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		// load library
		$this->load->library(array('Browser'));
		
		// load helper
		$this->load->helper('date');// date helper required for gmt.
		
		// sql insert log
		$this->db->set('account_id', $account_id);
		$this->db->set('site_id', $site_id);
		$this->db->set('login_ua', $this->browser->getUserAgent());
		$this->db->set('login_os', $this->browser->getPlatform());
		$this->db->set('login_browser', $this->browser->getBrowser() . ' ' . $this->browser->getVersion());
		$this->db->set('login_ip', $this->input->ip_address());
		$this->db->set('login_time', date('Y-m-d h:i:s', time()));
		$this->db->set('login_time_gmt', date('Y-m-d H:i:s', local_to_gmt(time())));
		$this->db->set('login_attempt', $attempt);
		$this->db->set('login_attempt_text', $attempt_text);
		$this->db->insert('account_logins');
		
		return true;
	}// adminLoginRecord
	
	
	/**
	 * canIAddEditAccount
	 * check that if user can add or edit account
	 * if user level is higher priority than or equal to target's level priority, then ok.
	 * @param integer $my_account_id
	 * @param integer $target_level_id
	 * @return boolean 
	 */
	public function canIAddEditAccount($target_level_id = '', $my_account_id = '') 
	{
		if (!is_numeric($target_level_id)) {return false;}
		
		if (!is_numeric($my_account_id)) {
			$ca_account = $this->getAccountCookie('admin');
			if (isset($ca_account['id'])) {
				$my_account_id = $ca_account['id'];
			} else {
				return false;
			}
		}
		
		// get my level group id
		$my_level_group_id = $this->showAccountLevelInfo($my_account_id);
		if ($my_level_group_id == false) {return false;}
		
		// get my level priority
		$my_level_priority = $this->showAccountLevelGroupInfo($my_level_group_id, 'level_priority');
		
		// get target level priority
		$target_level_priority = $this->showAccountLevelGroupInfo($target_level_id, 'level_priority');
		if ($my_level_priority == false || $target_level_priority == false) {return false;}
		
		// check if higher? (higher is lower number, 1 is highest and 2 is lower)
		if ($my_level_priority <= $target_level_priority) {
			return true;
		}
		
		return false;
	}// canIAddEditAccount
	
	
	/**
	 * checkAccount
	 * check if account is really logged in, account is enabled, is duplicate login
	 * @param integer $id
	 * @param string $username
	 * @param string $password
	 * @param string $onlinecode
	 * @return boolean 
	 */
	public function checkAccount($id='', $username='', $password='', $onlinecode='') 
	{
		// load other model
		$this->load->model('config_model');
		
		// load library
		$this->load->library('session');
		
		// get cookie
		$ca_account = $this->getAccountCookie('admin');
		$c_account = $ca_account;
		
		// if admin cookie is not set...
		if (!isset($ca_account['id']) || !isset($ca_account['username']) || !isset($ca_account['password']) || !isset($ca_account['onlinecode'])) {
			// get member cookie.
			$cm_account = $this->getAccountCookie('member');
			
			// if member cookie is not set
			if (!isset($cm_account['id']) || !isset($cm_account['username']) || !isset($cm_account['password']) || !isset($cm_account['onlinecode'])) {
				// do nothing
			} else {
				// in this condition, member cookie is set.
				$c_account = $cm_account;
				unset($cm_account);
			}
		}
		unset($ca_account);
		
		// replace method's attributes with cookie
		if ($id == null || $username == null || $password == null || $onlinecode = '') {
			$id = $c_account['id'];
			$username = $c_account['username'];
			$password = $c_account['password'];
			$onlinecode = $c_account['onlinecode'];
		}
		
		// if still has no cookie account id
		if (!is_numeric($id)) {return false;}
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		// check cached
		if (false === $account_val = $this->cache->get('chkacc_'.$id.'_'.$site_id.'_'.$username.$password)) {
			// check with db
			$this->db->where('account_id', $id);
			$this->db->where('account_username', $username);
			$this->db->where('account_password', $password);
			$query = $this->db->get('accounts');
			if ($query->num_rows() > 0) {
				$row = $query->row();
				
				// check account disabled or not
				if ($row->account_status == '1') {
					// account is not disable
					
					// check if globa config not allow duplicate login
					if ($this->config_model->loadSingle('duplicate_login') == '0') {
						if ($this->isDuplicateLogin($id, $onlinecode) === true) {
							// dup log in detected.
							$query->free_result();
							$this->config_model->deleteCache('chkacc_'.$id.'_'.$site_id.'_');
							
							// log out
							$this->logOut();
							
							// load langauge
							$this->lang->load('account');
							
							// flash error and return
							$this->session->set_flashdata(
								'form_status',
								array(
									'form_status' => 'error',
									'form_status_message' => $this->lang->line('account_duplicate_login_detected')
								)
							);
							return false;
						}
					}
					
					// dup log in allowed or not allowed and check account pass!
					$query->free_result();
					
					// save to cache and return true
					$this->cache->save('chkacc_'.$id.'_'.$site_id.'_'.$username.$password, $this->getAccountOnlineCode($row->account_id, $site_id), 3600);
					return true;
				} else {
					// account was disabled
					$query->free_result();
					
					// delete cache
					$this->config_model->deleteCache('chkacc_'.$id.'_'.$site_id.'_');
					
					// log out
					$this->logOut();
					return false;
				}
			}
			// not found
			$query->free_result();
			
			// delete cache
			$this->config_model->deleteCache('chkacc_'.$id.'_'.$site_id.'_');
			
			// log out
			$this->logOut();
			return false;
		}
		
		// return cached
		if ($account_val != null) {
			// check global config not allow duplicate login
			if ($this->config_model->loadSingle('duplicate_login') == '0') {
				if ($onlinecode != $account_val) {
					// duplicate login detected
					
					// delete cache
					$this->config_model->deleteCache('chkacc_'.$id.'_');
					
					// log out
					$this->logOut();
					
					// load language
					$this->lang->load('account');
					
					// set error message and return false
					$this->session->set_flashdata('account_error', $this->lang->line('account_duplicate_login_detected'));
					return false;
				}
			}
			// dup log in allowed or not allowed and check account pass!
			return true;
		}
	}// checkAccount
	
	
	/**
	 * check admin permission
	 * check permission match to user'sgroup_id page_name and action
	 * @param integer $account_id
	 * @param string $page_name
	 * @param string $action
	 * @return boolean 
	 */
	public function checkAdminPermission($page_name = '', $action = '', $account_id = '') 
	{
		if ($account_id == null) {
			// account id is empty, get it from cookie.
			$ca_account = $this->getAccountCookie('admin');
			$account_id = (isset($ca_account['id']) ? $ca_account['id'] : '0');
		}
		
		// check for required attribute
		if (!is_numeric($account_id) || $page_name == null || $action == null) {return false;}
		
		if ($account_id == '1') {return true;}// permanent owner's account
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// check cached
		if (false === $check_admin_permission = $this->cache->get('check_admin_permission_'.SITE_TABLE.$page_name.'_'.$action.'_'.$account_id)) {
			$this->db->where('account_id', $account_id);
			$query = $this->db->get('account_level');
			
			if ($query->num_rows() > 0) {
				foreach ($query->result() as $row) {
					
					if ($row->level_group_id == '1') {
						// super admin group allow all by default.
						$query->free_result();
						$this->cache->save('check_admin_permission_'.SITE_TABLE.$page_name.'_'.$action.'_'.$account_id, 'true', 600);
						return true;
					}
					
					$this->db->where('permission_page', $page_name);
					$this->db->where('permission_action', $action);
					$this->db->where('level_group_id', $row->level_group_id);
					$query2 = $this->db->get('account_level_permission');
					
					if ($query2->num_rows() > 0) {
						$query->free_result();
						$query2->free_result();
						$this->cache->save('check_admin_permission_'.SITE_TABLE.$page_name.'_'.$action.'_'.$account_id, 'true', 600);
						return true;
					}
					$query2->free_result();
				}
				$query->free_result();
				
				$this->cache->save('check_admin_permission_'.SITE_TABLE.$page_name.'_'.$action.'_'.$account_id, 'false', 600);
				return false;
			}
			$query->free_result();
			
			$this->cache->save('check_admin_permission_'.SITE_TABLE.$page_name.'_'.$action.'_'.$account_id, 'false', 600);
			return false;
		}
		
		// return value
		if ($check_admin_permission == 'true') {
			return true;
		} else {
			return false;
		}
	}// checkAdminPermission
	
	
	/**
	 * alias of method checkAdminPermission.
	 */
	public function check_admin_permission($page_name = '', $action = '', $account_id = '') 
	{
		return $this->checkAdminPermission($page_name, $action, $account_id);
	}// check_admin_permission
	
	
	/**
	 * check password entered and hash one.
	 * @param string $entered_password
	 * @param string $hashed_password
	 * @return boolean
	 */
	public function checkPassword($entered_password = '', $hashed_password = '') 
	{
		if ($this->modules_plug->has_filter('account_check_hash_password')) {
			$result = $this->modules_plug->do_action('account_check_hash_password', array($entered_password, $hashed_password));
			
			if (isset($result['account_check_hash_password']) && is_array($result['account_check_hash_password'])) {
				return array_shift(array_values($result['account_check_hash_password']));
			}
			
			return false;
		} else {
			include_once 'application/libraries/PasswordHash.php';
			$PasswordHash = new PasswordHash(12, false);
			
			// check password
			return $PasswordHash->CheckPassword($entered_password, $hashed_password);
		}
	}// checkPassword
	
	
	/**
	 * count login fail 
	 * count continuous log in fail.
	 * @param string $username
	 * @return mixed 
	 */
	public function countLoginFail($username = '') 
	{
		if (empty($username)) {return false;}
		
		// get account data from username
		$this->db->where('account_username', $username);
		
		$query = $this->db->get('accounts');
		
		// not found this username.
		if ($query->num_rows() <= 0) {
			$query->free_result(); 
			return false;
		}
		
		$row = $query->row();
		
		$account_id = $row->account_id;
		
		$query->free_result();
		unset($query, $row);
		
		// get account logins data from account id.
		$this->db->where('account_id', $account_id);
		$this->db->order_by('account_login_id', 'desc');
		
		$query = $this->db->get('account_logins');
		
		if ($query->num_rows() > 0) {
			$i = 0;
			foreach ($query->result() as $row) {
				if ($row->login_attempt == '1') {
					$query->free_result();
					return $i;
				}
				$i++;
			}
			$query->free_result();
			return $i;
		}
		
		$query->free_result();
		return false;
	}// countLoginFail
	
	
	/**
	 * delete account
	 * @param integer $account_id
	 * @return boolean 
	 */
	public function deleteAccount($account_id = '') 
	{
		// check if guest id, first id, not delete.
		if ($account_id === '0' || $account_id === '1') {return false;}
		
		// update any post/comment/media 's account id to other account id here.
		$this->db->where('account_id', $account_id);
		$this->db->set('account_id', '1');
		$this->db->update('posts');
		//
		$this->db->where('account_id', $account_id);
		$this->db->set('account_id', '1');
		$this->db->update('post_revision');
		//
		$this->db->where('account_id', $account_id);
		$this->db->set('account_id', '0');
		$this->db->update('comments');
		//
		$this->db->where('account_id', $account_id);
		$this->db->set('account_id', '1');
		$this->db->update('files');
			
		// delete avatar
		$this->deleteAccountAvatar($account_id);
		
		// delete account
		$this->db->where('account_id', $account_id)->delete('account_fields');
		$this->db->where('account_id', $account_id)->delete('account_level');
		$this->db->where('account_id', $account_id)->delete('account_logins');
		$this->db->where('account_id', $account_id)->delete('account_sites');
		$this->db->where('account_id', $account_id)->delete('accounts');
		
		// delete cache.
		$this->config_model->deleteCache('ainf_');
		
		// get account info for send to api
		$this->db->where('account_id', $account_id);
		$query = $this->db->get('accounts');
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			// module plug here.
			$this->modules_plug->do_action('account_delete_account', $row);
			$query->free_result();
			unset($row);
		}
		unset($query);
		
		return true;
	}// deleteAccount
	
	
	
	/**
	 * delete account avatar
	 * @param integer $account_id
	 * @return boolean 
	 */
	public function deleteAccountAvatar($account_id = '') 
	{
		if (!is_numeric($account_id)) {return false;}
		
		$query = $this->db->where('account_id', $account_id)->get('accounts');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			if ($row->account_avatar != null && file_exists($row->account_avatar)) {
				unlink($row->account_avatar);
				$this->db->set('account_avatar', null);
				$this->db->where('account_id', $account_id);
				$this->db->update('accounts');
			}
			unset($row);
		}
		
		$query->free_result();
		unset($account_id, $query);
		
		return true;
	}// deleteAccountAvatar
	
	
	/**
	 * delete level group
	 * @param integer $level_group_id
	 * @return boolean 
	 */
	public function deleteLevelGroup($level_group_id = '') 
	{
		// check if level_group_id is number
		if (!is_numeric($level_group_id)) {return false;}
		
		// update current users who is in this group become to member group
		$this->db->set('level_group_id', '3');// 3 = member
		$this->db->where('level_group_id', $level_group_id);
		$this->db->update('account_level');
		
		// delete from account level permission
		$this->db->where('level_group_id', $level_group_id)->delete('account_level_permission');
		
		// delete from account_level
		$this->db->where('level_group_id', $level_group_id)->delete('account_level');
		
		// delete level group
		$this->db->where('level_group_id', $level_group_id)->delete('account_level_group');
		
		// delete cache
		$this->config_model->deleteCache('alg_'.SITE_TABLE.$level_group_id);
		
		return true;
	}// deleteLevelGroup
	
	
	/**
	 * edit account
	 * @param array $data
	 * @return mixed 
	 */
	public function editAccount($data = array()) 
	{
		if (!is_array($data) || empty($data)) {return false;}
		
		// change some variable in $data to other variable to prevent insert/update failed in accounts table
		// accounts table use $data, account_level may use $data2
		$data2['level_group_id'] = $data['level_group_id'];
		unset($data['level_group_id']);
		
		// check if changing target account to higher level than yours
		if (!$this->canIAddEditAccount($data2['level_group_id'])) {return $this->lang->line('account_cannot_edit_account_higher_your_level');}
		
		// check if email change?
		if ($data['account_old_email'] == $data['account_email']) {
			$email_change = 'no';
		} else {
			// check for duplicate email
			$this->db->where('account_id != ', $data['account_id']);
			$this->db->where('account_email', $data['account_email']);
			$query = $this->db->select('account_id, account_email')->get('accounts');
			
			if ($query->num_rows() > 0) {
				$query->free_result();
				return $this->lang->line('account_email_already_exists');
			} else {
				$email_change = 'yes';
				$data['account_new_email'] = $data['account_email'];
			}
			
			$query->free_result();
		}
		// end check for duplicate email
		
		// if email changed, send confirm
		if ($email_change == 'yes') {
			$send_change_email = $this->sendChangeEmail($data);
			
			if (isset($send_change_email['result']) && $send_change_email['result'] === true) {
				$data['account_confirm_code'] = $send_change_email['confirm_code'];
			} else {
				return $send_change_email;
			}
		}
		unset($send_change_email);
		
		// check avatar upload
		 if ($this->config_model->loadSingle('allow_avatar') == '1' && (isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null)) {
			 $result = $this->uploadAvatar($data['account_id']);
			 if (isset($result['result']) && $result['result'] === true) {
				 $data['account_avatar'] = $result['account_avatar'];
			 } else {
				 return $result;
			 }
		 }
		 unset($result);
		 
		 // check password change and set password value for update in db.
		if (!empty($data['account_new_password'])) {
			$data['account_old_password_encrypted'] = $data['account_password'];
			$data['account_new_password_encrypted'] = $this->encryptPassword($data['account_new_password']);
			$get_old_password_from_db = $this->show_accounts_info($data['account_id'], 'account_id', 'account_password');
			
			// check old password is match in db.
			if ($this->checkPassword($data['account_password'], $get_old_password_from_db)) {
				$data['account_password'] = $data['account_new_password_encrypted'];
				
				// module plug here
				$this->modules_plug->do_action('account_change_password', $data);
			} else {
				unset($get_old_password_from_db);
				return $this->lang->line('account_wrong_password');
			}
			unset($get_old_password_from_db);
		} else {
			// no password change, remove this variable to prevent set null value to db while update.
			unset($data['account_password']);
		}
		
		// remove unnecessary $data variable for push to update db.
		unset($data['account_old_email'], $data['account_email'], $data['account_new_password'], $data['account_old_password_encrypted'], $data['account_new_password_encrypted']);
		
		// update to db
		$this->db->where('account_id', $data['account_id']);
		$this->db->update('accounts', $data);
		
		// update or add level (if missing)
		$current_lv_group_id = $this->showAccountLevelInfo($data['account_id']);
		if ($current_lv_group_id !== false) {
			$this->db->set('level_group_id', $data2['level_group_id']);
			$this->db->where('account_id', $data['account_id']);
			$this->db->update('account_level');
		} else {
			$this->db->set('level_group_id', $data2['level_group_id']);
			$this->db->set('account_id', $data['account_id']);
			$this->db->insert('account_level');
		}
		
		// delete cache
		$this->config_model->deleteCache('ainf_');
		$this->config_model->deleteCache('chkacc_'.$data['account_id'].'_');
		
		// module plug here
		$data['level_group_id'] = $data2['level_group_id'];// set back for use in plugins
		$this->modules_plug->do_action('account_admin_edit', $data);
		
		return true;
	}// editAccount
	
	
	/**
	 * edit level group
	 * @param array $data
	 * @return boolean 
	 */
	public function editLevelGroup($data = array()) 
	{
		if (!is_array($data)) {return false;}
		
		// update to db
		$this->db->where('level_group_id', $data['level_group_id']);
		$this->db->update('account_level_group', $data);
		
		// delete cache
		$this->config_model->deleteCache('alg_'.SITE_TABLE);
		
		return true;
	}// editLevelGroup
	
	
	/**
	 * encrypt password
	 * @param string $password
	 * @return string
	 */
	public function encryptPassword($password = '') 
	{
		if (property_exists($this, 'modules_plug') && $this->modules_plug->has_filter('account_generate_hash_password')) {
			return $this->modules_plug->do_filter('account_generate_hash_password', $password);
		} else {
			include_once dirname(dirname(__FILE__)).'/libraries/PasswordHash.php';
			$PasswordHash = new PasswordHash(12, false);
			return $PasswordHash->HashPassword($password);
		}
	}// encryptPassword
	
	
	/**
	 * get account cookie
	 * get cookie and decode > unserialize to array and return
	 * @param string $level
	 * @return array|null 
	 */
	public function getAccountCookie($level = 'admin') 
	{if ($level != 'admin' && $level != 'member') {$level = 'member';}
		
		// load helper & library
		$this->load->helper('cookie');
		$this->load->library('encrypt');
		
		// get cookie
		$c_account = get_cookie($level . '_account', true);
		if ($c_account != null) {
			$c_account = $this->encrypt->decode($c_account);
			$c_account = @unserialize($c_account);
			return $c_account;
		}
		
		return null;
		
	}// getAccountCookie
	
	
	/**
	 * alias of method getAccountCookie.
	 */
	public function get_account_cookie($level = 'admin') 
	{
		return $this->getAccountCookie($level);
	}// get_account_cookie
	
	
	/**
	 * get account data in single row by condition.
	 * @param array $data
	 * @return mixed
	 */
	public function getAccountData($data = array()) 
	{
		if (!is_array($data) || (is_array($data) && !isset($data['accounts.account_id']) && !isset($data['account_id']) && !isset($data['account_username']) && !isset($data['account_email']))) {
			return false;
		}
		
		// remove ambiguous column
		if (isset($data['account_id'])) {
			$data['accounts.account_id'] = $data['account_id'];
			unset($data['account_id']);
		}
		
		$this->db->select('*, accounts.account_id AS account_id, account_level.level_group_id AS level_group_id');
		$this->db->join('account_level', 'account_level.account_id = accounts.account_id', 'left');
		$this->db->join('account_level_group', 'account_level_group.level_group_id = account_level.level_group_id', 'left');
		$this->db->where($data);
		$query = $this->db->get('accounts');
		
		return $query->row();
	}// getAccountData
	
	
	/**
	 * get account fields from db
	 * @param integer $account_id
	 * @param array $data
	 * @return mixed
	 */
	public function getAccountFields($account_id = '', $data = array()) 
	{
		if (!is_numeric($account_id)) {
			return null;
		}
		
		$this->db->from('account_fields')
				->where('account_id', $account_id);
		
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0) {
			return $query->result();
		}
		
		$query->free_result();
		
		return null;
	}// getAccountFields
	
	
	/**
	 * get data from account_level_group table
	 * @param array $data
	 * @return mixed
	 */
	public function getAccountLevelGroupData($data = array()) 
	{
		if (!isset($data['level_group_id']) && !isset($data['level_priority'])) {
			return false;
		}
		
		$this->db->where($data);
		$query = $this->db->get('account_level_group');
		
		return $query->row();
	}// getAccountLevelGroupData
	
	
	/**
	 * get account online code
	 * @param integer $account_id
	 * @param integer $site_id
	 * @return string
	 */
	public function getAccountOnlineCode($account_id = '', $site_id = '') 
	{
		$this->db->where('account_id', $account_id)
			   ->where('site_id', $site_id);
		$query = $this->db->get('account_sites');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			
			return $row->account_online_code;
		}
		
		$query->free_result();
		return null;
	}// getAccountOnlineCode
	
	
	/**
	 * hash password
	 * alias name of encrypt password.
	 * @param string $password
	 * @return string
	 */
	public function hashPassword($password = '') 
	{
		return $this->encryptPassword($password);
	}// hashPassword
	
	
	
	/**
	 * check is admin login
	 * @return boolean 
	 */
	public function isAdminLogin() 
	{
		// get admin cookie
		$ca_account = $this->getAccountCookie('admin');
		
		// check admin cookie is set or not?
		if (!isset($ca_account['id']) || !isset($ca_account['username']) || !isset($ca_account['password']) || !isset($ca_account['onlinecode'])) {
			return false;
		}
		
		// check again in database
		return $this->checkAccount();
	}// isAdminLogin
	
	
	/**
	 * is duplicate login or is simultaneously login
	 * @param string $onlinecode
	 * @return boolean
	 */
	public function isDuplicateLogin($account_id = '', $onlinecode = '') 
	{
		// if not set online code.
		if ($onlinecode == null) {
			$cm_account = $this->getAccountCookie('member');
			
			if (!isset($cm_account['onlinecode'])) {
				return false;
			}
			
			$onlinecode = $cm_account['onlinecode'];
		}
		
		// load model
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		$this->db->where('account_id', $account_id)
			   ->where('site_id', $site_id)
			   ->where('account_online_code', $onlinecode);
		$query = $this->db->get('account_sites');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			
			if ($row->account_online_code == $onlinecode) {
				// NOT duplicate login
				return false;
			}
			return true;
		}
		
		$query->free_result();
		return false;
	}// isDuplicateLogin
	
	
	/**
	 * check if member log
	 * @return boolean 
	 */
	public function isMemberLogin() 
	{
		// get member cookie
		$cm_account = $this->getAccountCookie('member');
		
		// check if member cookie is not set
		if (!isset($cm_account['id']) || !isset($cm_account['username']) || !isset($cm_account['password']) || !isset($cm_account['onlinecode'])) {
			return false;
		}
		
		// check again in database
		return $this->checkAccount();
	}// isMemberLogin
	
	
	/**
	 * list accounts
	 * @param array $data
	 * @return mixed 
	 */
	public function listAccount($data = array()) 
	{
		// query sql
		$this->db->join('account_level', 'account_level.account_id = accounts.account_id', 'left');
		$this->db->join('account_level_group', 'account_level_group.level_group_id = account_level.level_group_id', 'left');
		$q = trim($this->input->get('q'));
		if ($q != null && $q != 'none') {
			$like_data[0]['field'] = 'accounts.account_username';
			$like_data[0]['match'] = $q;
			$like_data[1]['field'] = 'accounts.account_email';
			$like_data[1]['match'] = $q;
			$like_data[2]['field'] = 'accounts.account_fullname';
			$like_data[2]['match'] = $q;
			$like_data[3]['field'] = 'accounts.account_status_text';
			$like_data[3]['match'] = $q;
			$this->db->like_group($like_data);
			unset($like_data);
		}
		$this->db->group_by('accounts.account_id');
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'account_username');
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'asc');
		$this->db->order_by($orders, $sort);
		//$sql .= ' order by '.$orders.' '.$sort;
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('accounts');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		// html encode for links.
		$q = urlencode(htmlspecialchars($q));
		
		// pagination-----------------------------
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->uri->uri_string()).'?' . generate_querystring_except(array('per_page'));
		if (isset($data['per_page']) && is_numeric($data['per_page'])) {
			$config['per_page'] = $data['per_page'];
		} else {
			$config['per_page'] = (isset($data['list_for']) && $data['list_for'] == 'admin' ? 20 : $this->config_model->loadSingle('content_items_perpage'));
		}
		$config['total_rows'] = $total;
		// pagination tags customize for bootstrap css framework
		$config['num_links'] = 3;
		$config['page_query_string'] = true;
		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = "</ul></div>\n";
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		// end customize for bootstrap
		$config['first_link'] = '|&lt;';
		$config['last_link'] = '&gt;|';
		$this->pagination->initialize($config);
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		
		$this->db->limit($config['per_page'], ($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')));
		
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listAccount
	
	
	/**
	 * list account logins
	 * @param integer $account_id
	 * @param array $data
	 * @return mixed 
	 */
	public function listAccountLogins($account_id = '', $data = array()) 
	{
		if (!is_numeric($account_id)) {return null;}
		
		// query sql
		$this->db->join('sites', 'sites.site_id = account_logins.site_id', 'left');
		$this->db->where('account_id', $account_id);
		
		// order and sort
		$orders = strip_tags(trim($this->input->get('orders')));
		$orders = ($orders != null ? $orders : 'account_login_id');
		$sort = strip_tags(trim($this->input->get('sort')));
		$sort = ($sort != null ? $sort : 'desc');
		$this->db->order_by($orders, $sort);
		
		// clone object before run $this->db->get()
		$this_db = clone $this->db;
		
		// query for count total
		$query = $this->db->get('account_logins');
		$total = $query->num_rows();
		$query->free_result();
		
		// restore $this->db object
		$this->db = $this_db;
		unset($this_db);
		
		// pagination-----------------------------
		$this->load->library('pagination');
		$config['base_url'] = site_url($this->uri->uri_string()).'?' . generate_querystring_except(array('per_page', 'orders', 'sort')) . '&amp;orders=' . htmlspecialchars($orders) . '&amp;sort=' . htmlspecialchars($sort);
		$config['total_rows'] = $total;
		if (isset($data['per_page']) && is_numeric($data['per_page'])) {
			$config['per_page'] = $data['per_page'];
		} else {
			$config['per_page'] = (isset($data['list_for']) && $data['list_for'] == 'admin' ? 20 : $this->config_model->loadSingle('content_items_perpage'));
		}
		// pagination tags customize for bootstrap css framework
		$config['num_links'] = 3;
		$config['page_query_string'] = true;
		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = "</ul></div>\n";
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		// end customize for bootstrap
		$config['first_link'] = '|&lt;';
		$config['last_link'] = '&gt;|';
		$this->pagination->initialize($config);
		// pagination create links in controller or view. $this->pagination->create_links();
		// end pagination-----------------------------
		
		$this->db->limit($config['per_page'], ($this->input->get('per_page') == null ? '0' : $this->input->get('per_page')));
		$query = $this->db->get('account_logins');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $total;
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listAccountLogins
	
	
	/**
	 * listLevelGroup
	 * @param boolean $noguest
	 * @return array|null
	 */
	public function listLevelGroup($noguest = true) 
	{
		if ($noguest) {
			$this->db->where('level_group_id !=', '4');
		}
		$this->db->order_by('level_priority', 'asc');
		
		$query = $this->db->get('account_level_group');
		
		if ($query->num_rows() > 0) {
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		return null;
	}// listLevelGroup
	
	
	/**
	 * logout
	 */
	public function logOut() 
	{
		// get account id from cookie
		$cm_account = $this->getAccountCookie('admin');
		if (!isset($cm_account['id'])) {
			$cm_account = $this->getAccountCookie('member');
		}
		
		// delete cache of this account id
		if (isset($cm_account['id']) && isset($cm_account['username'])) {
			$this->config_model->deleteCache('chkacc_'.$cm_account['id'].'_');
		}
		
		// load helper for delete cookie
		$this->load->helper(array('cookie'));
		
		// delete cookie
		delete_cookie('admin_account');
		delete_cookie('member_account');
		
		// module plug here
		$this->modules_plug->do_action('account_logout', $cm_account);
		
		// done
		return true;
	}// logOut
	
	
	/**
	 * login fail last time
	 * @param string $username
	 * @return mixed 
	 */
	public function loginFailLastTime($username = '') 
	{
		if (empty($username)) {return false;}
		
		// get account id from username
		$this->db->where('account_username', $username);
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() <= 0) {
			$query->free_result(); 
			return false;
		}
		
		$row = $query->row();
		$account_id = $row->account_id;
		
		$query->free_result();
		unset($query, $row);
		
		// get account login fail last time.
		$this->db->where('account_id', $account_id);
		$this->db->order_by('account_login_id', 'desc');
		
		$query = $this->db->get('account_logins');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			if ($row->login_attempt == '0') {
				return $row->login_time;
			} else {
				return false;
			}
		}
		
		$query->free_result();
		return false;
	}// loginFailLastTime
	
	
	/**
	 * member edit profile
	 * @param array $data
	 * @return mixed 
	 */
	public function memberEditProfile($data = array()) 
	{
		if (empty($data) || !is_array($data)) {return false;}
		
		// check if email change?
		if ($data['account_old_email'] == $data['account_email']) {
			$email_change = 'no';
		} else {
			// check for duplicate email
			$this->db->where('account_id != ', $data['account_id']);
			$this->db->where('account_email', $data['account_email']);
			$query = $this->db->select('account_id, account_email')->get('accounts');
			if ($query->num_rows() > 0) {
				$query->free_result();
				return $this->lang->line('account_email_already_exists');
			} else {
				$email_change = 'yes';
				$data['account_new_email'] = $data['account_email'];
			}
			$query->free_result();
			unset($query);
		}
		// end check for duplicate email
		
		// if email changed, send confirm
		if ($email_change == 'yes') {
			$send_change_email = $this->sendChangeEmail($data);
			if (isset($send_change_email['result']) && $send_change_email['result'] === true) {
				$data['account_confirm_code'] = $send_change_email['confirm_code'];
			} else {
				return $send_change_email;
			}
		}
		unset($send_change_email);
		
		// check avatar upload
		 if ($this->config_model->loadSingle('allow_avatar') == '1' && (isset($_FILES['account_avatar']['name']) && $_FILES['account_avatar']['name'] != null)) {
			 $result = $this->uploadAvatar($data['account_id']);
			 if (isset($result['result']) && $result['result'] === true) {
				 $data['account_avatar'] = $result['account_avatar'];
			 } else {
				 return $result;
			 }
		 }
		 unset($result);
		 
		 // check password change and set password value for update in db.
		if (!empty($data['account_new_password'])) {
			$old_password = $this->encryptPassword($data['account_password']);
			$data['account_old_password_encrypted'] = $data['account_password'];
			$data['account_new_password_encrypted'] = $this->encryptPassword($data['account_new_password']);
			$get_old_password_from_db = $this->show_accounts_info($data['account_id'], 'account_id', 'account_password');
			
			// check old password is match in db.
			if ($this->checkPassword($data['account_password'], $get_old_password_from_db)) {
				$data['account_password'] = $data['account_new_password_encrypted'];
				
				// module plug here
				$this->modules_plug->do_action('account_change_password', $data);
			} else {
				unset($get_old_password_from_db);
				return $this->lang->line('account_wrong_password');
			}
			unset($get_old_password_from_db);
		} else {
			// no password change, remove this variable to prevent set null value to db while update.
			unset($data['account_password']);
		}
		
		// remove unnecessary $data variable for push to update db.
		unset($data['account_old_email'], $data['account_email'], $data['account_new_password'], $data['account_old_password_encrypted'], $data['account_new_password_encrypted']);
		
		// update to db
		$this->db->where('account_id', $data['account_id']);
		$this->db->update('accounts', $data);
		
		// delete cache
		$this->config_model->deleteCache('ainf_');
		$this->config_model->deleteCache('chkacc_'.$data['account_id'].'_');
		
		// module plug here
		$this->modules_plug->do_action('account_member_edit', $data);
		
		return true;
	}// memberEditProfile
	
	
	/**
	 * member login
	 * @param array $data
	 * @return mixed 
	 */
	public function memberLogIn($data = array()) 
	{
		if (!isset($data['account_username']) || !isset($data['account_password'])) {return false;}
		
		$this->db->where('account_username', $data['account_username']);
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			if ($row->account_status == '1') {
				// check hash password.
				if ($this->checkPassword($data['account_password'], $row->account_password) === true) {
					// generate session id for check simultanous login
					$this->load->library('session');
					$session_id = $this->session->userdata('session_id');

					// set cookie
					$this->load->library('encrypt');
					$this->load->helper('cookie');

					$expires = ($this->input->post('remember', true) == 'yes' ? (60*60*24*365)/12 : '0');
					$set_cm_account['id'] = $row->account_id;
					$set_cm_account['username'] = $data['account_username'];
					$set_cm_account['password'] = $row->account_password;
					$set_cm_account['fullname'] = $row->account_fullname;
					$set_cm_account['onlinecode'] = $session_id;
					$set_cm_account = $this->encrypt->encode(serialize($set_cm_account));
					set_cookie('member_account', $set_cm_account, $expires);

					// update session
					$this->load->helper('date');
					//$this->db->set('account_online_code', $session_id);// deprecated
					$this->db->set('account_last_login', date('Y-m-d H:i:s', time()));
					$this->db->set('account_last_login_gmt', date('Y-m-d H:i:s', local_to_gmt(time())));
					$this->db->where('account_id', $row->account_id);
					$this->db->update('accounts');

					// add online code for multisite check ------------------------------------------------
					$session_data['account_id'] = $row->account_id;
					$session_data['session_id'] = $session_id;
					$this->addLoginSession($session_data);
					unset($session_data);
					// add online code for multisite check ------------------------------------------------

					// record log in
					$this->adminLoginRecord($row->account_id, '1', 'Success');
					$query->free_result();

					// module plug here
					$this->modules_plug->do_action('account_login_process', $data);
					unset($query, $row, $session_id, $expires, $set_cm_account);

					return true;
				} else {
					// password not match
					// login failed.
					$this->adminLoginRecord($row->account_id, '0', 'Wrong username or password');
					$query->free_result();
					unset($query, $row);

					return $this->lang->line('account_wrong_username_or_password');
				}
			} else {
				// account disabled
				$this->adminLoginRecord($row->account_id, '0', 'Account was disabed.');
				$query->free_result();
				unset($query);
				
				return $this->lang->line('account_disabled') . ': ' . $row->account_status_text;
			}
		}
		$query->free_result();
		
		// login failed.
		$query = $this->db->get_where('accounts', array('account_username' => $data['account_username']));
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$this->adminLoginRecord($row->account_id, '0', 'Wrong username or password');
		}
		$query->free_result();
		unset($query, $row);
		
		return $this->lang->line('account_wrong_username_or_password');
	}// memberLogIn
	
	
	/**
	 * for member register their account
	 * @param type $data
	 * @return boolean
	 */
	public function registerAccount($data = array()) 
	{
		if (empty($data) || !is_array($data)) {return false;}
		
		// check duplicate account (duplicate username)
		$this->db->where('account_username', $data['account_username']);
		$query = $this->db->select('account_username')->get('accounts');
		if ($query->num_rows() > 0) {
			$query->free_result();
			return $this->lang->line('account_username_already_exists');
		}
		$query->free_result();
		
		// check duplicate account (duplicate email)
		$this->db->where('account_email', $data['account_email']);
		$query = $this->db->select('account_email')->get('accounts');
		if ($query->num_rows() > 0) {
			$query->free_result();
			return $this->lang->line('account_email_already_exists');
		}
		$query->free_result();
		
		// generate confirm code
		$this->load->helper('string');
		$data['account_confirm_code'] = random_string('alnum', '6');
		
		// send register email
		$send_result = $this->sendRegisterEmail($data);
		if ($send_result !== true) {
			return $send_result;
		}
		unset($send_result);
		
		// load date helper for gmt
		$this->load->helper('date');
		
		// set new values for add to db
		$data['account_password'] = $this->encryptPassword($data['account_password']);
		$data['account_create'] = date('Y-m-d H:i:s', time());
		$data['account_create_gmt'] = date('Y-m-d H:i:s', local_to_gmt(time()));
		$data['account_status'] = '0';
		if ($this->config_model->loadSingle('member_verification') == '2') {
			$data['account_status_text'] = 'Waiting for admin verification.';
		}
		
		// add to db
		$this->db->insert('accounts', $data);
		
		// get account id
		$account_id = $this->db->insert_id();
		
		// add level
		$this->load->model('siteman_model');
		$list_site = $this->siteman_model->listWebsitesAll();
		
		if (isset($list_site['items']) && is_array(($list_site['items']))) {
			foreach ($list_site['items'] as $site) {
				$site_table_prefix = '';

				if ($site->site_id != '1') {
					$site_table_prefix = $site->site_id . '_';
				}
				
				$this->db->set('level_group_id', '3');
				$this->db->set('account_id', $account_id);
				$this->db->insert($this->db->dbprefix($site_table_prefix . 'account_level'));
			}
		}
		unset($list_site, $site);
		
		// module plug here
		$this->modules_plug->do_action('account_register', $data);
		
		// done
		return true;
	}// registerAccount
	
	
	/**
	 * reset password step 1
	 * request to reset password
	 * @param string $email
	 * @return mixed 
	 */
	public function resetPassword1($email = '') 
	{
		if (empty($email)) {return false;}
		
		// load libraries
		$this->load->library(array('email', 'email_template'));
		
		// load helper
		$this->load->helper(array('string'));
		
		$this->db->where('account_email', $email);
		$query = $this->db->get('accounts');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			
			// if account was disabled
			if ($row->account_status == '0') {
				$query->free_result();
				return $this->lang->line('account_disabled') . ': ' . $row->account_status_text;
			}
			
			// generate confirm_code
			$confirm_code = random_string('alnum', 5);
			
			// genrate new password
			$new_password = random_string('alnum', 10);
			
			// confirm and cancel link
			$link_confirm = site_url('account/resetpw2/' . $row->account_id . '/' . $confirm_code);
			$link_cancel = site_url('account/resetpw2/' . $row->account_id . '/0');
			
			// email content
			$email_content = $this->email_template->read_template('reset_password1.html');
			$email_content = str_replace("%username%", $row->account_username, $email_content);
			$email_content = str_replace("%newpassword%", $new_password, $email_content);
			$email_content = str_replace("%linkconfirm%", $link_confirm, $email_content);
			$email_content = str_replace("%linkcancel%", $link_cancel, $email_content);
			
			// send email
			$this->email->from($this->config_model->loadSingle('mail_sender_email'));
			$this->email->to($email);
			$this->email->subject($this->lang->line('account_email_reset_password1'));
			$this->email->message($email_content);
			$this->email->set_alt_message(str_replace("\t", '', strip_tags($email_content)));
			if ($this->email->send() == false) {
				// email could not send.
				unset($confirm_code, $new_password, $link_cancel, $link_confirm, $email_content);
				$query->free_result();
				return $this->lang->line('account_email_could_not_send');
			}
			
			// add to db
			$this->db->set('account_confirm_code', $confirm_code);
			$this->db->set('account_new_password', $this->encryptPassword($new_password));
			$this->db->where('account_id', $row->account_id);
			$this->db->update('accounts');
			unset($confirm_code, $new_password, $link_cancel, $link_confirm, $email_content);
			$query->free_result();
			
			return true;
		}
		$query->free_result();
		
		return $this->lang->line('account_not_found_with_this_email');
	}// resetPassword1
	
	
	/**
	 * send change email
	 * @param array $data
	 * @return mixed 
	 */
	public function sendChangeEmail($data = array()) 
	{
		// for email changed, send email for confirm.
		
		// load library
		$this->load->library(array('email', 'email_template'));
		
		// load helper
		$this->load->helper(array('string'));
		
		// generate confirm_code
		$confirm_code = random_string('alnum', 5);
		
		// email content
		$email_content = $this->email_template->read_template('change_email1.html');
		$email_content = str_replace("%username%", $data['account_username'], $email_content);
		$email_content = str_replace("%newemail%", $data['account_email'], $email_content);
		$link_confirm = site_url('account/changeemail2/' . $data['account_id'] . '/' . $confirm_code);
		$link_cancel = site_url('account/changeemail2/' . $data['account_id'] . '/0');
		$email_content = str_replace("%linkconfirm%", $link_confirm, $email_content);
		$email_content = str_replace("%linkcancel%", $link_cancel, $email_content);
		
		// send email
		$this->email->from($this->config_model->loadSingle('mail_sender_email'));
		$this->email->to($data['account_old_email']);
		$this->email->subject($this->lang->line('account_email_change1'));
		$this->email->message($email_content);
		$this->email->set_alt_message(str_replace("\t", '', strip_tags($email_content)));
		if ($this->email->send() == false) {
			// email could not send.
			unset($confirm_code, $link_cancel, $link_confirm, $email_content);
			return $this->lang->line('account_email_could_not_send');
		}
		unset($link_cancel, $link_confirm, $email_content);
		
		return array('result' => true, 'confirm_code' => $confirm_code);
		// end for email changed, send email for confirm.
	}// sendChangeEmail
	
	
	/**
	 * send register email
	 * @param array $data
	 * @return mixed 
	 */
	public function sendRegisterEmail($data = array()) 
	{
		if (!isset($data['account_username']) || !isset($data['account_email']) || !isset($data['account_confirm_code'])) {return false;}
		
		// load email library
		$this->load->library(array('email', 'email_template'));
		
		// load config values
		$cfg = $this->config_model->load(array('member_verification', 'mail_sender_email', 'member_register_notify_admin', 'member_admin_verify_emails'));
		
		// email content
		$member_verification = $cfg['member_verification']['value'];
		if ($member_verification == '1') {
			$email_content = $this->email_template->read_template('register_account.html');
		} elseif ($member_verification == '2') {
			$email_content = $this->email_template->read_template('register_account_adminverify.html');
		}
		$email_content = str_replace("%username%", $data['account_username'], $email_content);
		$email_content = str_replace('%register_confirm_link%', site_url('account/confirm-register/'.urlencode($data['account_username']).'/'.$data['account_confirm_code']), $email_content);
		
		// send email
		$this->email->from($cfg['mail_sender_email']['value']);
		$this->email->to($data['account_email']);
		if ($member_verification == '1') {
			$this->email->subject($this->lang->line('account_email_register_confirm'));
		} elseif ($member_verification == '2') {
			$this->email->subject($this->lang->line('account_email_register_adminmod'));
		}
		$this->email->message($email_content);
		$this->email->set_alt_message(str_replace("\t", '', strip_tags($email_content)));
		if ($this->email->send() == false) {
			// email could not send.
			unset($email_content, $member_verification);
			log_message('error', 'Could not send email to user.');
			return $this->lang->line('account_email_could_not_send');
		}
		
		// send email to notify admin
		if (!isset($data['resend-register'])) {
			if ($member_verification == '2' || $cfg['member_register_notify_admin']['value']) {
				// email content
				$email_content = $this->email_template->read_template('admin_notice_new_account.html');
				$email_content = str_replace("%username%", $data['account_username'], $email_content);
				
				// send email
				$this->email->clear();
				$this->email->from($cfg['mail_sender_email']['value']);
				$this->email->to($cfg['member_admin_verify_emails']['value']);
				$this->email->subject(sprintf($this->lang->line('account_email_please_moderate_member'), $data['account_username']));
				$this->email->message($email_content);
				$this->email->set_alt_message(str_replace("\t", '', strip_tags($email_content)));
				if ($this->email->send() == false) {
					// email could not send.
					unset($email_content, $member_verification);
					log_message('error', 'Could not send email to notify admin.');
					return $this->lang->line('account_email_could_not_send');
				}
			}
		}
		unset($cfg, $member_verification, $email_content);
		
		return true;
	}// sendRegisterEmail
	
	
	/**
	 * show account evel group info
	 * show info from account_level_group table
	 * @param integer $lv_group_id
	 * @param string $return_field
	 * @return mixed 
	 */
	public function showAccountLevelGroupInfo($lv_group_id = '', $return_field = 'level_name') 
	{
		if (!is_numeric($lv_group_id)) {return false;}
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// check cached
		if (! $alg_val = $this->cache->get('alg_'.SITE_TABLE.$lv_group_id.'_'.$return_field)) {
			$this->db->where('level_group_id', $lv_group_id);
			$query = $this->db->get('account_level_group');
			
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$query->free_result();
				$this->cache->save('alg_'.SITE_TABLE.$lv_group_id.'_'.$return_field, $row->$return_field, 3600);
				return $row->$return_field;
			}
			
			$query->free_result();
			return null;
		}
		
		return $alg_val;
	}// showAccountLevelGroupInfo
	
	
	/**
	 * show account level info
	 * @param integer $account_id
	 * @param boolean $return_level_name
	 * @return mixed 
	 */
	public function showAccountLevelInfo($account_id = '', $return_level_name = false) 
	{
		if ($account_id == null) {
			$ca_account = $this->getAccountCookie('admin');
			$cm_account = $this->getAccountCookie('member');
			if (isset($ca_account['id'])) {
				$account_id = $ca_account['id'];
			} elseif (isset($cm_account['id'])) {
				$account_id = $cm_account['id'];
			} else {
				return false;
			}
		}
		
		$this->db->where('account_id', $account_id);
		$query = $this->db->get('account_level');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			
			if ($return_level_name == true) {
				return $this->showAccountLevelGroupInfo($row->level_group_id);
			}
			
			return $row->level_group_id;
		}
		$query->free_result();
		
		return false;
	}// showAccountLevelInfo
	
	
	/**
	 * show_accounts_info
	 * show info inside accounts table
	 * @param mixed $check_value
	 * @param string $check_field
	 * @param string $return_field
	 * @return mixed
	 */
	public function showAccountsInfo($check_value = '', $check_field = 'account_id', $return_field = 'account_username') 
	{
		if ($check_value == null || $check_field == null || $return_field == null) {return false;}
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// check cached
		if (false === $ainf = $this->cache->get('ainf_'.$check_value.'_'.$check_field.'_'.$return_field)) {
			$this->db->where($check_field, $check_value);
			$query = $this->db->get('accounts');
			
			if ($query->num_rows() > 0) {
				$row = $query->row();
				$query->free_result();
				$this->cache->save('ainf_'.$check_value.'_'.$check_field.'_'.$return_field, $row->$return_field, 3600);
				return $row->$return_field;
			}
			
			$query->free_result();
			return false;
		}
		
		return $ainf;
	}// showAccountsInfo
	
	
	/**
	 * alias of method showAccountsInfo.
	 */
	public function show_accounts_info($check_value = '', $check_field = 'account_id', $return_field = 'account_username') 
	{
		return $this->showAccountsInfo($check_value, $check_field, $return_field);
	}// show_accounts_info
	
	
	/**
	 * upload avatar
	 * @param integer $account_id
	 * @return mixed 
	 */
	public function uploadAvatar($account_id = '') 
	{
		if (!is_numeric($account_id)) {return false;}
		
		$cfg = $this->config_model->load(array('avatar_path', 'avatar_allowed_types', 'avatar_size'));
		
		$config['upload_path'] = $cfg['avatar_path']['value'];
		$config['allowed_types'] = $cfg['avatar_allowed_types']['value'];
		$config['max_size'] = $cfg['avatar_size']['value'];
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);
		
		if (! $this->upload->do_upload('account_avatar')) {
			return $this->upload->display_errors('<div>', '</div>');
		} else {
			// upload success, delete old avatar
			$this->deleteAccountAvatar($account_id);
			
			// get file data
			$data = $this->upload->data();
			
			// resize to prevent very large image upload
			$this->load->library('vimage', $data['full_path']);
			$this->vimage->resize_ratio(500, 2000);
			$this->vimage->save('', $data['full_path']);
			
			return array('result' => true, 'account_avatar' => $config['upload_path'].$data['file_name']);
		}
	}// uploadAvatar
	

}

