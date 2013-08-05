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

function check_admin_permission($page = '', $action = '', $account_id = '') {
	$CI =& get_instance();
	$CI->load->model('account_model');
	return $CI->account_model->check_admin_permission($page, $action, $account_id);
}// check_admin_permission


function show_accounts_info($check_value = '', $check_field = 'account_id', $return_field = 'account_username') {
	$CI =& get_instance();
	$CI->load->model('account_model');
	return $CI->account_model->show_accounts_info($check_value, $check_field, $return_field);
}// show_accounts_info
