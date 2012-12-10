<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class logout extends MY_Controller {

	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function index() {
		$this->account_model->logout();
		
		// go back
		$this->load->library( 'user_agent' );
		if ( $this->agent->is_referral() && $this->agent->referrer() != current_url() ) {
			redirect( $this->agent->referrer() );
		} else {
			redirect( site_url() );
		}
	}// index
	

}

