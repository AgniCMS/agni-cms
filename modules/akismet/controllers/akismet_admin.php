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
 
class akismet_admin extends MX_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
		
		// load helper
		$this->load->helper(array('url'));
		
		// load language
		$this->lang->load('akismet/akismet');
	}// __construct
	
	
	public function _define_permission() 
	{
		return array('akismet_perm' => array('akismet_config_perm', 'akismet_set_spam_perm', 'akismet_set_notspam_perm'));
	}// _define_permission
	
	
	public function admin_nav() 
	{
		return '<li>' . anchor('#', lang('akismet_akismet'), array('onclick' => 'return false')) . '
				<ul>
					<li>' . anchor('akismet/site-admin/akismet/config', lang('akismet_config')) . '</li>
				</ul>
			</li>';
	}// admin_nav
	
	
}

// EOF