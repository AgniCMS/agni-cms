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


/**
 * easy filesize
 * input filesize in Byte and return to easiest to read (KB, MB, GB, ...)
 * @param integer $filesize
 * @return integer|float 
 */
function easy_filesize($filesize = 0) {
	if (!is_numeric($filesize)) {$filesize = 0;}
	
	//
	if ($filesize <= 1) {
		$output = $filesize.' B';
	} elseif ($filesize <= 1024) {
		$output = $filesize.' B';
	} elseif ($filesize <= 1048576) {
		$output = number_format($filesize/'1024', 2).' KB';
	} elseif ($filesize <= 1073741824) {
		$output = number_format($filesize/'1048576', 2).' MB';
	} elseif ($filesize <= 1099511627776) {
		$output = number_format($filesize/'1073741824', 2).' GB';
	} elseif ($filesize <= 1125899906842624) {
		$output = number_format($filesize/'1099511627776', 2).' TB';
	} elseif ($filesize <= 1152921504606846976) {
		$output = number_format($filesize/'1125899906842624', 2).' PB';
	} elseif ($filesize <= 1180591620717411303424) {
		$output = number_format($filesize/'1152921504606846976', 2).' EB';
	} else {
		$output = number_format($filesize/'1180591620717411303424', 2).' ZB';
	}
	
	return str_replace('.00', '', $output);
}// easy_filesize


/**
* Create a new directory, and the whole path.
*
* If  the  parent  directory  does  not exists, we will create it,
* etc.
* @author baldurien at club-internet dot fr 
* @param string the directory to create
* @param int the mode to apply on the directory
* @return bool return true on success, false else
* @previousNames mkdirs
*/

function makeAll($dir, $mode = 0777, $recursive = true) {
	if (is_null($dir) || $dir === "") {
		return FALSE;
	}

	if (is_dir($dir) || $dir === "/") {
		return TRUE;
	}
	
	if (makeAll(dirname($dir), $mode, $recursive)) {
		return mkdir($dir, $mode);
	}
	
	return FALSE;
}// makeAll


/**
* Copy file or folder from source to destination, it can do
* recursive copy as well and is very smart
* It recursively creates the dest file or directory path if there weren't exists
* Situtaions :
* - Src:/home/test/file.txt ,Dst:/home/test/b ,Result:/home/test/b -> If source was file copy file.txt name with b as name to destination
* - Src:/home/test/file.txt ,Dst:/home/test/b/ ,Result:/home/test/b/file.txt -> If source was file Creates b directory if does not exsits and copy file.txt into it
* - Src:/home/test ,Dst:/home/ ,Result:/home/test/** -> If source was directory copy test directory and all of its content into dest     
* - Src:/home/test/ ,Dst:/home/ ,Result:/home/**-> if source was direcotry copy its content to dest
* - Src:/home/test ,Dst:/home/test2 ,Result:/home/test2/** -> if source was directoy copy it and its content to dest with test2 as name
* - Src:/home/test/ ,Dst:/home/test2 ,Result:->/home/test2/** if source was directoy copy it and its content to dest with test2 as name
* @param $source //file or folder
* @param $dest ///file or folder
* @param $options //folderPermission,filePermission
* @return boolean
 * 
 * @author baldurien at club-internet dot fr
 * @link http://sina.salek.ws/content/unix-smart-recursive-filefolder-copy-function-php
 * @author Sina Salek
 * @link http://www.php.net/manual/en/function.copy.php#91256
*/
function smartCopy($source, $dest, $options = array('folderPermission' => 0777, 'filePermission' => 0777)) {
	$result = false;

	if (is_file($source)) {
		if ($dest[strlen($dest) - 1] == '/') {
			if (!file_exists($dest)) {
				makeAll($dest, $options['folderPermission'], true);
			}
			$__dest = $dest . "/" . basename($source);
		} else {
			$__dest = $dest;
		}
		$result = copy($source, $__dest);
		if (file_exists($__dest))
			chmod($__dest, $options['filePermission']);
	} elseif (is_dir($source)) {
		if ($dest[strlen($dest) - 1] == '/') {
			if ($source[strlen($source) - 1] == '/') {
				//Copy only contents
			} else {
				//Change parent itself and its contents
				$dest = $dest . basename($source);
				if (!file_exists($dest))
					mkdir($dest);
				if (file_exists($dest))
					chmod($dest, $options['filePermission']);
			}
		} else {
			if ($source[strlen($source) - 1] == '/') {
				//Copy parent directory with new name and all its content
				if (!file_exists($dest))
					mkdir($dest, $options['folderPermission']);
				if (file_exists($dest))
					chmod($dest, $options['filePermission']);
			} else {
				//Copy parent directory with new name and all its content
				if (!file_exists($dest))
					mkdir($dest, $options['folderPermission']);
				if (file_exists($dest))
					chmod($dest, $options['filePermission']);
			}
		}

		$dirHandle = opendir($source);
		while ($file = readdir($dirHandle)) {
			if ($file != "." && $file != "..") {
				if (!is_dir($source . "/" . $file)) {
					$__dest = $dest . "/" . $file;
				} else {
					$__dest = $dest . "/" . $file;
				}
				//echo "$source/$file ||| $__dest<br />";
				$result = smartCopy($source . "/" . $file, $__dest, $options);
			}
		}
		closedir($dirHandle);
	} else {
		$result = false;
	}
	return $result;
}// smartCopy

