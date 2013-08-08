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

class themes_model extends CI_Model 
{
	
	
	private $theme_dir;
	public $theme_system_name;
	
	
	public function __construct() 
	{
		parent::__construct();
		
		$this->_setupThemeDir();
	}// __construct
	
	
	/**
	 * setup theme directory
	 */
	private function _setupThemeDir() 
	{
		$this->config->load('agni');
		$this->theme_dir = $this->config->item('agni_theme_path');
	}// _setupThemeDir
	
	
	/**
	 * add theme
	 * @return mixed 
	 */
	public function addTheme() 
	{
		// load agni config
		$this->config->load('agni');
		
		// system log
		$log['sl_type'] = 'theme';
		$log['sl_message'] = 'Add new theme';
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		// config upload
		$config['upload_path'] = $this->config->item('agni_upload_path').'unzip';
		$config['allowed_types'] = 'zip';
		$config['encrypt_name'] = true;
		$this->load->library('upload', $config);
		
		if (! $this->upload->do_upload('theme_file')) {
			return $this->upload->display_errors('<div>', '</div>');
		} else {
			$data = $this->upload->data();
		}
		
		// trying to extract ZIP
		if (isset($data) && is_array($data) && !empty($data)) {
			require_once APPPATH.'/libraries/dunzip/dUnzip2.inc.php';
			
			$zip = new dUnzip2($data['full_path']);
			$zip->debug = false;
			
			// check inside zip that it is module directory.
			$list = $zip->getList();
			$valid_theme = true;
			
			$i = 1;
			foreach ($list as $file_name => $zipped) {
				if ($i == 1) {
					$theme_dir = str_replace('/', '', $file_name);
				}
				if ($i == 1 && ($zipped['compression_method'] != '0' || $zipped['compressed_size'] != '0' || $zipped['uncompressed_size'] != '0')) {
					// first item is not directory.
					$valid_theme = false;
				}
				$i++;
				if ($i >= 2) {continue;}
			}
			
			// theme required file is not exists = invalid theme file (theme_name/theme.info)
			if (!isset($list[$theme_dir.'/'.$theme_dir.'.info'])) {
				$valid_theme = false;
			}
			
			// valid theme or not
			if ($valid_theme == false) {
				$zip->__destroy();
				
				if (file_exists($data['full_path'])) {
					unlink($data['full_path']);
				}
				
				unset($i, $valid_theme, $list, $zip);
				
				return $this->lang->line('themes_wrong_structure');
			} else {
				// unzip
				$zip->unzipall($config['upload_path']);
				$zip->__destroy();
				
				// remove zip file
				if (file_exists($data['full_path'])) {
					unlink($data['full_path']);
				}
				
				// move to theme dir
				$this->load->helper('file');
				smartCopy(dirname(BASEPATH).'/'.$config['upload_path'].'/'.$theme_dir, dirname(BASEPATH).'/'.$this->theme_dir.$theme_dir);
				
				// delete everything in theme upload/
				delete_files($config['upload_path'].'/'.$theme_dir, true);
				
				// delete theme/
				rmdir($config['upload_path'].'/'.$theme_dir);
				
				return true;
			}
		}
	}// addTheme
	
	
	/**
	 * delete theme
	 * @param string $theme_system_name
	 * @return boolean
	 */
	public function deleteTheme($theme_system_name = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		// check if theme is default in admin or front
		if ($this->isDefaultOneTheme($theme_system_name) || $this->isDefaultOneTheme($theme_system_name, 'admin')) {
			return $this->lang->line('themes_delete_fail_enabled');
		}
		
		// check if enabled
		if ($this->isEnabledOneTheme($theme_system_name)) {
			return $this->lang->line('themes_delete_fail_enabled');
		}
		
		// change theme that used by posts to default.
		$this->db->set('theme_system_name', null);
		$this->db->where('theme_system_name', $theme_system_name);
		$this->db->update('posts');
		
		// change theme that used by taxterm to default.
		$this->db->set('theme_system_name', null);
		$this->db->where('theme_system_name', $theme_system_name);
		$this->db->update('taxonomy_term_data');
		
		// delete from blocks db
		$this->db->where('theme_system_name', $theme_system_name);
		$this->db->delete('blocks');
		
		// delete from theme_sites
		// get theme_id
		$theme_db = $this->getThemesData(array('theme_system_name' => $theme_system_name));
		if ($theme_db != null) {
			$this->db->where('theme_id', $theme_db->theme_id);
			$this->db->delete('theme_sites');
		}
		
		// delete from db.
		$this->db->where('theme_system_name', $theme_system_name);
		$this->db->delete('themes');
		
		// may delete theme_system_name from other table if there is.
		// delete theme file
		$this->load->helper('file');
		delete_files($this->theme_dir.$theme_system_name, true);
		rmdir($this->theme_dir.$theme_system_name);
		
		// delete cache
		$this->config_model->deleteCache('themedefault_');
		$this->config_model->deleteCache('isthemeenable_');
		
		// system log
		$log['sl_type'] = 'theme';
		$log['sl_message'] = 'Delete theme '.$theme_system_name;
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		return true;
	}// deleteTheme
	
	
	/**
	 * disable theme
	 * @param string $theme_system_name
	 * @return boolean 
	 */
	public function doDisableTheme($theme_system_name = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		// check if theme is default in admin or front
		if ($this->isDefaultTheme($theme_system_name) || $this->isDefaultTheme($theme_system_name, 'admin')) {
			return false;
		}
		
		// get site_id
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		// get theme db
		$theme_db = $this->getThemesData(array('theme_system_name' => $theme_system_name));
		$theme_id = '';
		if ($theme_db != null) {
			$theme_id = $theme_db->theme_id;
		}
		unset($theme_db);
		
		$this->db->where('theme_id', $theme_id);
		$this->db->where('site_id', $site_id);
		$this->db->set('theme_enable', '0');
		$this->db->update('theme_sites');
		
		// delete cache
		$this->config_model->deleteCache('themedefault_');
		$this->config_model->deleteCache('isthemeenable_');
		
		// system log
		$log['sl_type'] = 'theme';
		$log['sl_message'] = 'Disable theme '.$theme_system_name;
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		return true;
	}// doDisableTheme
	
	
	/**
	 * enable theme
	 * @param string $theme_system_name
	 * @return boolean 
	 */
	public function doEnableTheme($theme_system_name = '', $site_id = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		// check if there is front folder or site-admin folder for this theme
		if (!file_exists($this->theme_dir.$theme_system_name.'/site-admin') && !file_exists($this->theme_dir.$theme_system_name.'/front')) {
			return false;
		}
		
		if ($site_id == null) {
			// get site_id
			$this->load->model('siteman_model');
			$site_id = $this->siteman_model->getSiteId();
		}
		
		// check if is in db?
		$this->db->where('theme_system_name', $theme_system_name);
		if ($this->db->count_all_results('themes') <= 0) {
			// not in db, use insert.
			$pdata = $this->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
			
			// check if enabled
			if ($this->isEnabledTheme($theme_system_name)) {
				return true;
			}
			
			$this->db->trans_start();
			$this->db->set('theme_system_name', $theme_system_name);
			$this->db->set('theme_name', (empty($pdata['name']) ? $theme_system_name : $pdata['name']));
			$this->db->set('theme_url', (!empty($pdata['url']) ? $pdata['url'] : null));
			$this->db->set('theme_version', (!empty($pdata['version']) ? $pdata['version'] : null));
			$this->db->set('theme_description', (!empty($pdata['description']) ? $pdata['description'] : null));
			$this->db->insert('themes');
			$this->db->trans_complete();
			
			// check transaction
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				return false;
			}
		} else {
			// get theme data from theme name.info
			$pdata = $this->readThemeMetadata($theme_system_name.'/'.$theme_system_name.'.info');
			
			// in db, use update
			$this->db->trans_start();
			$this->db->where('theme_system_name', $theme_system_name);
			$this->db->set('theme_name', (empty($pdata['name']) ? $theme_system_name : $pdata['name']));
			$this->db->set('theme_url', (!empty($pdata['url']) ? $pdata['url'] : null));
			$this->db->set('theme_version', (!empty($pdata['version']) ? $pdata['version'] : null));
			$this->db->set('theme_description', (!empty($pdata['description']) ? $pdata['description'] : null));
			$this->db->update('themes');
			$this->db->trans_complete();
			
			// check transaction
			if ($this->db->trans_status() === false) {
				$this->db->trans_rollback();
				return false;
			}
		}
		
