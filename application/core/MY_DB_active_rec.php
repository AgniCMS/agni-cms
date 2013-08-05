<?php

class MY_DB_active_record extends CI_DB_active_record {
	
	
	/**
	 * sample use
	 * 
		$this->db->where('posts.post_type', $this->post_type);

		$like_data[0]['field'] = 'posts.post_name';
		$like_data[0]['match'] = 'a';
		$like_data[0]['side'] = 'both';
		$like_data[1]['field'] = 'posts.post_uri';
		$like_data[1]['match'] = 'a';
		$like_data[2]['field'] = 'posts.post_uri_encoded';
		$like_data[2]['match'] = 'a';

		$this->db->like_group($like_data);

		unset($like_data);

		$query = $this->db->get('posts');

		echo $this->db->last_query();
	 * 
	 * like_group create like group query eg. AND (field1 LIKE '%keyword%' OR field2 LIKE '%keyword%')
	 * 
	 * @param array $data
	 * @param string $type
	 * @return mixed
	 */
	public function like_group($data = array(), $type = 'AND') {
		if (!is_array($data)) {return null;}
		
		$i = 1;
		
		$sql = '(';
		foreach ($data as $item) {
			
			// check and fix side
			if (!isset($item['side'])) {
				$item['side'] = 'both';
			} else {
				$item['side'] =strtolower(trim($item['side']));
			}
			
			// use OR or not (first loop do not use OR)
			if ($i > 1) {
				$sql .= ' OR ';
			}
			
			// detect table name and add prefix
			if (strpos($item['field'], '.') !== false) {
				$ci =& get_instance();
				$ci->load->model('siteman_model');
				$site_wide_tables = $ci->siteman_model->site_wide_tables;
				
				// get only table name prefix
				$tbname_exp = explode('.', $item['field']);
				$table_name = $tbname_exp[count($tbname_exp)-1];
				
				if (!in_array($table_name, $site_wide_tables)) {
					$sql .= $this->dbprefix(SITE_TABLE.$item['field']);
				} else {
					$sql .= $this->dbprefix($item['field']);
				}
				
				unset($ci, $site_wide_tables, $table_name, $tbname_exp);
			}
			
			if ($item['side'] == 'none') {
				$sql .= ' LIKE \''.$this->escape_like_str($item['match']).'\'';
			} elseif ($item['side'] == 'before') {
				$sql .= ' LIKE \'%'.$this->escape_like_str($item['match']).'\'';
			} elseif ($item['side'] == 'after') {
				$sql .= ' LIKE \''.$this->escape_like_str($item['match']).'%\'';
			} else {
				$sql .= ' LIKE \'%'.$this->escape_like_str($item['match']).'%\'';
			}
			
			$i++;
		}
		$sql .= ')';
		
		// done, send these like query to active record.
		if ($type == 'OR') {
			return $this->or_where($sql);
		} else {
			return $this->where($sql);
		}
	}// like_group
	
	
}
