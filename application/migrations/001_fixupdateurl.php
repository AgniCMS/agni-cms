<?php

/*
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 * 
 */


class Migration_fixupdateurl extends CI_Migration
{
	
	
	public function up() 
	{
		$this->load->model('siteman_model');
		
		$list_site = $this->siteman_model->listWebsitesAll();
		
		if (isset($list_site['items']) && is_array($list_site['items'])) {
			foreach ($list_site['items'] as $row) {
				$site_table_prefix = '';
				
				if ($row->site_id != '1') {
					$site_table_prefix = $row->site_id . '_';
				}
				
				$this->db->where('config_name', 'agni_auto_update_url')
						->set('config_value', 'http://agnicms.org/modules/updateservice/update.xml')
						->update('config');
			}
		}
	}
	
	
}

