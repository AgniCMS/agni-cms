<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class MY_Input extends CI_Input {
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * ip_address
	 * @return string 
	 */
	function  ip_address() {
		if ($this->ip_address !== false) {
			return $this->ip_address;
		}
		
		// IMPROVED!! CI ip address cannot detect through http_x_forwarded_for. this one can do.
		if (isset($_SERVER['HTTP_CLIENT_IP'])) {
			// //check ip from share internet
			$this->ip_address = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			//to check ip is pass from proxy
			$this->ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$this->ip_address = $_SERVER['REMOTE_ADDR'];
		}
		
		//
		if ($this->ip_address === false) {
			$this->ip_address = "0.0.0.0";
			return $this->ip_address;
		}
		
		//
		if (strpos($this->ip_address, ',') !== FALSE)
		{
			$x = explode(',', $this->ip_address);
			$this->ip_address = trim(end($x));
		}
		//
		if (! $this->valid_ip($this->ip_address)){
			$this->ip_address = '0.0.0.0';
		}
		//
		return $this->ip_address;
	}
	
	
}
