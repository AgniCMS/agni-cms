<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * auto controller works when the system cannot found match controller.
 * this controller will lookup uri and determine if it is category, article, page and call those controller to work.
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */
 
class auto_controller extends MY_Controller 
{
	
	
	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	public function _remap() 
	{
		$this->index();
	}// _remap
	
	
	public function index() 
	{
		$att1 = $this->uri->segment(1);
		
		// set att2 and prevent some of att2 match att1.
		$uri_arr = $this->uri->segment_array();
		$att2 = array();
		foreach ($uri_arr as $item) {
			if ($item != $att1) {
				$att2[] = $item;
			}
		}
		unset($uri_arr);
		
		// get real uri.
		if (empty($att2)) {
			$last_urisegment = $att1;
		} else {
			$last_urisegment = $att2[count($att2)-1];
		}
		
		// lookup in url alias
		$this->db->where('uri_encoded', $last_urisegment);
		$this->db->where('language', $this->lang->get_current_lang());
		$query = $this->db->get('url_alias');
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			$c_type = $row->c_type;
			unset($row);
			
			// select which type is it.
			if ($c_type == 'category') {
				$this->load->module('category');
				return $this->category->index($att1, $att2);
			} elseif ($c_type == 'article') {
				$this->load->module('post');
				return $this->post->view($last_urisegment);
			} elseif ($c_type == 'page') {
				$this->load->module('post');
				return $this->post->view($last_urisegment);
			}
			
			$query->free_result();
			unset($c_type, $query);
		}
		
		// nod found any alias?? check redirection in url_alias.-----------------------------------------------------------------------
		// not found? lookup in url alias as redirect
		$uri_string = $this->uri->uri_string();
		$uri_string = preg_replace('/\/(.*)/', '$1', $uri_string);
		
		// find in db
		$this->db->where('uri_encoded', $uri_string);
		$this->db->where('c_type', 'redirect');
		$query = $this->db->get('url_alias');
		//
		unset($uri_string);
		
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$query->free_result();
			redirect($row->redirect_to_encoded, '', $row->redirect_code);
		}
		
		$query->free_result();
		unset($query);
		// end check redirection in url_alias.-----------------------------------------------------------------------------------------------
		
		// found nothing.
		show_404();
	}// index
	
	
}

// EOF