<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

/**
 * convert gmt time to local with date format
 * @param string $dateformat
 * @param string $time time must be gmt
 * @return string 
 */
function gmt_date($dateformat = 'Y-m-d H:i:s', $time = '', $gmt = '') {
	if (empty($dateformat)) {$dateformat = 'Y-m-d H:i:s';}
	
	if (empty($time)) {
		return null;
	} elseif (! isValidTimeStamp($time)) {
		// convert datetime to timestamp
		$time = strtotime($time);
	}
	
	// get instance
	$CI =& get_instance();
	if ($gmt == null) {
		// get gmt from global config
		$CI->load->model('config_model');
		$global_gmt = $CI->config_model->loadSingle('site_timezone');
		
		// load account_model
		$CI->load->model('account_model');
		
		// get account cookie
		$cm = $CI->account_model->getAccountCookie('member');
		
		if (! isset($cm['id'])) {
			// not member, not login get gmt value from config
			$gmt = $global_gmt;
		} else {
			if ($gmt == null) {
				$gmt = $CI->account_model->show_accounts_info($cm['id'], 'account_id', 'account_timezone');
				
				// still cannot got gmt?
				if ($gmt == null) {
					// set gmt from global config
					$gmt = $global_gmt;
				}
			}
		}
	}
	
	return date($dateformat, gmt_to_local($time, $gmt));
}// gmt_date


/**
 * gmtdate
 * alias of gmt_date
 */
function gmtdate($dateformat = '', $time = '') {
	return gmt_date($dateformat, $time);
}// gmtdate


/**
 * is valid timestamp
 * @author Gordon
 * @link http://stackoverflow.com/questions/2524680/check-whether-the-string-is-a-unix-timestamp
 * @param integer $timestamp
 * @return boolean 
 */
function isValidTimeStamp($timestamp) {
	return ((string) (int) $timestamp === $timestamp)
		  && ($timestamp <= PHP_INT_MAX)
		  && ($timestamp >= ~PHP_INT_MAX);
}
