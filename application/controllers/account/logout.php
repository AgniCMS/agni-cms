<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class logout extends MY_Controller 
{

	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function index() 
	{
		$this->account_model->logOut();
		
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
			redirect($this->agent->referrer());
		} else {
			redirect(site_url());
		}
	}// index
	

}

