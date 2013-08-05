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

class queue_model extends CI_Model {


	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/**
	 * add_queue
	 * @param array $data
	 * @return boolean
	 */
	function add_queue($data = array()) {
		$this->db->insert('queue', $data);
		
		// get inserted id
		$output['queue_id'] = $this->db->insert_id();
		
		$output['result'] = true;
		return $output;
	}// add_queue
	
	
	/**
	 * delete_queue
	 * @param array $data
	 * @return boolean
	 */
	function delete_queue($data = array()) {
		if (!is_array($data) || (is_array($data) && empty($data))) {return false;}
		
		$this->db->where($data)
			   ->delete('queue');
		
		return true;
	}// delete_queue
	
	
	/**
	 * edit_queue
	 * @param array $data
	 * @return boolean
	 */
	function edit_queue($data = array()) {
		$this->db->where('queue_id', $data['queue_id'])
			   ->update('queue', $data);
		
		$output['queue_id'] = $data['queue_id'];
		$output['result'] = true;
		return $output;
	}// edit_queue
	
	
	/**
	 * get_queue_data
	 * @param array|string $data
	 * @return mixed
	 */
	function get_queue_data($data = array()) {
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		} elseif (is_string($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('queue');
		
		return $query->row();
	}// get_queue_data
	
	
	/**
	 * is_queue_exists
	 * @param string $queue_name
	 * @return boolean
	 */
	function is_queue_exists($queue_name = '') {
		$this->db->where('queue_name', $queue_name);
		
		if ($this->db->count_all_results('queue') >= 1) {
			return true;
		}
		
		return false;
	}// is_queue_exists


}
