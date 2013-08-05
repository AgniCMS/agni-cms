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

class corebreadcrumb extends widget 
{
	
	
	public $title;
	public $description;
	
	
	public function __construct() 
	{
		$this->lang->load('core/coremd');
		$this->title = $this->lang->line('coremd_breadcrumb_title');
		$this->description = $this->lang->line('coremd_breadcrumb_desc');
	}// __construct
	
	
	public function run() 
	{
		if ((current_url() == site_url()) || (current_url().'/' == site_url('/')) || (current_url() == site_url('/'))) {
			// on home page = not show.
			return ;
		}
		
		$args = func_get_args();
		
		// if there is data send from controller or view.
		if (isset($args[3]) && is_array($args[3])) {
			$output = '<ul class="breadcrumb">';
			foreach ($args[3] as $item) {
				$output .= '<li';
				if (end($args[3]) == $item) {
					$output .= ' class="active end last"';
				}
				$output .= '>';
				
				if (isset($item['text']) && isset($item['url'])) {
					$output .= anchor($item['url'], $item['text']);
				} elseif (isset($item['text'])) {
					$output .= anchor('#', $item['text'], array('onclick' => 'return false;'));
				}
				
				if (end($args[3]) != $item) {
					if (isset($item['divider']) && $item['divier'] != null) {
						$output .= ' <span class="divider">' . $item['divider'] . '</span>';
					} elseif (isset($item['divider']) && $item['divider'] == null) {
						$output .= '';
					} else {
						$output .= ' <span class="divider">&rsaquo;</span>';
					}
				}
				
				$output .= '</li>';
			}
			$output .= '</ul>';
		} else {
			// there is no data send from controller or view, use old breadcrumb style.
			// load cache driver
			$this->load->driver('cache', array('adapter' => 'file'));
			if (false === $output = $this->cache->get('breadcrumb_'.md5(current_url()))) {
				$output = '<ul>';
				// start from Home
				$output .= '<li>'.anchor('', lang('coremd_breadcrumb_home')).'</li>';
				// loop each uri to breadcrumb
				$segs = $this->uri->segment_array();
				foreach ($segs as $segment) {
					// lookup in url alias
					$this->db->join('posts', 'url_alias.uri = posts.post_uri', 'left');
					$this->db->join('taxonomy_term_data', 'url_alias.uri = taxonomy_term_data.t_uri', 'left');
					$this->db->where('uri_encoded', $segment);
					$this->db->where('url_alias.language', $this->lang->get_current_lang());
					$query = $this->db->get('url_alias');
					
					if ($query->num_rows() > 0) {
						$row = $query->row();
						$query->free_result();
						$c_type = $row->c_type;
						if ($c_type == 'category') {
							$output .= '<li>'.anchor($row->t_uris, $row->t_name).'</li>';
						} elseif ($c_type == 'tag') {
							$output .= '<li>'.anchor('tag/'.$row->t_uris, $row->t_name).'</li>';
						} elseif ($c_type == 'article') {
							$output .= '<li>'.anchor($this->uri->uri_string(), $row->post_name).'</li>';
						} elseif ($c_type == 'page') {
							$output .= '<li>'.anchor($this->uri->uri_string(), $row->post_name).'</li>';
						}
					}
					
					// other pages that doesn't in url alias.
					switch($segment) {
						case 'search':
							$this->lang->load('search');
							$output .= '<li>'.anchor('search', lang('search_search')).'</li>';
							break;
						default: 
							break;
					}
				}
				
				$output .= '</ul>';
				
				$this->cache->save('breadcrumb_'.md5(current_url()), $output, 3600);
			}
		}
		
		echo $output;
	}// run
	
	
}
