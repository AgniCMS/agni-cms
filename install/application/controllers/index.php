<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class index extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load
		$this->load->model(array('install_model'));
		
		// load helper
		$this->load->helper(array('cookie', 'date', 'form'));
	}// __construct
	
	
	public function ajax_install_sample_data() 
	{
		if (!$this->input->is_ajax_request()) {return false;}
		
		if ($_POST) {
			$sample_data = $this->input->post('sample_data');
			
			$result = $this->install_model->install_sample_data($sample_data);
			
			if ($result === true) {
				$output['result'] = true;
			} else {
				$output['result'] = false;
				$output['result_text'] = $result;
			}
			
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// ajax_install_sample_data
	
	
	public function ajax_test_db() 
	{
		if ($_POST) {
			$db_name = trim($this->input->post('db_name'));
			$db_username = trim($this->input->post('db_username'));
			$db_password = trim($this->input->post('db_password'));
			$db_host = trim($this->input->post('db_host'));
			$db_port = trim($this->input->post('db_port'));
			$db_table_prefix = trim($this->input->post('db_table_prefix'));
			//
			$output = $this->install_model->test_connect_db($db_host, $db_username, $db_password, $db_name, $db_port);
			//
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($output));
		}
	}// ajax_test_db
	
	
	public function finished() 
	{
		// check verified
		$verify = get_cookie('agni_install_verify');
		if ($verify == null || $verify == 'fail') {
			delete_cookie('agni_install_verify');
			redirect('./');
		}
		
		// check step2
		$step2 = get_cookie('agni_install_step2');
		if ($step2 == null || $step2 != 'pass') {
			delete_cookie('agni_install_step2');
			redirect('./');
		}
		
		// check step3
		$step3 = get_cookie('agni_install_step3');
		if ($step3 == null || $step3 != 'pass') {
			delete_cookie('agni_install_step3');
			redirect('index/step3');
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->lang->line('agni_agnicms').' &gt; '.$this->lang->line('agni_install');
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('template/finished_view', $output);
	}// finished
	
	
	public function index() 
	{
		// list check result-----------------------------------------------------------------------------------------------
		
		// web server
		$list_verify['agni_webserver']['value'] = $this->input->server('SERVER_SOFTWARE');
		$list_verify['agni_webserver']['result'] = (strpos($this->input->server('SERVER_SOFTWARE'), 'Apache/2') !== false ? 'pass' : 'warn');
		
		// php
		$list_verify['agni_vf_php']['value'] = phpversion();
		if (phpversion() >= 5.3) {
			$list_verify['agni_vf_php']['result'] = 'pass';
		} elseif (phpversion() >= 5.2) {
			$list_verify['agni_vf_php']['result'] = 'warn';
			$list_verify['agni_vf_php']['result_text'] = lang('agni_vf_php_recomment_version');
		} else {
			$list_verify['agni_vf_php']['result'] = 'fail';
			$list_verify['agni_vf_php']['result_text'] = lang('agni_vf_php_recomment_version');
		}
		
		// register global
		$register_globals = ini_get('register_globals');
		if (strtolower($register_globals) == 'on' || (!empty($register_globals) && is_numeric($register_globals))) {
			$list_verify['agni_vf_regglobal']['value'] = lang('agni_enable');
			$list_verify['agni_vf_regglobal']['result'] = 'fail';
		} else {
			$list_verify['agni_vf_regglobal']['value'] = lang('agni_disable');
			$list_verify['agni_vf_regglobal']['result'] = 'pass';
		}
		
		// magic quote gpc
		if (function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) {
			// is on
			$list_verify['agni_vf_magic_quote_gpc']['value'] = lang('agni_enable');
			$list_verify['agni_vf_magic_quote_gpc']['result'] = 'fail';
			$list_verify['agni_vf_magic_quote_gpc']['result_text'] = lang('agni_please_disable_magic_quotes_gpc');
		} else {
			$list_verify['agni_vf_magic_quote_gpc']['value'] = lang('agni_disable');
			$list_verify['agni_vf_magic_quote_gpc']['result'] = 'pass';
		}
		
		// php extensions----------------------------------------------------------------
		$extensions = get_loaded_extensions();
		natsort($extensions);
		$required_extensions = array('curl', 'date', 'gd', 'hash', 'iconv', 'json', 'mbstring', 'mysqli', 'xml', 'zip');
		// preset variables
		$not_installed_extensions = array();
		$critical_error = false;
		// loop test
		foreach ($required_extensions as $required_extension) {
			if (!in_array($required_extension, $extensions) && $required_extension != 'mysqli') {
				$not_installed_extensions[] = $required_extension;
			} elseif ($required_extension == 'mysqli') {
				if (!in_array('mysqli', $extensions) && !in_array('mysql', $extensions)) {
					$not_installed_extensions[] = $required_extension;
					$critical_error = true;
				} elseif (!in_array('mysqli', $extensions)) {
					$not_installed_extensions[] = $required_extension;
				} elseif (!in_array('mysql', $extensions)) {
					$not_installed_extensions[] = $required_extension;
					$critical_error = true;
				}
			}
		}
		
		if (empty($not_installed_extensions)) {
			// verified pass!
			$list_verify['agni_vf_php_extensions']['value'] = lang('agni_enable');
			$list_verify['agni_vf_php_extensions']['result'] = 'pass';
		} else {
			if ($critical_error === false) {
				$list_verify['agni_vf_php_extensions']['value'] = lang('agni_disable');
				$list_verify['agni_vf_php_extensions']['result'] = 'warn';
				$list_verify['agni_vf_php_extensions']['result_text'] = sprintf(lang('agni_php_missing_extensions'), implode(', ', $not_installed_extensions));
			} elseif ($critical_error === true) {
				$list_verify['agni_vf_php_extensions']['value'] = lang('agni_disable');
				$list_verify['agni_vf_php_extensions']['result'] = 'fail';
				$list_verify['agni_vf_php_extensions']['result_text'] = sprintf(lang('agni_php_missing_extensions'), implode(', ', $not_installed_extensions));
			}
		}
		// php extensions----------------------------------------------------------------
		
		// db support
		if (function_exists('mysqli_connect')) {
			$list_verify['agni_vf_db_support']['value'] = lang('agni_enable');
			$list_verify['agni_vf_db_support']['result'] = 'pass';
		} elseif (function_exists('mysql_connect') || function_exists('mysql_pconnect')) {
			$list_verify['agni_vf_db_support']['value'] = lang('agni_enable');
			$list_verify['agni_vf_db_support']['result'] = 'warn';
			$list_verify['agni_vf_db_support']['result_text'] = lang('agni_vf_mysql_recommend_function');
		} else {
			$list_verify['agni_vf_db_support']['value'] = lang('agni_disable');
			$list_verify['agni_vf_db_support']['result'] = 'fail';
			$list_verify['agni_vf_db_support']['result_text'] = lang('agni_please_enable_mysql');
		}
		// check writable folders and files-----------------------------------------------
		$required_write_dirfile = array(
			'../application/cache', 
			'../application/config/config.php', 
			'../application/config/database.php', 
			'../application/logs', 
			'../modules', 
			'../public/upload', 
			'../public/upload/avatar', 
			'../public/upload/media',
			'../public/upload/unzip',
			'../public/themes'
		);
		foreach ($required_write_dirfile as $file) {
			if (substr(sprintf('%o', fileperms($file)), -3) == '777' || (is_file($file) && substr(sprintf('%o', fileperms($file)), -3) == '666')) {
				$list_verify_write[$file]['value'] = lang('agni_writable');
				$list_verify_write[$file]['result'] = 'pass';
			} else {
				$list_verify_write[$file]['value'] = lang('agni_unwritable');
				$list_verify_write[$file]['result'] = 'fail';
				$list_verify_write[$file]['result_text'] = lang('agni_this_dir_or_file_cannot_writable_please_change_permission');
			}
		}
		// check writable folders and files-----------------------------------------------
		
		// list check result-----------------------------------------------------------------------------------------------
		
		$output['list_verify'] = $list_verify;
		$output['list_verify_writable'] = $list_verify_write;
		
		// head tags output ##############################
		$output['page_title'] = $this->lang->line('agni_agnicms').' &gt; '.$this->lang->line('agni_install');
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('template/index_view', $output);
	}// index
	
	
	public function step2() 
	{
		// check verified
		$verify = get_cookie('agni_install_verify');
		if ($verify == null || $verify == 'fail') {
			delete_cookie('agni_install_verify');
			redirect('./');
		}
		
		//
		if ($_POST) {
			$db_name = trim($this->input->post('db_name'));
			$db_username = trim($this->input->post('db_username'));
			$db_password = trim($this->input->post('db_password'));
			$db_host = trim($this->input->post('db_host'));
			$db_port = trim($this->input->post('db_port'));
			$db_table_prefix = trim($this->input->post('db_table_prefix'));
			
			// form validate
			$this->load->library('form_validation');
			$this->form_validation->set_rules('db_name', 'lang:agni_db_name', 'trim|required');
			$this->form_validation->set_rules('db_username', 'lang:agni_db_username', 'trim|required');
			$this->form_validation->set_rules('db_password', 'lang:agni_db_password', 'trim|required');
			$this->form_validation->set_rules('db_host', 'lang:agni_db_host', 'trim|required');
			
			if ($this->form_validation->run() === false) {
				$output['form_status'] = '<div class="alert alert-error"><ul>'.validation_errors('<li>', '</li>').'</ul></div>';
			} else {
				// test connect mysql
				$result = $this->install_model->test_connect_db($db_host, $db_username, $db_password, $db_name, $db_port);
				if ($result['result'] !== true) {
					$output['form_status'] = '<div class="alert alert-error">'.$result['result_text'].'</div>';
				} else {
					$this->install_model->db_name = $db_name;
					$this->install_model->db_host = $db_host;
					$this->install_model->db_username = $db_username;
					$this->install_model->db_password = $db_password;
					$this->install_model->db_port = $db_port;
					$this->install_model->db_table_prefix = $db_table_prefix;
					// start import .sql into db
					$result = $this->install_model->install_db();
					if ($result['result'] !== true) {
						$output['form_status'] = '<div class="alert alert-error">'.$result['result_text'].'</div>';
					} else {
						// next step
						set_cookie('agni_install_step2', 'pass', 86400);
						redirect('index/step3');
					}
				}
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->lang->line('agni_agnicms').' &gt; '.$this->lang->line('agni_install');
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('template/step2_view', $output);
	}// step2
	
	
	public function step3() 
	{
		// check verified
		$verify = get_cookie('agni_install_verify');
		if ($verify == null || $verify == 'fail') {
			delete_cookie('agni_install_verify');
			redirect('./');
		}
		
		// check step2
		$step2 = get_cookie('agni_install_step2');
		if ($step2 == null || $step2 != 'pass') {
			delete_cookie('agni_install_step2');
			redirect('./');
		}
		
		// get configured from files
		include_once('../application/config/database.php');
		
		// reformat config for manual connect db
		foreach ($db['default'] as $key => $item) {
			$db[$key] = $item;
		}
		
		// this step connected to db. if fail or wrong settings, it should throw error.
		$this->load->database($db);
		
		include_once('../application/config/config.php');
		
		// set config on /install to match the main app (for easier to configure site)
		$this->config->set_item('encryption_key', $config['encryption_key']);
		// 
		if ($_POST) {
			$data['site_name'] = trim($this->input->post('site_name'));
			$data['sender_email'] = trim($this->input->post('sender_email'));
			$data['timezones'] = trim($this->input->post('timezones'));
			$data['account_username'] = trim($this->input->post('account_username'));
			$data['account_email'] = trim($this->input->post('account_email'));
			$data['account_password'] = trim($this->input->post('account_password'));
			// form validate
			$this->load->library('form_validation');
			$this->form_validation->set_rules('site_name', 'lang:agni_config_site_name', 'trim|required');
			$this->form_validation->set_rules('sender_email', 'lang:agni_config_site_email', 'trim|required|valid_email');
			$this->form_validation->set_rules('account_username', 'lang:agni_config_sa_username', 'trim|required');
			$this->form_validation->set_rules('account_email', 'lang:agni_config_sa_email', 'trim|required|valid_email');
			$this->form_validation->set_rules('account_password', 'lang:agni_config_sa_password', 'trim|required');
			$this->form_validation->set_rules('account_cf_password', 'lang:agni_config_sa_confirm_password', 'trim|required|matches[account_password]');
			if ($this->form_validation->run() === false) {
				$output['form_status'] = '<div class="alert alert-error"><ul>'.validation_errors('<li>', '</li>').'</ul></div>';
			} else {
				// get main app model to gen password
				include_once('../application/models/account_model.php');
				
				$account_model = new account_model();
				
				$data['encrypted_password'] = $account_model->encryptPassword($data['account_password']);
				$result = $this->install_model->install_configured($data);
				
				if (isset($result['result']) && $result['result'] === true) {
					// install complete.
					set_cookie('agni_install_step3', 'pass', 86400);
					redirect('index/finished');
				} else {
					$output['form_status'] = '<div class="alert alert-error">'.$result['result_text'].'</div>';
				}
			}
		}
		
		// head tags output ##############################
		$output['page_title'] = $this->lang->line('agni_agnicms').' &gt; '.$this->lang->line('agni_install');
		// meta tags
		// link tags
		// script tags
		// end head tags output ##############################
		
		// output
		$this->generate_page('template/step3_view', $output);
	}// step3
	
	
}

// EOF