<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 */

class MY_Form_validation extends CI_Form_validation {
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	function preg_match_date($str = '') {
		$this->CI =& get_instance();
		if (!$this->regex_match($str, "/(17|18|19|20|21)\d\d[-](0[1-9]|1[012])[-](0[1-9]|[12][0-9]|3[01])/")) {
			$this->set_message("preg_match_date", $this->CI->lang->line("regex_match"));
			return false;
		}
		return true;
	}// preg_match_date
	
	
}
