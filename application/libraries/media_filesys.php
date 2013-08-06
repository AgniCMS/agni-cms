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


class media_filesys 
{
	
	
	// base directory for prevent user back to system files. (no end slash trail)
	public $base_dir;
	// current path (no end slash trail)
	public $current_path;
	
	
	/**
	 * create_folder
	 * @param string $path_create
	 * @param string $name_check
	 * @return mixed
	 */
	public function create_folder($path_create = '', $name_check = '') 
	{
		if (!preg_match("/^[A-Za-z 0-9~_\-.+={}\"'()]+$/", $name_check)) {
			return lang('media_new_folder_disallowed_characters');
		}
		
		if (file_exists($path_create) && is_dir($path_create)) {
			return lang('media_new_folder_fail_folder_exists');
		}
		
		$result = mkdir($path_create, 0777, true);
		
		if ($result === true) {
			return true;
		}
		
		unset($result);
		
		return lang('media_new_folder_failed_please_check_write_permission');
	}// create_folder
	
	
	/**
	 * is_over_limit_base
	 * sample1 is_over_limit_base('public/upload/media', 'public/upload/media/sub-folder/') => return false because it is not over limit base
	 * sample2 is_over_limit_base('public/upload/media', 'public/upload/') => return true because it is over limit base
	 * sample3 is_over_limit_base('public/upload/media', 'public/upload/media/sub-folder/../../../../') => return true because it is over limit base
	 * @param string $path1
	 * @param string $path2
	 * @return boolean
	 */
	public function is_over_limit_base($path1 = '', $path2 = '') 
	{
		if ($path1 == null) {
			$path1 = $this->base_dir;
		}
		
		if ($path2 == null) {
			$path2 = $this->current_path;
		}
		
		// in case  ?current_path=/base/dir/outer/ which is outside of 'base_dir'
		if (strpos($path2, $path1) === false) {
			return true;
		}
		
		// in case ?current_path=public/media/some/dir/../../../../../.. which browse upper base_dir level
		if (mb_strlen(realpath($path1)) > mb_strlen(realpath($path2))) {
			return true;
		}
		
		return false;
	}// is_over_limit_base
	
	
	/**
	 * list_dir
	 * list folders and files in current path.
	 * @param boolean $split_folder_file
	 * @return array
	 */
	public function list_dir($split_folder_file = true) 
	{
		if ($this->base_dir != null) {
			// check browse parent folder upper base_dir or not?
			if ($this->is_over_limit_base($this->base_dir, $this->current_path)) {
				$browse_path = $this->base_dir;
			} else {
				$browse_path = $this->current_path;
			}
		} else {
			$browse_path = $this->current_path;
		}
		
		if (is_dir($browse_path)) {
			$fs = scandir($browse_path);
		} else {
			// current_path is not directory, return empty array.
			return array();
		}
		
		// sort for human understandable. for example system sort will be 1 10 11 2 3 4 5 6 7 8 9, but natsort will be 1 2 3 ... to 10
		if (is_array($fs)) {
			natsort($fs);
		}
		
		if ($split_folder_file === true) {
			// split folders and files. folder comes first
			$tmp_fs = array();
			
			if (is_array($fs) && !empty($fs)) {
				// folder first.
				foreach ($fs as $file_folder) {
					if ($file_folder == '.' || $file_folder == '..' || is_dir($browse_path.'/'.$file_folder)) {
						$tmp_fs[] = $file_folder;
					}
				}
				
				// then file.
				foreach ($fs as $file_folder) {
					if ($file_folder != '.' && $file_folder != '..' && is_file($browse_path.'/'.$file_folder)) {
						$tmp_fs[] = $file_folder;
					}
				}
			}
			
			$fs = $tmp_fs;
			
			unset($browse_path, $tmp_fs);
		}
		
		return $fs;
	}// list_dir
	
	
	/**
	 * list_dir_and_sub
	 * list folder files recursively
	 * @return array
	 */
	public function list_dir_and_sub() 
	{
		if ($this->base_dir != null) {
			// this method is list all folders and sub. we do not need to set browse_path to current anymore.
			$browse_path = $this->base_dir;
			
			// check browse parent folder upper base_dir or not?
			if ($this->is_over_limit_base($this->base_dir, $this->current_path)) {
				$browse_path = $this->base_dir;
			}
		} else {
			$browse_path = $this->current_path;
		}
		
		if (is_dir($browse_path)) {
			$ci =& get_instance();
			$ci->load->helper(array('directory'));
			
			$fs = directory_map($browse_path);
		} else {
			// current_path is not directory, return empty array.
			return array();
		}
		
		return $fs;
	}// list_dir_and_sub
	
	
	/**
	 * move media file
	 * @param string $old_path
	 * @param string $new_path
	 * @return boolean
	 */
	public function moveMediaFile($old_path = '', $new_path = '') 
	{
		if (file_exists($old_path) && is_file($old_path)) {
			$result = copy($old_path, $new_path);
			
			if ($result === true) {
				unlink($old_path);
			}
			return $result;
		}
		
		return false;
	}// moveMediaFile
	
	
	/**
	 * rename folder
	 * @param string $current_path
	 * @param string $current_folder
	 * @param string $new_name
	 * @return mixed
	 */
	public function renameFolder($current_path = '', $current_folder = '', $new_name = '') 
	{
		if ($this->is_over_limit_base($this->base_dir, $current_path.'/') === true || $current_path == $this->base_dir) {
			return 'Hack attempt!';
		}
		
		if (!preg_match("/^[A-Za-z 0-9~_\-.+={}\"'()]+$/", $new_name)) {
			return lang('media_new_folder_disallowed_characters');
		}
		
		if (!file_exists($current_path) || !is_dir($current_path)) {
			return lang('media_folder_not_exists');
		}
		
		// loop cut current folder and set new one. --------------------------------------------------------
		$current_path_exp = explode('/', $current_path);
		$new_folder_name_path = '';
		$i = 1;
		foreach ($current_path_exp as $path) {
			if ($i < count($current_path_exp)) {
				$new_folder_name_path .= $path.'/';
			}
			$i++;
		}
		$new_folder_name_path .= $new_name;
		
		unset($current_path_exp, $path);
		// loop cut current folder and set new one. --------------------------------------------------------
		
		$result = rename($current_path, $new_folder_name_path);
		
		if ($result === true) {
			return true;
		}
		
		unset($new_folder_name_path, $result);
		
		return lang('media_rename_folder_failed_please_check_write_permission');
	}// renameFolder
	
	
}

?>