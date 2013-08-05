<?php
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class corerecentarticle extends widget 
{
	
	
	public $title;
	public $description;
	
	
	public function __construct() 
	{
		$this->lang->load('core/coremd');
		$this->title = $this->lang->line('coremd_recentarticle_title');
		$this->description = $this->lang->line('coremd_recentarticle_desc');
	}// __construct
	
	
	public function block_show_form($row = '') 
	{
		// this is method for show form edit in admin page.
		$values = unserialize($row->block_values);
		include dirname(__FILE__).'/views/form.php';
	}// block_show_form
	
	
	public function run() 
	{
		// get arguments
		$args = func_get_args();
		$values = (isset($args[1]) ? unserialize($args[1]) : '');
		
		// query articles
		$sql = 'select * from '.$this->db->dbprefix('posts').' as p';
		$sql .= ' left outer join '.$this->db->dbprefix('taxonomy_index').' as ti';
		$sql .= ' on p.post_id = ti.post_id';
		$sql .= ' left join '.$this->db->dbprefix('accounts').' as a';
		$sql .= ' on p.account_id = a.account_id';
		$sql .= ' inner join '.$this->db->dbprefix('post_revision').' as pr';
		$sql .= ' on p.post_id = pr.post_id';
		$sql .= ' where post_type = '.$this->db->escape('article');
		$sql .= ' and language = '.$this->db->escape($this->lang->get_current_lang());
		$sql .= ' and post_status = 1';
		$sql .= ' group by p.post_id';
		// order and sort
		$sql .= ' ORDER BY post_add DESC';
		$sql .= ' limit 0, '.(isset($values['recent_num']) && is_numeric($values['recent_num']) ? $values['recent_num'] : 5);
		$query = $this->db->query($sql);
		
		if ($query->num_rows() > 0) {
			$result = $query->result();
		}
		
		$query->free_result();
		
		include dirname(__FILE__).'/views/display.php';
		
		unset($args, $values, $sql, $query, $result);
	}// run
	
	
}
