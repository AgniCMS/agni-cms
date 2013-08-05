<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class install_model extends CI_Model 
{
	
	
	public $db_name;
	public $db_host;
	public $db_username;
	public $db_password;
	public $db_port;
	public $db_table_prefix;
	
	public $post_port = null;// for check later that input post enter port or not


	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function install_configured($data = array()) 
	{
		if (!is_array($data)) {
			$output['result'] = false;
			$output['result_text'] = 'Error in install config model.';
			return $output;
		}
		// set config value----------------------------------------------------------------------------------------------
		// site info
		$this->db->set('config_value', $data['site_name']);
		$this->db->where('config_name', 'site_name');
		$this->db->update('config');
		
		$this->db->set('config_value', $data['timezones']);
		$this->db->where('config_name', 'site_timezone');
		$this->db->update('config');
		// sender email
		$this->db->set('config_value', $data['sender_email']);
		$this->db->where('config_name', 'mail_sender_email');
		$this->db->update('config');
		
		$this->db->set('config_value', $data['sender_email']);
		$this->db->where('config_name', 'mail_smtp_user');
		$this->db->update('config');
		// notify email
		$this->db->set('config_value', $data['account_email']);
		$this->db->where('config_name', 'member_admin_verify_emails');
		$this->db->update('config');
		
		$this->db->set('config_value', $data['account_email']);
		$this->db->where('config_name', 'comment_admin_notify_emails');
		$this->db->update('config');
		
		// set super admin account value-----------------------------------------------------------------------------
		$this->db->set('account_username', $data['account_username']);
		$this->db->set('account_email', $data['account_email']);
		$this->db->set('account_password', $data['encrypted_password']);
		$this->db->set('account_timezone', $data['timezones']);
		$this->db->where('account_id', '1');
		$this->db->update('accounts');
		
		// install site on sites table -------------------------------------------------------------------------------------
		$query = $this->db->where('site_id', '1')->get('sites');
		if ($query->num_rows() <= 0) {
			$this->db->set('site_name', $data['site_name'])
					->set('site_domain', $_SERVER['HTTP_HOST'])
					->set('site_status', '1')
					->set('site_create', time())
					->set('site_create_gmt', local_to_gmt(time()))
					->set('site_update', time())
					->set('site_update_gmt', local_to_gmt(time()))
					->insert('sites');
		} else {
			$this->db->set('site_name', $data['site_name'])
					->set('site_domain', $_SERVER['HTTP_HOST'])
					->set('site_status', '1')
					->set('site_update', time())
					->set('site_update_gmt', local_to_gmt(time()))
					->where('site_id', '1')
					->update('sites');
		}
		$query->free_result();
		
		// done
		$output['result'] = true;
		return $output;
	}// install_configured
	
	
	public function install_db() 
	{
		//
		if ($this->db_port == null) {
			$this->db_port = ini_get("mysqli.default_port");
			$this->post_port = null;
		} else {
			$this->post_port = $this->db_port;
		}
		//
		$sql_file = $this->load->file(dirname(APPPATH).'/agni-empty-install.sql', true);
		// standardize new line
		$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
		// choose 1 that match in .sql file
		#$sql_file = preg_replace("/\#(.*)\n$/", "", $sql_file);// ลบพวก comment ใน sql แบบ # table xxxx ออก
		$sql_file = preg_replace("/--(.*)\n/", "", $sql_file);// ลบพวก comment ในแบบ -- table xxxx ออก
		$asql = explode(";\n", $sql_file);
		// connect db
		$link = mysqli_connect($this->db_host, $this->db_username, $this->db_password, $this->db_name, $this->db_port);
		
		// check empty db?----------------------------------------------------------------------------------------------
		if ($result = mysqli_query($link, 'SHOW TABLES;')) {
			$i = 0;
			while ($row = mysqli_fetch_object($result)) {
				$i++;
			}
			mysqli_free_result($result);
			if ($i >= 1) {
				$output['result_text'] = lang('agni_please_empty_db');
				$output['result'] = false;
				return $output;
			}
		}
		unset($i , $row, $result);
		
		// import db------------------------------------------------------------------------------------------------------
		$i = 0;
		foreach ($asql as $key) {
			$key = trim($key);
			$key = str_replace('`an_', '`'.$this->db_table_prefix, $key);
			if ($key != null) {
				$query_result = mysqli_query($link, $key);
				if ($query_result !== false) {
					$i++;
				}
			}
		}
		mysqli_close($link);
		unset($link, $key, $sql_file, $a_sql, $query_result);
		if ($i <= 0) {
			$output['result'] = false;
			$output['result_text'] = lang('agni_fail_install_db');
			return $output;
		}
		
		// write database file-------------------------------------------------------------------------------------------
		$this->load->helper('file');
		// read database.php.bak
		$db_template = read_file('../application/config/database.php.bak');
		$db_template = str_replace('$db[\'default\'][\'hostname\'] = \'localhost\';', '$db[\'default\'][\'hostname\'] = \''.$this->db_host.($this->post_port == null ? '' : ':'.$this->db_port).'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'username\'] = \'root\';', '$db[\'default\'][\'username\'] = \''.$this->db_username.'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'password\'] = \'\';', '$db[\'default\'][\'password\'] = \''.$this->db_password.'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \''.$this->db_name.'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'dbprefix\'] = \'an_\';', '$db[\'default\'][\'dbprefix\'] = \''.$this->db_table_prefix.'\';', $db_template);
		// write to database.php
		$result = write_file('../application/config/database.php', $db_template, 'w+');
		if ($result === false) {
			$output['result'] = false;
			$output['result_text'] = lang('agni_cant_write_database_php');
			return $output;
		}
		
		// generate hash text and prefix and names in config file----------------------------------------------------------------------------
		$this->load->helper('string');
		$new_encryption_key = random_string('alnum', 9);
		$new_cookie_prefix = random_string('alpha', 3).'_';
		$new_csrf_token = random_string('alpha', 5).'_token';
		$new_csrf_cookie = random_string('alpha', 5).'_cookie';
		// read config.php.bak
		$config_php = read_file('../application/config/config.php.bak');
		$config_php = preg_replace("#\\\$config\['language'\] = '(.*)';#", '\$config[\'language\'] = \''.$this->lang->get_current_lang().'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['language_abbr'\] = '(.*)';#", '\$config[\'language_abbr\'] = \''.$this->lang->get_current_lang(false).'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['log_threshold'\] = (\d);#", '\$config[\'log_threshold\'] = 0;', $config_php);
		$config_php = preg_replace("#\\\$config\['encryption_key'\] = '(.*)';#", '\$config[\'encryption_key\'] = \''.$new_encryption_key.'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['cookie_prefix\'] = '(.*)';#", '\$config[\'cookie_prefix\'] = \''.$new_cookie_prefix.'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['csrf_token_name'\] = '(.*)';#", '\$config[\'csrf_token_name\'] = \''.$new_csrf_token.'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['csrf_cookie_name'\] = '(.*)';#", '\$config[\'csrf_cookie_name\'] = \''.$new_csrf_cookie.'\';', $config_php);
		// write to config.php
		$result = false;
		if ($config_php != null) {
			$result = write_file('../application/config/config.php', $config_php, 'w+');
		}
		if ($result === false) {
			$output['result'] = false;
			$output['result_text'] = lang('agni_cant_write_config_php');
			return $output;
		}
		
		// update required data in db ---------------------------------------------------------------------------------------------------
		// get configured from files
		include_once('../application/config/database.php');
		
		// reformat config for manual connect db
		foreach ($db['default'] as $key => $item) {
			$db[$key] = $item;
		}
		
		// this step connected to db. if fail or wrong settings, it should throw error.
		$this->load->database($db);
		
		$this->load->helper(array('date'));
		$data['site_domain'] = $this->input->server('HTTP_HOST');
		$data['site_create'] = time();
		$data['site_create_gmt'] = local_to_gmt(time());
		$data['site_update'] = time();
		$data['site_update_gmt'] = local_to_gmt(time());
		$this->db->where('site_id', '1')
			   ->update($this->db_table_prefix.'sites', $data);
		unset($data);
		// update required data in db ---------------------------------------------------------------------------------------------------
		
		// done
		$output['result'] = true;
		return $output;
	}// install_db
	
	
	public function install_sample_data($sample_data = '') 
	{
		// get configured from files
		include_once('../application/config/database.php');
		
		// reformat config for manual connect db
		foreach ($db['default'] as $key => $item) {
			$db[$key] = $item;
		}
		
		// this step connected to db. if fail or wrong settings, it should throw error.
		$this->load->database($db);
		
		$sql_file = $this->load->file(dirname(APPPATH).'/'.$sample_data.'.sql', true);
		
		// standardize new line
		$sql_file = str_replace(array("\r\n", "\r"), "\n", $sql_file);
		
		// choose 1 that match in .sql file
		#$sql_file = preg_replace("/\#(.*)\n$/", "", $sql_file);// ลบพวก comment ใน sql แบบ # table xxxx ออก
		$sql_file = preg_replace("/--(.*)\n/", "", $sql_file);// ลบพวก comment ในแบบ -- table xxxx ออก
		
		$asql = explode(";\n", $sql_file);
		
		foreach ($asql as $key) {
			$key = trim($key);
			$key = str_replace('`an_', '`'.$db['dbprefix'], $key);
			if ($key != null) {
				$this->db->query($key);
			}
		}
		
		return true;
	}// install_sample_data
	
	
	public function test_connect_db($db_host = '', $db_username = '', $db_password = '', $db_name = '', $db_port = '') 
	{
		if ($db_host == null || $db_username == null || $db_password == null || $db_name == null) {
			return false;
		}
		if ($db_port == null) {
			$db_port = ini_get("mysqli.default_port");
		}
		//
		$link = @mysqli_connect($db_host, $db_username, $db_password, $db_name, $db_port);
		if (!$link) {
			$output['result'] = false;
			$output['result_text'] = 'Error: '.mysqli_connect_errno().' '.mysqli_connect_error();
		} else {
			$output['result'] = true;
			$output['result_text'] = '<span class="icon-ok"></span> '.lang('agni_db_connect_successfully');
		}
		@mysqli_close($link);
		return $output;
	}// test_connect_db
	
	
}

// EOF