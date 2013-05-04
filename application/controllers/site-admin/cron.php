<?php if ( ! defined('BASEPATH') ) exit('No direct script access allowed');
/** 
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 */

class cron extends admin_controller {


	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function index() {
		// system log
		$log['sl_type'] = 'cron';
		$log['sl_message'] = 'Run cron';
		$this->load->model( 'syslog_model' );
		$this->syslog_model->add_new_log( $log );
		unset( $log );
		
		
	}// index


}
