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

class queue_model extends CI_Model 
{


	public function __construct() 
	{
		parent::__construct();
	}// __construct
	
	
	/**
	 * add queue
	 * @param array $data
	 * @return boolean
	 */
	public function addQueue($data = array()) 
	{
		$this->db->insert('queue', $data);
		
		// get inserted id
		$output['queue_id'] = $this->db->insert_id();
		
		$output['result'] = true;
		return $output;
	}// addQueue
	
	
	/**
	 * delete queue
	 * @param array $data
	 * @return boolean
	 */
	public function deleteQueue($data = array()) 
	{
		if (!is_array($data) || (is_array($data) && empty($data))) {return false;}
		
		$this->db->where($data)
			   ->delete('queue');
		
		return true;
	}// deleteQueue
	
	
	/**
	 * edit queue
	 * @param array $data
	 * @return boolean
	 */
	public function editQueue($data = array()) 
	{
		$this->db->where('queue_id', $data['queue_id'])
			   ->update('queue', $data);
		
		$output['queue_id'] = $data['queue_id'];
		$output['result'] = true;
		return $output;
	}// editQueue
	
	
	/**
	 * get queue data
	 * @param array|string $data
	 * @return mixed
	 */
	public function getQueueData($data = array()) 
	{
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		} elseif (is_string($data)) {
			$this->db->where($data);
		}
		
		$query = $this->db->get('queue');
		
		return $query->row();
	}// getQueueData
	
	
	/**
	 * is queue exists
	 * @param string $queue_name
	 * @return boolean
	 */
	public function isQueueExists($queue_name = '') 
	{
		$this->db->where('queue_name', $queue_name);
		
		if ($this->db->count_all_results('queue') >= 1) {
			return true;
		}
		
		return false;
	}// isQueueExists


}
