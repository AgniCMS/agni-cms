<?php
/*
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 */


class agni_update 
{
	
	
	public $agni_update_core_name = 'agni_update_core';
	public $agni_update_module_name = 'agni_update_module';
	
	
	/**
	 * check_update_core
	 * check update core and insert/update to queue.
	 * @return boolean
	 */
	public function check_update_core() 
	{
		// get CI instance
		$ci =& get_instance();
		
		// load configuration to check
		$cfg = $ci->config_model->load(array('agni_auto_update_url', 'agni_version'));
		
		// get target xml from update server to check
		$xml = $this->get_xml($cfg['agni_auto_update_url']['value']);
		
		// can't get xml update
		if ($xml === false) {return false;}
		
		// got xml update, loop check.
		foreach ($xml as $update) {
			// if module name is agnicms and version > currently use version
			if ($update->moduleName == 'agnicms' && $update->version > $cfg['agni_version']['value']) {
				// if target version is null or target version <= currently use version
				if ($update->targetVersion == '' || ($update->targetVersion != '' && $update->targetVersion <= $cfg['agni_version']['value'])) {
					// xml has update core available. add to queue for admin to click update
					$ci->load->model('queue_model');
					
					$data['queue_name'] = $this->agni_update_core_name;
					$data_arr['update_available'] = true;
					$data_arr['update_version'] = (string) $update->version;
					$data['queue_data'] = serialize($data_arr);
					$data['queue_update'] = time();
					if (!$ci->queue_model->isQueueExists($data['queue_name'])) {
						// queue is not exists, use insert queue.
						$data['queue_create'] = time();

						$ci->queue_model->addQueue($data);
					} else {
						// queue exists, use update queue.
						// get queue data
						$queue = $ci->queue_model->getQueueData(array('queue_name' => $data['queue_name']));
						if ($queue != null) {
							$data['queue_id'] = $queue->queue_id;
							
							$ci->queue_model->editQueue($data);
						}
					}
					
					unset($data, $data_arr);
					break;
				}
			}
		}
		
		unset($cfg, $ci, $xml);
		
		return true;
	}// check_update_core
	
	
	/**
	 * get_xml
	 * get remote xml.
	 * @param string $xml_url target xml url
	 * @return mixed return SimpleXML object on success, or return false on failure.
	 */
	public function get_xml($xml_url = '') 
	{
		libxml_use_internal_errors(true);
		
		try {
			if ($this->url_exists($xml_url, array('301', '302')) == false) {
				// log message.
				log_message('error', 'Fail to load remote XML.');
				return false;
			}
			
			$sxe = new SimpleXMLElement($xml_url, 0, true);
			
			return $sxe;
		} catch (Exception $e){ 
			// error, cannot get xml or something.
			// log message.
			log_message('error', 'Fail to load remote XML.');
			
			libxml_clear_errors();
			
			return false;
		}
	}// get_xml
	
	
	/**
	 * update_core_copy_files
	 * update core > copy extracted files and folders to installed folder.
	 * @param array $data
	 * @return boolean
	 */
	public function update_core_copy_files($data = array()) 
	{
		// check required $data
		if (!isset($data['downloaded_file']) || !isset($data['unzip_path'])) {
			die('Missing required data in $data. '.__FILE__.' '.__LINE__);
		}
		
		// get CI instance
		$ci =& get_instance();
		
		$ci->load->library('filesystem');
		
		// copy reserved variables --------------------------------------------------------------------------------------------------------
		// copy reserved variables from application/config/config.php
		//include_once(dirname(dirname(__FILE__)).'/config/config.php');
		$bak_config = $ci->config->config;
		
		// copy reserved variables from application/config/database.php
		include('application/config/database.php');
		// copy reserved variables --------------------------------------------------------------------------------------------------------
		
		// copy folder and files ------------------------------------------------------------------------------------------------------------
		// get rewrite method
		$rewrite_method = $ci->session->userdata('rewrite_method');
		
		if ($rewrite_method == 'filesys') {
			$result = $ci->filesystem->copy($data['unzip_path'], dirname(dirname(dirname(__FILE__))));
		} elseif ($rewrite_method == 'ftp') {
			// if rewrite method is ftp
			// get ftp config from session.
			$ftp_config = $ci->session->userdata('ftp_config');

			$ci->filesystem->use_ftp = true;

			// store config for use in ftp library.
			$ci->filesystem->ftp_hostname = $ftp_config['ftp_host'];
			$ci->filesystem->ftp_username = $ftp_config['ftp_username'];
			$ci->filesystem->ftp_password = $ftp_config['ftp_password'];
			$ci->filesystem->ftp_port = $ftp_config['ftp_port'];
			$ci->filesystem->ftp_passive = $ftp_config['ftp_passive'];
			$ci->filesystem->ftp_basepath = $ftp_config['ftp_basepath'];
			
			$result = $ci->filesystem->copy($data['unzip_path'], '/');
		}
		
		// if cannot copy files, do not continue
		if (!isset($result) || (isset($result) && $result === false)) {
			return false;
		}
		
		unset($result);
		// copy folder and files ------------------------------------------------------------------------------------------------------------
		
		// delete unzipped and downloaded files ------------------------------------------------------------------------------------
		$update_folder_exp = explode('/', $data['unzip_path']);
		unset($update_folder_exp[(count($update_folder_exp)-2)]);
		$update_folder = implode('/', $update_folder_exp);
		
		$ci->filesystem->rmdir($update_folder);
		
		unset($update_folder, $update_folder_exp);
		// delete unzipped and downloaded files ------------------------------------------------------------------------------------
		
		// copy config .bak to .php and paste reserved variables. -------------------------------------------------------------
		// if use ftp rewrite method, chmod config.php and database.php to 666(writable) before.
		if($rewrite_method == 'ftp') {
			$ci->filesystem->chmod('application/config/config.php', '0666');
			$ci->filesystem->chmod('application/config/database.php', '0666');
		}
		
		// get index page.
		$index_page = rtrim(str_replace($ci->lang->get_current_lang(), '', $bak_config['index_page']), '/');
		
		// read config.php.bak ---------------------------------------------------------------
		$config_php = read_file('application/config/config.php.bak');
		$config_php = preg_replace("#\\\$config\['index_page'\] = '(.*)';#", '\$config[\'index_page\'] = \''.$index_page.'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['uri_protocol'\] = '(.*)';#", '\$config[\'uri_protocol\'] = \''.$bak_config['uri_protocol'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['url_suffix'\] = '(.*)';#", '\$config[\'url_suffix\'] = \''.$bak_config['url_suffix'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['language'\] = '(.*)';#", '\$config[\'language\'] = \''.$bak_config['language'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['language_abbr'\] = '(.*)';#", '\$config[\'language_abbr\'] = \''.$bak_config['language_abbr'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['lang_uri_abbr'\] = '(.*)';#", '\$config[\'lang_uri_abbr\'] = '.str_replace(array('  ', "\r\n", "\r", "\n", ',)'), array(' ', '', '', '', ',)'), var_export($bak_config['lang_uri_abbr'], true)).';', $config_php);
		$config_php = preg_replace("#\\\$config\['lang_ignore'\] = (.*);#", '\$config[\'lang_ignore\'] = '.var_export($bak_config['lang_ignore'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['lang_ignore_default'\] = (.*);#", '\$config[\'lang_ignore_default\'] = '.var_export($bak_config['lang_ignore_default'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['enable_hooks'\] = (.*);#", '\$config[\'enable_hooks\'] = '.var_export($bak_config['enable_hooks'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['permitted_uri_chars'\] = (.*);#", '\$config[\'permitted_uri_chars\'] = '.var_export($bak_config['permitted_uri_chars'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['log_threshold'\] = (\d);#", '\$config[\'log_threshold\'] = 0;', $config_php);
		$config_php = preg_replace("#\\\$config\['log_path'\] = '(.*)';#", '\$config[\'log_path\'] = \''.$bak_config['log_path'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['log_date_format'\] = '(.*)';#", '\$config[\'log_date_format\'] = \''.$bak_config['log_date_format'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['cache_path'\] = '(.*)';#", '\$config[\'cache_path\'] = \''.$bak_config['cache_path'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['encryption_key'\] = '(.*)';#", '\$config[\'encryption_key\'] = \''.$bak_config['encryption_key'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_cookie_name'\] = '(.*)';#", '\$config[\'sess_cookie_name\'] = \''.$bak_config['sess_cookie_name'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_expiration'\] = '(.*)';#", '\$config[\'sess_expiration\'] = '.$bak_config['sess_expiration'].';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_expire_on_close'\] = (.*);#", '\$config[\'sess_expire_on_close\'] = '.var_export($bak_config['sess_expire_on_close'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_encrypt_cookie'\] = (.*);#", '\$config[\'sess_encrypt_cookie\'] = '.var_export($bak_config['sess_encrypt_cookie'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_table_name'] = '(.*)';#", '\$config[\'sess_table_name\'] = \''.$bak_config['sess_table_name'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_match_ip'\] = (.*);#", '\$config[\'sess_match_ip\'] = '.var_export($bak_config['sess_match_ip'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_match_useragent'\] = (.*);#", '\$config[\'sess_match_useragent\'] = '.var_export($bak_config['sess_match_useragent'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['sess_time_to_update'\] = (.*);#", '\$config[\'sess_time_to_update\'] = '.var_export($bak_config['sess_time_to_update'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['cookie_prefix\'] = '(.*)';#", '\$config[\'cookie_prefix\'] = \''.$bak_config['cookie_prefix'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['cookie_domain'] = '(.*)';#", '\$config[\'cookie_domain\'] = \''.$bak_config['cookie_domain'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['cookie_path'] = '(.*)';#", '\$config[\'cookie_path\'] = \''.$bak_config['cookie_path'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['cookie_secure'] = (.*);#", '\$config[\'cookie_secure\'] = '.var_export($bak_config['cookie_secure'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['csrf_token_name'\] = '(.*)';#", '\$config[\'csrf_token_name\'] = \''.$bak_config['csrf_token_name'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['csrf_cookie_name'\] = '(.*)';#", '\$config[\'csrf_cookie_name\'] = \''.$bak_config['csrf_cookie_name'].'\';', $config_php);
		$config_php = preg_replace("#\\\$config\['compress_output'\] = (.*);#", '\$config[\'compress_output\'] = '.var_export($bak_config['compress_output'], true).';', $config_php);
		$config_php = preg_replace("#\\\$config\['proxy_ips'\] = '(.*)';#", '\$config[\'proxy_ips\'] = \''.$bak_config['proxy_ips'].'\';', $config_php);
		
		if ($config_php != null) {
			write_file('application/config/config.php', $config_php, 'w+');
		}
		
		// done with config.php
		unset($bak_config, $config_php);
		
		// read database.php.bak ---------------------------------------------------------------
		$db_template = read_file('application/config/database.php.bak');
		$db_template = preg_replace("#\\\$active_group = '(.*)';#", '\$active_group = \''.$active_group.'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'hostname\'] = \'localhost\';', '$db[\'default\'][\'hostname\'] = \''.$db['default']['hostname'].'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'username\'] = \'root\';', '$db[\'default\'][\'username\'] = \''.$db['default']['username'].'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'password\'] = \'\';', '$db[\'default\'][\'password\'] = \''.$db['default']['password'].'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'database\'] = \'\';', '$db[\'default\'][\'database\'] = \''.$db['default']['database'].'\';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['driver'\] = '(.*)';#", '\$db[\'default\'][\'driver\'] = \''.$db['default']['dbdriver'].'\';', $db_template);
		$db_template = str_replace('$db[\'default\'][\'dbprefix\'] = \'an_\';', '$db[\'default\'][\'dbprefix\'] = \''.$db['default']['dbprefix'].'\';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['pconnect'\] = (.*);#", '\$db[\'default\'][\'pconnect\'] = '.var_export($db['default']['pconnect'], true).';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['db_debug'\] = (.*);#", '\$db[\'default\'][\'db_debug\'] = '.var_export($db['default']['db_debug'], true).';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['cache_on'\] = (.*);#", '\$db[\'default\'][\'cache_on\'] = '.var_export($db['default']['cache_on'], true).';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['cachedir'\] = '(.*)';#", '\$db[\'default\'][\'cachedir\'] = \''.$db['default']['cachedir'].'\';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['swap_pre'\] = '(.*)';#", '\$db[\'default\'][\'swap_pre\'] = \''.$db['default']['swap_pre'].'\';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['autoinit'\] = (.*);#", '\$db[\'default\'][\'autoinit\'] = '.var_export($db['default']['autoinit'], true).';', $db_template);
		$db_template = preg_replace("#\\\$db\['default'\]\['stricton'\] = (.*);#", '\$db[\'default\'][\'stricton\'] = '.var_export($db['default']['stricton'], true).';', $db_template);
		
		if ($db_template != null) {
			write_file('application/config/database.php', $db_template, 'w+');
		}
		
		// done with database.php
		unset($active_group, $active_record, $db, $db_template);
		// copy config .bak to .php and paste reserved variables. -------------------------------------------------------------
		
		return true;
	}// update_core_copy_files
	
	
	/**
	 * update core database
	 * 
	 * we are using CI's migration (there is no better way right now). 
	 * after everything was copied to root web, the migration files and version in config should be there.
	 * just run migration->current() to alter database to current version.
	 * 
	 * @return boolean
	 */
	public function update_core_database() 
	{
		include_once('application/config/migration.php');
		// if migration disabled
		if (isset($config['migration_enabled']) && $config['migration_enabled'] === false) {
			return false;
		}
		
		// get CI instance
		$ci =& get_instance();
		
		$ci->load->library('migration');
		$ci->migration->current();
		
		return true;
	}// update_core_database
	
	/**
	 * update_core_download
	 * download update, extract zipped file
	 * @return mixed
	 */
	public function update_core_download() 
	{
		// get CI instance
		$ci =& get_instance();
		
		$ci->load->library('filesystem');
		
		// get update xml file.
		$cfg = $ci->config_model->load(array('agni_auto_update_url', 'agni_version'));
		
		$xml = $this->get_xml($cfg['agni_auto_update_url']['value']);
		
		foreach ($xml as $update) {
			// if module name is agnicms and version > currently use version
			if ($update->moduleName == 'agnicms' && $update->version > $cfg['agni_version']['value']) {
				// if target version is null or target version <= currently use version
				if ($update->targetVersion == '' || ($update->targetVersion != '' && $update->targetVersion <= $cfg['agni_version']['value'])) {
					// if download link is not null and exists.
					if ($update->download != null && $this->url_exists($update->download, array('301', '302'))) {
						$rewrite_method = $ci->session->userdata('rewrite_method');
						$update_folder = $ci->config->item('agni_upload_path').'unzip/agnicms_update_core/';
						
						// if rewrite method is filesystem
						if ($rewrite_method == 'filesys') {
							$ci->filesystem->mkdir($update_folder);
						} elseif ($rewrite_method == 'ftp') {
							// if rewrite method is ftp
							// get ftp config from session.
							$ftp_config = $ci->session->userdata('ftp_config');
							
							$ci->filesystem->use_ftp = true;
							
							// store config for use in ftp library.
							$ci->filesystem->ftp_hostname = $ftp_config['ftp_host'];
							$ci->filesystem->ftp_username = $ftp_config['ftp_username'];
							$ci->filesystem->ftp_password = $ftp_config['ftp_password'];
							$ci->filesystem->ftp_port = $ftp_config['ftp_port'];
							$ci->filesystem->ftp_passive = $ftp_config['ftp_passive'];
							$ci->filesystem->ftp_basepath = $ftp_config['ftp_basepath'];
							
							$ci->filesystem->mkdir($update_folder);
						}
						
						// clear unused variables
						unset($ftp_config, $rewrite_method);
						
						// get only file name from url
						$url_exp = explode('/', $update->download);
						$file_name = $url_exp[(count($url_exp)-1)];
						unset($url_exp);
						
						// start to download update file.
						$ch = curl_init($update->download);
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
						$file_data = curl_exec($ch);
						curl_close($ch);
						
						$output['result'] = file_put_contents($update_folder.$file_name, $file_data);
						$output['downloaded_file'] = $update_folder.$file_name;
						return $output;
					}
				}
			}
		}
		
		return false;
	}// update_core_download
	
	
	/**
	 * update_core_extract
	 * @param string $downloaded_file
	 * @return mixed
	 */
	public function update_core_extract($downloaded_file = '') 
	{
		if ($downloaded_file == null) {return false;}
		
		// if downloaded file is not exists, or exists but not a file
		if (!file_exists($downloaded_file) || (file_exists($downloaded_file) && !is_file($downloaded_file))) {return false;}
		
		// get CI instance
		$ci =& get_instance();
		
		$ci->load->library('filesystem');
		
		// get update core folder
		$uncompress_folder = $ci->config->item('agni_upload_path').'unzip/agnicms_update_core/uncompressed/';
		
		// get rewrite method
		$rewrite_method = $ci->session->userdata('rewrite_method');
		
		// create uncompressed folder ----------------------------------------------------------------------------
		if ($rewrite_method == 'filesys') {
			$ci->filesystem->mkdir($uncompress_folder);
		} elseif ($rewrite_method == 'ftp') {
			// if rewrite method is ftp
			// get ftp config from session.
			$ftp_config = $ci->session->userdata('ftp_config');

			$ci->filesystem->use_ftp = true;

			// store config for use in ftp library.
			$ci->filesystem->ftp_hostname = $ftp_config['ftp_host'];
			$ci->filesystem->ftp_username = $ftp_config['ftp_username'];
			$ci->filesystem->ftp_password = $ftp_config['ftp_password'];
			$ci->filesystem->ftp_port = $ftp_config['ftp_port'];
			$ci->filesystem->ftp_passive = $ftp_config['ftp_passive'];
			$ci->filesystem->ftp_basepath = $ftp_config['ftp_basepath'];

			$ci->filesystem->mkdir($uncompress_folder);
		}
		// create uncompressed folder ----------------------------------------------------------------------------
		
		unset($ftp_config, $rewrite_method);
		
		// check again if uncompress folder exists
		if (!file_exists($uncompress_folder)) {
			// uncompress folder is not exists. can not continue, return false.
			return false;
		}
		
		// extract downloaded zip file ---------------------------------------------------------------------------------------------------
		// use dUnzip
		// include dUnzip library to unzip.
		//require_once(APPPATH.'/libraries/dunzip/dUnzip2.inc.php');
		
		//$zip = new dUnzip2($downloaded_file);
		//$zip->debug = true;
		//$zip->unzipAll($uncompress_folder);
		//$zip->close();
		
		// use php unzip
		$zip = new ZipArchive();
		$res = $zip->open($downloaded_file);
		if ($res === true) {
			$zip->extractTo($uncompress_folder);
		}
		$zip->close();
		// extract downloaded zip file ---------------------------------------------------------------------------------------------------
		
		// done extract step.
		$output['result'] = true;
		$output['unzip_path'] = $uncompress_folder;
		return $output;
	}// update_core_extract
	
	
	public function update_core_version() 
	{
		// get CI instance
		$ci =& get_instance();
		
		$cfg = $ci->config_model->load(array('agni_auto_update_url', 'agni_version'));
		
		$xml = $this->get_xml($cfg['agni_auto_update_url']['value']);
		
		foreach ($xml as $update) {
			// if module name is agnicms and version > currently use version
			if ($update->moduleName == 'agnicms' && $update->version > $cfg['agni_version']['value']) {
				// if target version is null or target version <= currently use version
				if ($update->targetVersion == '' || ($update->targetVersion != '' && $update->targetVersion <= $cfg['agni_version']['value'])) {
					// if download link is not null and exists.
					if ($update->download != null && $this->url_exists($update->download, array('301', '302'))) {
						$ci->load->model('siteman_model');
						
						// get all sites from sites table
						$list_site = $ci->siteman_model->listWebsitesAll();
						
						if (isset($list_site['items']) && is_array($list_site['items'])) {
							// loop update config in all sites that we are using current version.
							foreach ($list_site['items'] as $row) {
								$site_table_prefix = '';
								
								if ($row->site_id != '1') {
									$site_table_prefix = $row->site_id . '_';
								}
								
								$ci->db->where('config_name', 'agni_version')
										->set('config_value', (string) $update->version)
										->update($ci->db->dbprefix($site_table_prefix . 'config'));
							}
						}

						break;
					}
				}
			}
		}
		
		return true;
	}// update_core_version
	
	
	/**
	 * url_exists
	 * check if url is exists.
	 * @param string $url target url to check
	 * @param array $disallow_status disallow http code
	 * @return boolean
	 */
	public function url_exists($url = '', $disallow_status = array()) 
	{
		// if this server has no curl.
		if (!function_exists('curl_close') || !function_exists('curl_exec') || !function_exists('curl_getinfo') || !function_exists('curl_init') || !function_exists('curl_setopt')) {
			if (function_exists('get_headers')) {
				$get_header = get_headers($url);
				preg_match("#^HTTP\/1\.(0|1)\s(\d+)#i", $get_header[0], $status);
				
				unset($get_header);
				
				if (isset($status[2])) {
					if ($status[2] === false || $status[2] == '0' || $status[2] == '404') {
						return false;
					} else {
						if (is_array($disallow_status) && in_array($status[2], $disallow_status)) {
							return false;
						}
						return true;
					}
				}
			}
			return false;
		}
		
		// curl functions available to use.
		$ch = curl_init($url);
		
		if ($ch === false) {return false;}
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		
		unset($ch);
		
		if ($status === false || $status == '0' || $status == '404') {
			return false;
		} else {
			if (is_array($disallow_status) && in_array($status, $disallow_status)) {
				return false;
			}
		}
		
		return true;
	}// url_exists
	
	
}

