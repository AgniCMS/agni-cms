<?php (defined('BASEPATH')) OR exit('No direct script access allowed');


/* load the MX_Router class */
require APPPATH."third_party/MX/Router.php";


class MY_Router extends MX_Router {
	
	
	protected $module;
	
	
	function __construct() {
		parent::__construct();
	}// __construct
	
	
	/** Locate the controller **/
	public function locate($segments) {		
		
		$this->module = '';
		$this->directory = '';
		$ext = $this->config->item('controller_suffix').EXT;
		
		/* use module route if available */
		if (isset($segments[0]) AND $routes = Modules::parse_routes($segments[0], implode('/', $segments))) {
			$segments = $routes;
		}
	
		/* get the segments array elements */
		list($module, $directory, $controller) = array_pad($segments, 3, NULL);

		/* check modules */
		foreach (Modules::$locations as $location => $offset) {
		
			/* module exists? */
			if (is_dir($source = $location.$module.'/controllers/')) {
				
				/* ADD AGNI CMS MODULE CHECK */
				// ใน router core ใช้ get_instance ไม่ได้ จึงใช้ $ci->db->get() ไม่ได้ เลยต้อง hardcode เอง.
				require_once(APPPATH.'config/database.php');
				if (!isset($db)) {
					require(APPPATH.'config/database.php');
				}
				$link = mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']) or die('Can\'t connect to db.');
				$db_selected = mysqli_select_db($link, $db['default']['database']) or die('Can\'t select db.');
				mysqli_query($link, 'SET character_set_results='.$db['default']['char_set']);
				mysqli_query($link, 'SET character_set_client='.$db['default']['char_set']);
				mysqli_query($link, 'SET character_set_connection='.$db['default']['char_set']);
				// get site_id
				$site_id = (isset($_GET['site_id']) ? $_GET['site_id'] : '');
				if ($site_id == null) {
					$result = mysqli_query($link, 'SELECT * FROM '.$db['default']['dbprefix'].'sites WHERE site_domain = \''.mysqli_real_escape_string($link, $_SERVER['HTTP_HOST']).'\'');
					$row = mysqli_fetch_object($result);
					if ($row != null) {
						$site_id = $row->site_id;
						mysqli_free_result($result);
						unset($result, $row);
					} else {
						// not found selected site.
						mysqli_free_result($result);
						mysqli_close($link);
						unset($link, $result, $row);
						continue;
					}
				}
				// check if this module in this site id is enable
				$result = mysqli_query($link, 'SELECT * FROM '.$db['default']['dbprefix'].'modules INNER JOIN '.$db['default']['dbprefix'].'module_sites ON '.$db['default']['dbprefix'].'module_sites.module_id = '.$db['default']['dbprefix'].'modules.module_id WHERE module_system_name = \''.$module.'\' AND '.$db['default']['dbprefix'].'module_sites.site_id = '.$site_id.' AND '.$db['default']['dbprefix'].'module_sites.module_enable = 1');
				if (mysqli_num_rows($result) <= 0) {
					mysqli_free_result($result);
					mysqli_close($link);
					unset($link, $db_selected);
					continue;
				}
				mysqli_free_result($result);
				mysqli_close($link);
				unset($link, $db_selected);
				/* END ADD AGNI CMS MODULE CHECK */
				
				$this->module = $module;
				$this->directory = $offset.$module.'/controllers/';
				
				/* module sub-controller exists? */
				if($directory AND is_file($source.$directory.$ext)) {
					return array_slice($segments, 1);
				}
					
				/* module sub-directory exists? */
				if($directory AND is_dir($source.$directory.'/')) {

					$source = $source.$directory.'/'; 
					$this->directory .= $directory.'/';

					/* module sub-directory controller exists? */
					if(is_file($source.$directory.$ext)) {
						return array_slice($segments, 1);
					}
				
					/* module sub-directory sub-controller exists? */
					if($controller AND is_file($source.$controller.$ext))	{
						return array_slice($segments, 2);
					}
				}
				
				/* module controller exists? */			
				if(is_file($source.$module.$ext)) {
					return $segments;
				}
			}
		}
		
		/* application controller exists? */			
		if (is_file(APPPATH.'controllers/'.$module.$ext)) {
			return $segments;
		}
		
		/* application sub-directory controller exists? */
		if($directory AND is_file(APPPATH.'controllers/'.$module.'/'.$directory.$ext)) {
			$this->directory = $module.'/';
			return array_slice($segments, 1);
		}
		
		/* ADD */
		/**
		 * add multi sub directories support
		 * @link http://codeigniter.com/forums/viewthread/190563/
		 * @author Damien K.
		 */
		/*if ($directory) {
			// @edit: Support multi-level sub-folders
			$dir = '';
			do {
				if (strlen($dir) > 0) {
					$dir .= '/';
				}
				$dir .= $segments[0];
				$segments = array_slice($segments, 1);
			} while (count($segments) > 0 && is_dir(APPPATH . 'controllers/' . $dir . '/' . $segments[0]));
			// Set the directory and remove it from the segment array
			$this->set_directory($dir);
			// @edit: END
			// @edit: If no controller found, use 'default_controller' as defined in 'config/routes.php'
			if (count($segments) > 0 && !file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
				array_unshift($segments, $this->default_controller);
			} elseif (empty($segments) && is_dir(APPPATH.'controllers/'.$this->directory)) {
				$segments = array($this->default_controller);
			}
			// @edit: END
			if (count($segments) > 0) {
				// Does the requested controller exist in the sub-folder?
				if (!file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
					// show_404($this->fetch_directory().$segments[0]);
					// @edit: Fix a "bug" where show_404 is called before all the core classes are loaded
					$this->directory = '';
					// @edit: END
				}
			}
			//print_r($segments);
			if ($this->directory.$segments[0] == $module.'/'.$this->default_controller) {
				// skip (for prevent show 404; use next if below)
			} elseif (count($segments) > 0 && file_exists(APPPATH . 'controllers/' . $this->fetch_directory() . $segments[0] . EXT)) {
				return $segments;
			}
		}*/ // NOT USE. because this part make auto_controller blank page when call category1/subcat1/. fix this thing later.
		/* end ADD */
		
		/* application sub-directory default controller exists? */
		if (is_file(APPPATH.'controllers/'.$module.'/'.$this->default_controller.$ext)) {
			$this->directory = $module.'/';
			return array($this->default_controller);
		}
	}// locate
	
	
	function set_directory($dir) {
		$this->directory = str_replace(array('.'), '', $dir) . '/'; // @edit: Preserve '/'
	}// set_directory
	
	
}