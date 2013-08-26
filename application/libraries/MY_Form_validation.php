<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 */

class MY_Form_validation extends CI_Form_validation 
{
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * no space between text.
	 * @param string $string
	 * @return boolean
	 */
	public function no_space_between_text($string = '') 
	{
		$ci =& get_instance();

		// if not found space.
		if (preg_match('/\s/', $string) == false) {
			// not found space, return true.
			return true;
		} else {
			// found space, return false.
			$ci->form_validation->set_message('no_space_between_text', $ci->lang->line('account_invalid_space_between_text'));
			return false;
		}
	}// no_space_between_text
	
	
	/**
	 * check date value.
	 * @param string $str
	 * @return boolean
	 */
	public function preg_match_date($str = '') 
	{
		$this->CI =& get_instance();
		if (!$this->regex_match($str, "/(17|18|19|20|21)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])/")) {
			$this->set_message("preg_match_date", $this->CI->lang->line("regex_match"));
			return false;
		}
		return true;
	}// preg_match_date
	
	
}
