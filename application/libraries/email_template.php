<?php
/**
 * @author mr.v
 * @copyright http://okvee.net
 */

class email_template 
{
	
	
	public function __construct() 
	{
		
	}// __construct
	
	public function read_template($email_file = '') 
	{
		if ($email_file == null) {return null;}
		
		$CI =& get_instance();
		$template_path = APPPATH.'language/'.$CI->config->item('language')."/";
		
		if (file_exists($template_path.$email_file)) {
			$site_name = $CI->config_model->loadSingle('site_name');
			//
			$output = file_get_contents($template_path.$email_file);
			$output = str_replace("%sitename%", $site_name, $output);
			$output = str_replace("%siteurl%", base_url(), $output);
			
			unset($site_name, $template_path, $CI);
			return $output;
		} else {
			return false;
		}
	}
}