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

function config_load( $cfg_name = '', $return_field = 'config_value' ) {
	$CI =& get_instance();
	$CI->load->model( 'config_model' );
	return $CI->config_model->load_single( $cfg_name, $return_field );
}// config_load


function set_site_table() {
	$CI =& get_instance();
	
	// load model
	$CI->load->model( array( 'siteman_model' ) );
	
	// get domain name.
	$site_domain = $CI->input->server( 'HTTP_HOST' );
	
	// get site info eg site_id.
	$data['site_domain'] = $site_domain;
	$data['site_status'] = '1';
	$site = $CI->siteman_model->get_site_data_db( $data );
	unset( $data );
	
	if ( $site == null ) {
		define( 'SITE_TABLE', '' );
		return false;
	}
	
	define( 'SITE_TABLE', $site->site_id.'_' );
	return true;
}// site_table

