<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 * html model
 *
 */

class html_model extends CI_Model 
{

	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * gen_front_body_class
	 * @return string 
	 */
	public function gen_front_body_class($class = '') 
	{
		$class = ' '.$class;
		
		// gen front class
		if (current_url() == base_url() || current_url() == site_url()) {$class .= ' home';}
		
		// gen logged in class
		$cm_cookie = $this->account_model->getAccountCookie('member');
		if (!isset($cm_cookie['id']) || !isset($cm_cookie['username']) || !isset($cm_cookie['password']) || !isset($cm_cookie['onlinecode'])) {
			$class .= ' not-logged-in';
		} elseif (isset($cm_cookie['id'])) {
			$class .= ' logged-in';
		}
		
		$class .= ' '.str_replace('/', '_', ltrim(urldecode($this->uri->uri_string()), '/'));
		
		// plugins here
		$class .= ' '.$this->modules_plug->do_filter('front_html_body_class');
		
		return rtrim($class);
	}// gen_front_body_class
	
	
	/**
	 * generate tags into string.
	 * @param array $tags
	 * @return string 
	 */
	public function gen_tags($tags = array()) 
	{
		if (!is_array($tags) || empty($tags)) {return null;}
		
		$output = '';
		foreach ($tags as $tag) {
			$output .= $tag."\n";
		}
		
		return $output;
	}// gen_tags
	
	
	/**
	 * generate text for <title>...</title>
	 * @param string $title
	 * @return string 
	 */
	public function gen_title($title = '') 
	{
		$cfg = $this->config_model->load(array('site_name', 'page_title_separator'));
		
		if (!empty($cfg)) {
			if ($title != null) {
				$title = $cfg['site_name']['value'] . $cfg['page_title_separator']['value'] . $title;
			} else {
				// no $title set, return only site name
				$title = $cfg['site_name']['value'];
			}
			
			$title = $this->modules_plug->do_filter('html_title', $title, $cfg);
			
			return $title;
		}
		
		return $title;
	}// gen_title
	

}

