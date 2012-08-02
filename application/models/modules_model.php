<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * 
 * PHP version 5
 * 
 * @package agni cms
 * @author vee w.
 * @license http://www.opensource.org/licenses/GPL-3.0
 *
 */

class modules_model extends CI_Model {
	
	
	private $module_dir;
	
	
	function __construct() {
		parent::__construct();
		$this->_setup_module_dir();
	}// __construct
	
	
	function _setup_module_dir() {
		$this->config->load( 'agni' );
		$this->module_dir = $this->config->item( 'modules_uri' );
	}// _setup_module_dir
	
	
	/**
	 * add module
	 * @return mixed 
	 */
	function add_module() {
		// load agni config
		$this->config->load( 'agni' );
		// config upload
		$config['upload_path'] = $this->config->item( 'agni_upload_path' ).'unzip';
		$config['allowed_types'] = 'zip';
		$config['encrypt_name'] = true;
		$this->load->library( 'upload', $config );
		if ( ! $this->upload->do_upload( 'module_file' ) ) {
			return $this->upload->display_errors( '<div>', '</div>' );
		} else {
			$data = $this->upload->data();
		}
		// trying to extract ZIP
		if ( isset( $data ) && is_array( $data ) && !empty( $data ) ) {
			require_once( APPPATH.'/libraries/dunzip/dUnzip2.inc.php' );
			$zip = new dUnzip2( $data['full_path'] );
			$zip->debug = false;
			// check inside zip that it is module directory.
			$list = $zip->getList();
			$valid_module = true;
			$i = 1;
			foreach ( $list as $file_name => $zipped ) {
				if ( $i == 1 ) {
					$module_dir = str_replace( '/', '', $file_name );
				}
				if ( $i == 1 && ( $zipped['compression_method'] != '0' || $zipped['compressed_size'] != '0' || $zipped['uncompressed_size'] != '0' ) ) {
					// first item is not directory.
					$valid_module = false;
				}
				$i++;
				if ( $i >= 2 ) {continue;}
			}
			// module required file is not exists = invalid module file (module_name/module_name_module.php)
			if ( !isset( $list[$module_dir.'/'.$module_dir.'_module.php'] ) ) {
				$valid_module = false;
			}
			// valid module or not
			if ( $valid_module == false ) {
				$zip->__destroy();
				if ( file_exists( $data['full_path'] ) ) {
					unlink( $data['full_path'] );
				}
				unset( $i, $valid_module, $list, $zip );
				return $this->lang->line( 'modules_wrong_structure' );
			} else {
				// unzip
				$zip->unzipall($config['upload_path']);
				$zip->__destroy();
				// remove zip file
				if ( file_exists( $data['full_path'] ) ) {
					unlink( $data['full_path'] );
				}
				// move to module dir
				$this->load->helper( 'file' );
				smartCopy( dirname(BASEPATH).'/'.$config['upload_path'].'/'.$module_dir, dirname(BASEPATH).'/'.$this->module_dir.$module_dir );
				// delete everything in upload module/
				delete_files( $config['upload_path'].'/'.$module_dir, true );
				// delete module/
				rmdir( $config['upload_path'].'/'.$module_dir );
				return true;
			}
		}
	}// add_module
	
	
	/**
	 * delete_a_module
	 * @param string $module_system_name
	 * @return boolean 
	 */
	function delete_a_module( $module_system_name = '' ) {
		// uninstall module controller
		$this->load->module( array( $module_system_name.'_uninstall' ) );
		$find_uninstall = Modules::find($module_system_name.'_uninstall', $module_system_name, 'controllers/');
		if ( isset( $find_uninstall[0] ) && $find_uninstall[0] != null ) {
			// enable module for uninstall action.
			$this->db->where( 'module_system_name', $module_system_name );
			$this->db->set( 'module_enable', '1' );
			$this->db->update( 'modules' );
			// do silent uninstall
			ob_start();
			$module_uninstall = $module_system_name.'_uninstall';
			$this->load->module( $module_system_name.'/'.$module_uninstall );
			$this->$module_uninstall->index();
			$output = ob_get_contents();
			ob_end_clean();
		}
		$this->db->trans_start();
		$this->db->where( 'module_system_name', $module_system_name );
		$this->db->delete( 'modules' );
		$this->db->trans_complete();
		// check transaction
		if ( $this->db->trans_status() === false ) {
			$this->db->trans_rollback();
			return false;
		}
		// delete cache
		$this->config_model->delete_cache( 'ismodactive_' );
		// load file helper for delete folder recursive
		$this->load->helper( 'file' );
		if ( delete_files( $this->module_dir.$module_system_name.'/', true ) == true ) {
			if ( is_dir( $this->module_dir.$module_system_name ) )
				@rmdir( $this->module_dir.$module_system_name );
			return true;
		}
		return false;
	}// delete_a_module
	
	
	/**
	 * do_activate
	 * @param string $module_system_name
	 * @return boolean 
	 */
	function do_activate( $module_system_name = '' ) {
		$pdata = $this->read_module_metadata( $module_system_name.'/'.$module_system_name.'_module.php'  );
		// check if module activated
		$this->db->where( 'module_system_name', $module_system_name );
		$query = $this->db->get( 'modules' );
		// set data for insert/update
		$this->db->set( 'module_name', ( empty($pdata['name']) ? $module_system_name : $pdata['name'] ) );
		$this->db->set( 'module_url', ( !empty($pdata['url']) ? $pdata['url'] : null ) );
		$this->db->set( 'module_version', ( !empty($pdata['version']) ? $pdata['version'] : null ) );
		$this->db->set( 'module_description', ( !empty($pdata['description']) ? $pdata['description'] : null ) );
		$this->db->set( 'module_author', ( !empty($pdata['author_name']) ? $pdata['author_name'] : null ) );
		$this->db->set( 'module_author_url', ( !empty($pdata['author_url']) ? $pdata['author_url'] : null ) );
		$this->db->set( 'module_enable', '1' );
		if ( $query->num_rows() <= 0 ) {
			// never install, use insert.
			$this->db->set( 'module_system_name', $module_system_name );
			$this->db->insert( 'modules' );
		} else {
			$this->db->where( 'module_system_name', $module_system_name );
			$this->db->update( 'modules' );
		}
		//
		unset( $pdata );
		// delete cache
		$this->config_model->delete_cache( 'ismodactive_' );
		// if module have install action?
		$this->load->module( array( $module_system_name.'_install' ) );
		$find_install = Modules::find($module_system_name.'_install', $module_system_name, 'controllers/');
		if ( isset( $find_install[0] ) && $find_install[0] != null ) {
			redirect( $module_system_name.'/'.$module_system_name.'_install' );
		} else {
			return true;
		}
	}// do_activate
	
	
	/**
	 * do_deactivate
	 * @param string $module_system_name
	 * @return boolean 
	 */
	function do_deactivate( $module_system_name = '' ) {
		$this->db->trans_start();
		$this->db->set( 'module_enable', '0' );
		$this->db->where( 'module_system_name', $module_system_name );
		$this->db->update( 'modules' );
		$this->db->trans_complete();
		// check transaction
		if ( $this->db->trans_status() === false ) {
			$this->db->trans_rollback();
			return false;
		}
		// delete cache
		$this->config_model->delete_cache( 'ismodactive_' );
		return true;
	}// do_deactivate
	
	
	/**
	 * is_activated
	 * @param string $module_system_name
	 * @return boolean 
	 */
	function is_activated( $module_system_name = '' ) {
		if ( $module_system_name == null ) {return false;}
		// load cache driver
		$this->load->driver( 'cache', array( 'adapter' => 'file' ) );
		// check cached
		if ( false === $ismod_active = $this->cache->get( 'ismodactive_'.$module_system_name ) ) {
			$this->db->where( 'module_system_name', $module_system_name );
			$this->db->where( 'module_enable', '1' );
			if ( $this->db->count_all_results( 'modules' ) > 0 ) {
				$this->cache->save( 'ismodactive_'.$module_system_name, 'true', 2678400 );
				return true;
			}
			$this->cache->save( 'ismodactive_'.$module_system_name, 'false', 2678400 );
			return false;
		}
		// return cached
		if ( $ismod_active == 'true' ) {
			return true;
		} else {
			return false;
		}
	}// is_activated
	
	
	/**
	 * list all modules
	 * @return mixed 
	 */
	function list_all_modules() {
		$dir = $this->scan_module_dir();
		if ( is_array( $dir ) )
			$pages = array_chunk( $dir, 20 );
		// pagination
		$pgkey = (int)$this->input->get( 'per_page' );
		if ( $pgkey > 0 ) {$pgkey = ($pgkey-1);}
		// pagination-----------------------------
		$this->load->library( 'pagination' );
		$config['base_url'] = site_url( $this->uri->uri_string() ).'?';
		$config['total_rows'] = count($dir);
		$config['per_page'] = 20;
		$config['use_page_numbers'] = true;
		// pagination tags customize for bootstrap css framework
		$config['num_links'] = 3;
		$config['page_query_string'] = true;
		$config['full_tag_open'] = '<div class="pagination"><ul>';
		$config['full_tag_close'] = "</ul></div>\n";
		$config['first_tag_open'] = '<li>';
		$config['first_tag_close'] = '</li>';
		$config['last_tag_open'] = '<li>';
		$config['last_tag_close'] = '</li>';
		$config['next_tag_open'] = '<li>';
		$config['next_tag_close'] = '</li>';
		$config['prev_tag_open'] = '<li>';
		$config['prev_tag_close'] = '</li>';
		$config['cur_tag_open'] = '<li class="active"><a>';
		$config['cur_tag_close'] = '</a></li>';
		$config['num_tag_open'] = '<li>';
		$config['num_tag_close'] = '</li>';
		// end customize for bootstrap
		$config['first_link'] = '|&lt;';
		$config['last_link'] = '&gt;|';
		$this->pagination->initialize( $config);
		//you may need this in view if you call this in controller or model --> $this->pagination->create_links();
		// end pagination-----------------------------
		//
		$output['total'] = count($dir);
		if ( is_array( $dir ) ) {
			$output['items'] = $pages[$pgkey];
		} else {
			$output['items'] = null;
		}
		//$output['pagination'] = $pagination;
		return $output;
	}// list_all_modules
	
	
	/**
	 * list_all_widgets
	 * @return mixed 
	 */
	function list_all_widgets() {
		$this->db->where( 'module_enable', '1' );
		$query = $this->db->get( 'modules' );
		// load helper
		$this->load->helper( array( 'directory', 'widget' ) );
		// preset $output
		$output = null;
		$i = 0;
		foreach ( $query->result() as $row ) {
			if ( file_exists( $this->module_dir.$row->module_system_name.'/widgets' ) ) {
				$maps = directory_map( $this->module_dir.$row->module_system_name.'/widgets', 1 );
				foreach ( $maps as $dir ) {
					if ( file_exists( $this->module_dir.$row->module_system_name.'/widgets/'.$dir.'/'.$dir.'.php' ) ) {
						include_once( $this->module_dir.$row->module_system_name.'/widgets/'.$dir.'/'.$dir.'.php' );
						$fileobj = new $dir;
						// block title
						if ( property_exists( $fileobj, 'title' ) ) {
							$output[$i]['block_title'] = $fileobj->title;
						} else {
							$output[$i]['block_title'] = $dir;
						}
						// block description
						if ( property_exists( $fileobj, 'description' ) ) {
							$output[$i]['block_description'] = $fileobj->description;
						} else {
							$output[$i]['block_description'] = $dir;
						}
						$output[$i]['block_name'] = $dir;
						$output[$i]['block_file'] = $row->module_system_name.'/widgets/'.$dir.'/'.$dir.'.php';
						$i++;
					}
				}
			}
		}
		//
		unset( $i, $maps, $dir, $fileobj );
		$query->free_result();
		return $output;
	}// list_all_widgets
	
	
	/**
	 * load_admin_nav
	 * @return string|null 
	 */
	function load_admin_nav() {
		// load enabled module
		$this->db->where( 'module_enable', '1' );
		$this->db->order_by( 'module_system_name', 'asc' );
		$query = $this->db->get( 'modules' );
		if ( $query->num_rows() > 0 ) {
			$output = '';
			foreach ( $query->result() as $row ) {
				if ( file_exists( $this->module_dir.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php' ) ) {
					//$this->load->module( $row->module_system_name.'/'.$row->module_system_name.'_admin' );// use include way to fix error sql not valid link resource
					include_once( $this->module_dir.$row->module_system_name.'/controllers/'.$row->module_system_name.'_admin.php' );
					$controller = $row->module_system_name.'_admin';
					$obj = new $controller;
					/*if ( method_exists( $this->$controller, 'admin_nav' ) ) {
						$list_prefix = ''; $list_suffix = '';
						if ( strpos( $this->$controller->admin_nav(), '<li' ) === false ) {$list_prefix = '<li>';}
						if ( strpos( $this->$controller->admin_nav(), '</li>' ) === false ) {$list_suffix = '</li>';}
						$output .= $list_prefix . $this->$controller->admin_nav() . $list_suffix . "\n";
					}
					unset( $this->$controller );*/ // use include way to fix error sql not valid link resource
					if ( method_exists( $obj, 'admin_nav' ) ) {
						$list_prefix = ''; $list_suffix = '';
						if ( strpos( $obj->admin_nav(), '<li' ) === false ) {$list_prefix = '<li>';}
						if ( strpos( $obj->admin_nav(), '</li>' ) === false ) {$list_suffix = '</li>';}
						$output .= $list_prefix . $obj->admin_nav() . $list_suffix . "\n";
					}
				}
			}
			if ( $output != null ) {
				$output = "<ul>\n" . $output . "\n</ul>";
			}
			$query->free_result();
			unset( $controller, $query, $list_prefix, $list_suffix );
			return $output;
		}
		$query->free_result();
		return null;
	}// load_admin_nav
	
	
	/**
	 * read module metadata
	 * @param string $module_item
	 * @return array 
	 */
	function read_module_metadata( $module_item = '' ) {
		if ( empty( $module_item ) ) {return null;}
		// load helper
		$this->load->helper( 'file' );
		// get module info.
		$p_data = read_file( $this->module_dir.$module_item );
		preg_match ( '|Module Name:(.*)$|mi', $p_data, $name );
		preg_match ( '|Module URL:(.*)$|mi', $p_data, $url );
		preg_match ( '|Version:(.*)|i', $p_data, $version );
		preg_match ( '|Description:(.*)$|mi', $p_data, $description );
		preg_match ( '|Author:(.*)$|mi', $p_data, $author_name );
		preg_match ( '|Author URL:(.*)$|mi', $p_data, $author_url );
		$output['name'] = ( isset( $name[1] ) ? trim($name[1]) : '' );
		$output['url'] = ( isset( $url[1] ) ? trim($url[1]) : '' );
		$output['version'] = ( isset( $version[1] ) ? trim($version[1]) : '' );
		$output['description'] = ( isset( $description[1] ) ? trim($description[1]) : '' );
		$output['author_name'] = ( isset( $author_name[1] ) ? trim($author_name[1]) : '' );
		$output['author_url'] = ( isset( $author_url[1] ) ? trim($author_url[1]) : '' );
		unset( $p_data, $name, $url, $version, $description, $author_name, $author_url );
		return $output;
	}// read_module_metadata
	
	
	/**
	 *scan module directory
	 * @return mixed 
	 */
	function scan_module_dir() {
		$map = scandir( $this->module_dir );
		if ( is_array( $map ) && !empty( $map ) ) {
			// sort
			natsort( $map );
			// prepare
			$dir = null;
			$i = 0;
			foreach ( $map as $key => $item ) {
				if ( $item != '.' && $item != '..' && $item != 'index.html' && strpos( $item, ' ' ) === false ) {
					if ( preg_match( "/[^a-zA-Z0-9_]/", $item ) ) {continue;}
					if ( is_dir( $this->module_dir.$item ) && file_exists( $this->module_dir.$item.'/'.$item.'_module.php' ) ) {
						$dir[$i]['module_system_name'] = $item;
						$pdata = $this->read_module_metadata( $item.'/'.$item.'_module.php' );
						$dir[$i]['module_name'] = $pdata['name'];
						$dir[$i]['module_url'] = $pdata['url'];
						$dir[$i]['module_version'] = $pdata['version'];
						$dir[$i]['module_description'] = $pdata['description'];
						$dir[$i]['module_author_name'] = $pdata['author_name'];
						$dir[$i]['module_author_url'] = $pdata['author_url'];
						unset( $pdata );
						// check if activated
						$dir[$i]['module_activated'] = $this->is_activated( $item );
						unset( $result );
					}
					$i++;
				}
			}
			return $dir;
		}
	}// scan_module_dir
	
	
}

