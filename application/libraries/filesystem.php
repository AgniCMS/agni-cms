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


class filesystem 
{
	
	
	public $ftp_hostname;
	public $ftp_username;
	public $ftp_password;
	public $ftp_port;
	public $ftp_passive;
	public $ftp_debug = false;
	public $ftp_basepath;
	// use ftp or not. (boolean)
	public $use_ftp = false;
	
	
	public function __construct($use_ftp = false) 
	{
		// if use ftp.
		if ($use_ftp == true) {
			$this->use_ftp = true;
			
			$ci =& get_instance();
			
			// get ftp configuration from db.
			$cfg = $ci->config_model->load(array('ftp_host', 'ftp_username', 'ftp_password', 'ftp_port', 'ftp_passive', 'ftp_basepath'));
			
			if (isset($cfg['ftp_host']['value'])) {
				$this->ftp_hostname = $cfg['ftp_host']['value'];
			}
			if (isset($cfg['ftp_username']['value'])) {
				$this->ftp_username = $cfg['ftp_username']['value'];
			}
			if (isset($cfg['ftp_password']['value'])) {
				$this->ftp_password = $cfg['ftp_password']['value'];
			}
			if (isset($cfg['ftp_port']['value'])) {
				$this->ftp_port = $cfg['ftp_port']['value'];
			}
			if (isset($cfg['ftp_passive']['value'])) {
				$this->ftp_passive = $cfg['ftp_passive']['value'];
			}
			if (isset($cfg['ftp_basepath']['value'])) {
				$this->ftp_basepath = $cfg['ftp_basepath']['value'];
			}
			
			// clear variable
			unset($cfg);
		}
	}// __construct
	
	
	public function chmod($path = '', $chmod = '0777') 
	{
		if ($this->use_ftp === false) {
			$old = umask(0);
			$result = chmod($path, $chmod);
			umask($old);
			
			return $result;
		} elseif ($this->use_ftp === true) {
			$config = $this->set_ftp_config();
			
			$ci =& get_instance();
			
			if ($chmod == '0777' || $chmod == '777') {
				$chmod = DIR_WRITE_MODE;
			} elseif ($chmod == '0666' || $chmod == '666') {
				$chmod = FILE_WRITE_MODE;
			} elseif ($chmod == '0644' || $chmod == '644') {
				$chmod = FILE_READ_MODE;
			} else {
				$chmod = DIR_READ_MODE;
			}
			
			$ci->load->library('ftp');
			$ci->ftp->connect($config);
			$result = $ci->ftp->chmod($this->ftp_basepath.$path, $chmod);
			$ci->ftp->close();
			
			unset($config);
			
			return $result;
		}
	}// chmod
	
	
	/**
	 * copy
	 * @param string $source
	 * @param string $destination
	 * @return boolean
	 */
	public function copy($source = '', $destination = '') 
	{
		// if path is not end with slash trail, add to it.
		if (substr($destination, -1) !== '/') {
			$destination .= '/';
		}

		$ci =& get_instance();
		
		if ($this->use_ftp === false) {
			$ci->load->helper('file');
			
			return smartCopy($source, $destination);
		} elseif ($this->use_ftp === true) {
			$config = $this->set_ftp_config();
			
			$ci->load->library('ftp');
			$ci->ftp->connect($config);
			$res = $ci->ftp->mirror($source, $this->ftp_basepath.$destination);
			$ci->ftp->close();
			
			unset($config);
			
			return $res;
		}
	}// copy
	
	
	/**
	 * mkdir
	 * create folder (directory)
	 * @param string $path
	 * @param octal $chmod
	 * @return boolean
	 */
	public function mkdir($path = '', $chmod = '0777') 
	{
		// if path is not end with slash trail, add to it.
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}
		
		// if folder is already exists, do nothing and return true.
		if (file_exists($path) && is_dir($path)) {
			return true;
		}
		
		if ($this->use_ftp === false) {
			// if not use ftp
			return mkdir($path, $chmod);
		} elseif ($this->use_ftp === true) {
			// if use ftp
			$ci =& get_instance();
			$config = $this->set_ftp_config();
			
			if ($chmod == '0777' || $chmod == '777') {
				$chmod = DIR_WRITE_MODE;
			} elseif ($chmod == '0666' || $chmod == '666') {
				$chmod = FILE_WRITE_MODE;
			} elseif ($chmod == '0644' || $chmod == '644') {
				$chmod = FILE_READ_MODE;
			} else {
				$chmod = DIR_READ_MODE;
			}
			
			$ci->load->library('ftp');
			$ci->ftp->connect($config);
			$result = $ci->ftp->mkdir($this->ftp_basepath.$path, $chmod);
			$ci->ftp->close();
			
			// remove unused variables
			unset($config);
			
			return $result;
		}
	}// mkdir
	
	
	/**
	 * rmdir
	 * @param string $path
	 * @return boolean
	 */
	public function rmdir($path = '') 
	{
		// if path is not end with slash trail, add to it.
		if (substr($path, -1) !== '/') {
			$path .= '/';
		}
		
		$ci =& get_instance();
		
		if ($this->use_ftp === false) {
			// if use filesystem 
			$ci->load->helper('file');
			
			delete_files($path, true);
			
			if (file_exists($path) && is_dir($path)) {
				return rmdir($path);
			}
			
			return true;
		} elseif ($this->use_ftp === true) {
			// if use ftp
			$config = $this->set_ftp_config();
			
			$ci->load->library('ftp');
			$ci->ftp->connect($config);
			set_time_limit(10);
			$res = $ci->ftp->delete_dir($path);
			$ci->ftp->close();
			
			// because CI's ftp class has bug in delete_dir that cannot delete all folders and sub folders in it. we will use file system to delete it again.
			$this->use_ftp = false;
			$this->rmdir($path);
			$this->use_ftp = true;
			return true;
		}
	}// rmdir
	
	
	/**
	 * set_ftp_config
	 * set up ftp config array for use in ftp connect method.
	 * @return array
	 */
	private function set_ftp_config() 
	{
		$output['hostname'] = $this->ftp_hostname;
		$output['username'] = $this->ftp_username;
		$output['password'] = $this->ftp_password;
		$output['port'] = $this->ftp_port;
		$output['passive'] = $this->ftp_passive;
		$output['debug'] = $this->ftp_debug;
		
		return $output;
	}// set_ftp_config
	
	
}

