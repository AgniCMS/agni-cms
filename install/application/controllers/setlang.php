<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class setlang extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function index() 
	{
		$lang = trim($this->input->get('lang'));
		$lang_arr = $this->config->item('lang_uri_abbr');
		if (isset($lang_arr[$lang])) {
			// selected language is in array
			// set language cookie
			$this->load->helper('cookie');
			set_cookie('agni_install_lang', $lang, 86400);// 24hrs = 86400seconds
		}
		// go back
		$this->load->library('user_agent');
		if ($this->agent->is_referral() && $this->agent->referrer() != current_url()) {
			redirect($this->agent->referrer());
		} else {
			redirect('./');
		}
	}// index
	
	
}

// EOF