		// set enable in theme_sties table --------------------------------------------------------------------------------------------
		$theme_db = $this->getThemesData(array('theme_system_name' => $theme_system_name));
		
		if ($theme_db != null) {
			// check if theme is inserted in theme_sites table
			$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
			$this->db->where('theme_system_name', $theme_system_name);
			$this->db->where('site_id', $site_id);
			
			if ($this->db->count_all_results('themes') <= 0) {
				// not in table, insert
				$data['theme_id'] = $theme_db->theme_id;
				$data['site_id'] = $site_id;
				$data['theme_enable'] = '1';
				$this->db->insert('theme_sites', $data);
				unset($data);
			} else {
				// use update
				$data['theme_enable'] = '1';
				$this->db->where('theme_id', $theme_db->theme_id);
				$this->db->where('site_id', $site_id);
				$this->db->update('theme_sites', $data);
				unset($data);
			}
		}
		
		unset($theme_db);
		// set enable in theme_sties table --------------------------------------------------------------------------------------------
		
		// delete cache
		$this->config_model->deleteCache('themedefault_');
		$this->config_model->deleteCache('isthemeenable_');
		
		// system log
		$log['sl_type'] = 'theme';
		$log['sl_message'] = 'Enable theme '.$theme_system_name;
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		return true;
	}// doEnableTheme
	
	
	/**
	 * get default theme
	 * @param admin|front $check_for
	 * @param string $return
	 * @return string 
	 */
	public function getDefaultTheme($check_for = 'front', $return = 'theme_system_name') 
	{
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// get site_id
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		// check cached
		if (false === $theme_val = $this->cache->get('themedefault_'.$site_id.'_'.$check_for.$return)) {
			$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
			$this->db->where('site_id', $site_id);
			if ($check_for == 'admin') {
				$this->db->where('theme_sites.theme_default_admin', '1');
			} else {
				$this->db->where('theme_sites.theme_default', '1');
			}
			
			$query = $this->db->get('themes');
			if ($query->num_rows() <= 0) {
				$query->free_result();
				return null;
			}
			$row = $query->row();
			$query->free_result();
			unset($query);
			
			$this->cache->save('themedefault_'.$site_id.'_'.$check_for.$return, $row->$return, 2678400);
			
			return $row->$return;
		}
		
		return $theme_val;
	}// getDefaultTheme
	
	
	/**
	 * get theme data from db
	 * @param array $data
	 * @return mixed
	 */
	public function getThemesData($data = array()) 
	{
		if (is_array($data) && !empty($data)) {
			$this->db->where($data);
		}
		$query = $this->db->get('themes');
		
		return $query->row();
	}// getThemesData
	
	
	/**
	 * is default one theme
	 * check this theme is default atleast one site.
	 * @param string $theme_system_name
	 * @param admin|front $check_for
	 * @return boolean 
	 */
	public function isDefaultOneTheme($theme_system_name = '', $check_for = 'front') 
	{
		if ($theme_system_name == null) {return false;}
		
		$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
		$this->db->where('theme_system_name', $theme_system_name);
		
		if ($check_for == 'admin') {
			$this->db->where('theme_sites.theme_default_admin', '1');
		} else {
			$this->db->where('theme_sites.theme_default', '1');
		}
		
		if ($this->db->count_all_results('themes')) {
			return true;
		}
		
		return false;
	}// isDefaultOneTheme
	
	
	/**
	 * is default theme
	 * @param string $theme_system_name
	 * @param admin|front $check_for
	 * @return boolean 
	 */
	public function isDefaultTheme($theme_system_name = '', $check_for = 'front') 
	{
		if ($theme_system_name == null) {return false;}
		
		// get site id
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
		$this->db->where('site_id', $site_id);
		$this->db->where('theme_system_name', $theme_system_name);
		
		if ($check_for == 'admin') {
			$this->db->where('theme_sites.theme_default_admin', '1');
		} else {
			$this->db->where('theme_sites.theme_default', '1');
		}
		
		if ($this->db->count_all_results('themes')) {
			return true;
		}
		
		return false;
	}// isDefaultTheme
	
	
	/**
	 * is enabled one theme
	 * check this theme is enabled atleast one site
	 * @param string $theme_system_name
	 * @return boolean 
	 */
	public function isEnabledOneTheme($theme_system_name = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
		$this->db->where('theme_system_name', $theme_system_name);
		$this->db->where('theme_sites.theme_enable', '1');
		
		if ($this->db->count_all_results('themes')) {
			return true;
		}
		
		return false;
	}// isEnabledOneTheme
	
	
	/**
	 * is enabled theme
	 * @param string $theme_system_name
	 * @return boolean 
	 */
	public function isEnabledTheme($theme_system_name = '', $site_id = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		if ($site_id == null) {
			// get site id
			$this->load->model('siteman_model');
			$site_id = $this->siteman_model->getSiteId();
		}
		
		// load cache driver
		$this->load->driver('cache', array('adapter' => 'file'));
		
		// check cached
		if (false === $theme_val = $this->cache->get('isthemeenable_'.$theme_system_name.'_'.$site_id)) {
			$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
			$this->db->where('theme_system_name', $theme_system_name);
			$this->db->where('site_id', $site_id);
			$this->db->where('theme_sites.theme_enable', '1');
			if ($this->db->count_all_results('themes')) {
				$this->cache->save('isthemeenable_'.$theme_system_name.'_'.$site_id, 'true', 2678400);// 31 days (ควรจะนานยิ่งนานยิ่งดีเพราะมันถูกเรียกจาก loop ซึ่งถ้าไม่นานมันจะทำงานหนักมากเป็นช่วงๆ)
				return true;
			}
			$this->cache->save('isthemeenable_'.$theme_system_name.'_'.$site_id, 'false', 2678400);
			return false;
		}
		
		// return cache
		if ($theme_val == 'true') {
			return true;
		} else {
			return false;
		}
	}// isEnabledTheme
	
	
	/**
	 * list all themes
	 * @return mixed 
	 */
	public function listAllThemes() 
	{
		$dir = $this->scanThemeDir();
		
		$output['items'] = $dir;
		
		return $output;
	}// listAllThemes
	
	
	/**
	 * list theme's areas
	 * @param string $theme_system_name
	 * @return array
	 */
	public function listAreas($theme_system_name = '') 
	{
		// load helper
		$this->load->helper('file');
		
		// get theme info.
		$p_data = read_file($this->theme_dir.$theme_system_name.'/'.$theme_system_name.'.info');
		preg_match_all('|areas\[(?P<area_system_name>.*)\] = (?P<area_name>.*)$|mi', $p_data, $matches);
		
		// reformat array
		$areas = array();
		
		if (is_array($matches)) {
			foreach ($matches['area_system_name'] as $key => $item) {
				$areas[$key]['area_system_name'] = $item;
				$areas[$key]['area_name'] = $matches['area_name'][$key];
			}
		}
		
		unset($matches);
		
		return $areas;
	}// listAreas
	
	
	/**
	 * list enabled themes
	 * @return mixed 
	 */
	public function listEnabledThemes() 
	{
		// get site id
		$this->load->model('siteman_model');
		$site_id = $this->siteman_model->getSiteId();
		
		$this->db->join('theme_sites', 'theme_sites.theme_id = themes.theme_id', 'inner');
		$this->db->where('site_id', $site_id);
		$this->db->where('theme_sites.theme_enable', '1');
		$this->db->order_by('theme_name', 'asc');
		
		$query = $this->db->get('themes');
		
		if ($query->num_rows() > 0) {
			$output['total'] = $query->num_rows();
			$output['items'] = $query->result();
			$query->free_result();
			return $output;
		}
		
		$query->free_result();
		
		return null;
	}// listEnabledThemes
	
	
	/**
	 * list theme used in any sites.
	 * @param string $theme_system_name
	 * @return mixed
	 */
	public function listThemeUseInSites($theme_system_name = '') 
	{
		// get theme_id
		$theme_db = $this->getThemesData(array('theme_system_name' => $theme_system_name));
		if ($theme_db == null) {
			return null;
		}
		$theme_id = $theme_db->theme_id;
		unset($theme_db);
		
		// list theme enabled in any sites
		$this->db->join('sites', 'sites.site_id = theme_sites.site_id', 'left')
			   ->where('theme_sites.theme_id', $theme_id)
			   ->where('theme_enable', '1');
		$query = $this->db->get('theme_sites');
		
		$output['total'] = $query->num_rows();
		$output['items'] = $query->result();
		
		$query->free_result();
		unset($query);
		return $output;
	}// listThemeUseInSites
	
	
	/**
	 * read theme metadata
	 * @param string $theme_item
	 * @return mixed 
	 */
	public function readThemeMetadata($theme_item = '') 
	{
		if (empty($theme_item)) {return null;}
		
		// load helper
		$this->load->helper('file');
		
		// get theme info.
		$p_data = read_file($this->theme_dir.$theme_item);
		preg_match ('|Theme Name:(.*)$|mi', $p_data, $name);
		preg_match ('|Theme URL:(.*)$|mi', $p_data, $url);
		preg_match ('|Version:(.*)|i', $p_data, $version);
		preg_match ('|Description:(.*)$|mi', $p_data, $description);
		
		$output['name'] = (isset($name[1]) ? trim($name[1]) : '');
		$output['url'] = (isset($url[1]) ? trim($url[1]) : '');
		$output['version'] = (isset($version[1]) ? trim($version[1]) : '');
		$output['description'] = (isset($description[1]) ? trim($description[1]) : '');
		unset($p_data, $name, $url, $version, $description, $author_name, $author_url);
		
		return $output;
	}// readThemeMetadata
	
	
	/**
	 * alias of method readThemeMetadata.
	 */
	public function read_theme_metadata($theme_item = '') 
	{
		return $this->readThemeMetadata($theme_item);
	}// read_theme_metadata
	
	
	/**
	 * render area
	 * @param string $area_name
	 * @param mixed $attributes send values as attirbutes from controller, view
	 * @return string 
	 */
	public function renderArea($area_name = '', $attributes = '') 
	{
		// load widget class
		$this->load->helper('widget');
		
		// query blocks
		$this->db->where('theme_system_name', $this->theme_system_name);
		$this->db->where('area_name', $area_name);
		$this->db->where('language', $this->lang->get_current_lang());
		$this->db->where('block_status', '1');
		$this->db->order_by('position', 'asc');
		
		$query = $this->db->get('blocks');
		
		if ($query->num_rows() > 0 && strpos(current_url(), site_url('area/demo')) === false) {
			// list blocks in area and NOT in area demo
			$current_uri = urldecode(substr($this->uri->uri_string(), 1));
			
			// loop to cut out the blocks that are in except uri------------------------------------
			$results = $query->result();
			$i = 0;
			foreach ($results as $row) {
				$block_except_uri = explode("\n", $row->block_except_uri);
				
				if (strpos($row->block_except_uri, '*') !== false && is_array($block_except_uri)) {
					// remove exception uri /* eg. book/* including book, book/magazine, book/comic/action
					$unset = false;
					
					foreach ($block_except_uri as $uri) {
						$uri = str_replace(array('/*', '*'), '', $uri);
						if (strpos($current_uri, $uri) !== false) {
							$unset = true;
						}
					}
					
					if ($unset === true) {
						unset($results[$i], $unset, $uri);
					}
				} else {
					if (($row->block_except_uri != null && in_array($current_uri, $block_except_uri))) {
						unset($results[$i]);
					}
				}
				
				unset($row, $block_except_uri);
				$i++;
			}
			// end cut except uri---------------------------------------------------------------------
			
			// loop cut to show only uri--------------------------------------------------------------
			$i = 0;
			foreach ($results as $row) {
				$block_only_uri = explode("\n", $row->block_only_uri);
				
				if (strpos($row->block_only_uri, '*') !== false && is_array($block_only_uri)) {
					// remove show only uri /* eg. book/* including book, book/magazine, book/comic/action
					$unset = false;
					
					foreach ($block_only_uri as $uri) {
						$uri = str_replace(array('/*', '*'), '', $uri);
						if (strpos($current_uri, $uri) === false) {
							$unset = true;
						}
					}
					
					if ($unset === true) {
						unset($results[$i], $unset, $uri);
					}
				} else {
					if ($row->block_only_uri != null && !in_array($current_uri, $block_only_uri)) {
						unset($results[$i]);
					}
				}
				
				unset($row, $block_only_uri);
				$i++;
			}
			// end loop cut to show only uri---------------------------------------------------------
			
			$output = null;
			
			if (!empty($results)) {
				// results not empty, start loop display blocks.
				$output = '<div class="area-'.$area_name.'">';
				foreach ($results as $row) {
					if (file_exists(config_item('modules_uri').$row->block_file)) {
						$output .= '<div class="each-block block-id-'.$row->block_id.' block-'.$row->block_name.'">';
						ob_start();
						widget::run($row->block_name, $row->block_file, $row->block_values, $row, $attributes);
						$output .= ob_get_contents();
						ob_end_clean();
						$output .= '</div>';
					}
				}
				$output .= '</div>';
			}
			// end
		} elseif (strpos(current_url(), site_url('area/demo')) !== false) {
			// display area demo
			$this->load->helper('array');
			
			$areas = $this->listAreas($this->theme_system_name);
			$key = recursive_array_search($area_name, $areas);
			$output = '<div class="area-'.$area_name.' demo-area">'.$areas[$key]['area_name'].'</div>';
		} else {
			// something wrong, return nothing
			$output = null;
		}
		
		$query->free_result();
		return $output;
	}// renderArea
	
	
	/**
	 * scan theme direcory
	 * @return mixed 
	 */
	public function scanThemeDir() 
	{
		$map = scandir($this->theme_dir);
		
		if (is_array($map) && !empty($map)) {
			// sort
			natsort($map);
			
			// prepare
			$dir = null;
			$i = 0;
			
			foreach ($map as $key => $item) {
				if ($item != '.' && $item != '..' && $item != 'index.html' && strpos($item, ' ') === false) {
					//if (preg_match("/[^a-zA-Z0-9_]/", $item)) {continue;}
					if (is_dir($this->theme_dir.$item) && file_exists($this->theme_dir.$item.'/'.$item.'.info')) {
						$dir[$i]['theme_system_name'] = $item;
						$pdata = $this->readThemeMetadata($item.'/'.$item.'.info');
						$dir[$i]['theme_name'] = $pdata['name'];
						$dir[$i]['theme_url'] = $pdata['url'];
						$dir[$i]['theme_version'] = $pdata['version'];
						$dir[$i]['theme_description'] = $pdata['description'];
						$dir[$i]['theme_front'] = (file_exists($this->theme_dir.$item.'/front') ? true : false);
						$dir[$i]['theme_admin'] = (file_exists($this->theme_dir.$item.'/site-admin') ? true : false);
						$dir[$i]['theme_screenshot'] = (file_exists($this->theme_dir.$item.'/screenshot.png') ? base_url().$this->theme_dir.$item.'/screenshot.png' : base_url().'public/images/no-screenshot.png');
						$dir[$i]['theme_screenshot_large'] = (file_exists($this->theme_dir.$item.'/screenshot-large.png') ? base_url().$this->theme_dir.$item.'/screenshot-large.png' : '');
						$dir[$i]['theme_enabled'] = $this->isEnabledTheme($item);
						unset($pdata);
					}
					$i++;
				}
			}
			
			return $dir;
		}
	}// scanThemeDir
	
	
	/**
	 * set default theme
	 * @param string $theme_system_name
	 * @param admin|front $set_for
	 * @return boolean 
	 */
	public function setDefaultTheme($theme_system_name = '', $set_for = 'front', $site_id = '') 
	{
		if ($theme_system_name == null) {return false;}
		
		// check if theme was enabled
		if ($this->isEnabledTheme($theme_system_name, $site_id)) {
			// theme was enabled, update to default below.
		} else {
			if (!$this->doEnableTheme($theme_system_name, $site_id)) {
				return false;
			}
		}
		
		// check if there is front folder or site-admin folder for this theme
		if ($set_for == 'admin') {
			if (!file_exists($this->theme_dir.$theme_system_name.'/site-admin')) {return false;}
		} else {
			if (!file_exists($this->theme_dir.$theme_system_name.'/front')) {return false;}
		}
		
		if ($site_id == null) {
			// get site id
			$this->load->model('siteman_model');
			$site_id = $this->siteman_model->getSiteId();
		}
		
		// get theme data
		$theme_db = $this->getThemesData(array('theme_system_name' => $theme_system_name));
		$theme_id = '';
		if ($theme_db != null) {
			$theme_id = $theme_db->theme_id;
		}
		unset($theme_db);
		
		// unset default for all other themes
		$this->db->where('theme_id !=', $theme_id);
		$this->db->where('site_id', $site_id);
		if ($set_for == 'admin') {
			$this->db->where('theme_sites.theme_default_admin', '1');
			$this->db->set('theme_sites.theme_default_admin', '0');
		} else {
			$this->db->where('theme_sites.theme_default', '1');
			$this->db->set('theme_sites.theme_default', '0');
		}
		$this->db->update('theme_sites');
		
		// update to default
		$this->db->where('theme_id', $theme_id);
		$this->db->where('site_id', $site_id);
		if ($set_for == 'admin') {
			$this->db->set('theme_sites.theme_default_admin', '1');
		} else {
			$this->db->set('theme_sites.theme_default', '1');
		}
		$this->db->update('theme_sites');
		
		// delete cache
		$this->config_model->deleteCache('themedefault_');
		$this->config_model->deleteCache('isthemeenable_');
		
		// system log
		$log['sl_type'] = 'theme';
		$log['sl_message'] = 'Set default theme '.$theme_system_name;
		$this->load->model('syslog_model');
		$this->syslog_model->addNewLog($log);
		unset($log);
		
		// done
		return true;
	}// setDefaultTheme
	
	
	/**
	 * show_theme_screenshot
	 * @param string $theme_system_name
	 * @param string $size normal or large.
	 * @return string 
	 */
	public function showThemeScreenshot($theme_system_name = '', $size = 'normal') 
	{
		if ($theme_system_name == null) {
			return base_url().'public/images/no-screenshot.png';
		}
		// normal size
		if ($size == 'normal') {
			if (file_exists($this->theme_dir.$theme_system_name.'/screenshot.png')) {
				return base_url().$this->theme_dir.$theme_system_name.'/screenshot.png';
			}
			return base_url().'public/images/no-screenshot.png';
		} elseif ($size == 'large') {
			if (file_exists($this->theme_dir.$theme_system_name.'/screenshot-large.png')) {
				return base_url().$this->theme_dir.$theme_system_name.'/screenshot-large.png';
			}
		}
	}// showThemeScreenshot
	
	
	/**
	 * alias of method showThemeScreenshot.
	 */
	public function show_theme_screenshot($theme_system_name = '', $size = 'normal') 
	{
		return $this->showThemeScreenshot($theme_system_name, $size);
	}// show_theme_screenshot
	
	
}

