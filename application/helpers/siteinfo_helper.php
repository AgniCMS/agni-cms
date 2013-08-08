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

function config_load($cfg_name = '', $return_field = 'config_value') {
	$CI =& get_instance();
	$CI->load->model('config_model');
	return $CI->config_model->loadSingle($cfg_name, $return_field);
}// config_load


/**
 * set_site_id
 * set site id constant
 * @return null
 */
function set_site_id() {
	if (!defined('SITE_ID')) {
		$CI =& get_instance();

		// load model
		$CI->load->model(array('siteman_model'));

		// get domain name.
		$site_domain = $CI->input->server('HTTP_HOST');

		// get site info eg site_id.
		$data['site_domain'] = $site_domain;
		$data['site_status'] = '1';
		$site = $CI->siteman_model->getSiteDataDb($data);
		unset($data);

		if ($site == null) {
			define('SITE_ID', '1');
			return null;
		}

		define('SITE_ID', $site->site_id);
		return null;
	}
	
	return null;
}// set_site_id


/**
 * set_site_table
 * set site table prefix constant.
 * @return boolean
 */
function set_site_table() {
	if (!defined('SITE_TABLE')) {
		$CI =& get_instance();

		// load model
		$CI->load->model(array('siteman_model'));

		// get domain name.
		$site_domain = $CI->input->server('HTTP_HOST');

		// get site info eg site_id.
		$data['site_domain'] = $site_domain;
		$data['site_status'] = '1';
		$site = $CI->siteman_model->getSiteDataDb($data);
		unset($data);

		// if no site data in db or site_id = 1
		if ($site == null || ($site != null && $site->site_id == '1' && $site->site_status == '1')) {
			define('SITE_TABLE', '');
			return false;
		}

		define('SITE_TABLE', $site->site_id.'_');
		return true;
	}
	
	return true;
}// set_site_table

