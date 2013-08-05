<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| MIME TYPES
| -------------------------------------------------------------------
| This file contains an array of mime types.  It is used by the
| Upload class to help identify allowed file types.
|
*/

/*$mimes = array(	'hqx'	=>	'application/mac-binhex40',
				'cpt'	=>	'application/mac-compactpro',
				'csv'	=>	array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream', 'application/vnd.ms-excel', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'application/excel', 'application/vnd.msexcel'),
				'bin'	=>	'application/macbinary',
				'dms'	=>	'application/octet-stream',
				'lha'	=>	'application/octet-stream',
				'lzh'	=>	'application/octet-stream',
				'exe'	=>	array('application/octet-stream', 'application/x-msdownload'),
				'class'	=>	'application/octet-stream',
				'psd'	=>	'application/x-photoshop',
				'so'	=>	'application/octet-stream',
				'sea'	=>	'application/octet-stream',
				'dll'	=>	'application/octet-stream',
				'oda'	=>	'application/oda',
				'pdf'	=>	array('application/pdf', 'application/x-download'),
				'ai'	=>	'application/postscript',
				'eps'	=>	'application/postscript',
				'ps'	=>	'application/postscript',
				'smi'	=>	'application/smil',
				'smil'	=>	'application/smil',
				'mif'	=>	'application/vnd.mif',
				'xls'	=>	array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
				'ppt'	=>	array('application/powerpoint', 'application/vnd.ms-powerpoint'),
				'wbxml'	=>	'application/wbxml',
				'wmlc'	=>	'application/wmlc',
				'dcr'	=>	'application/x-director',
				'dir'	=>	'application/x-director',
				'dxr'	=>	'application/x-director',
				'dvi'	=>	'application/x-dvi',
				'gtar'	=>	'application/x-gtar',
				'gz'	=>	'application/x-gzip',
				'php'	=>	'application/x-httpd-php',
				'php4'	=>	'application/x-httpd-php',
				'php3'	=>	'application/x-httpd-php',
				'phtml'	=>	'application/x-httpd-php',
				'phps'	=>	'application/x-httpd-php-source',
				'js'	=>	'application/x-javascript',
				'swf'	=>	'application/x-shockwave-flash',
				'sit'	=>	'application/x-stuffit',
				'tar'	=>	'application/x-tar',
				'tgz'	=>	array('application/x-tar', 'application/x-gzip-compressed'),
				'xhtml'	=>	'application/xhtml+xml',
				'xht'	=>	'application/xhtml+xml',
				'zip'	=>  array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
				'mid'	=>	'audio/midi',
				'midi'	=>	'audio/midi',
				'mpga'	=>	'audio/mpeg',
				'mp2'	=>	'audio/mpeg',
				'mp3'	=>	array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
				'aif'	=>	'audio/x-aiff',
				'aiff'	=>	'audio/x-aiff',
				'aifc'	=>	'audio/x-aiff',
				'ram'	=>	'audio/x-pn-realaudio',
				'rm'	=>	'audio/x-pn-realaudio',
				'rpm'	=>	'audio/x-pn-realaudio-plugin',
				'ra'	=>	'audio/x-realaudio',
				'rv'	=>	'video/vnd.rn-realvideo',
				'wav'	=>	array('audio/x-wav', 'audio/wave', 'audio/wav'),
				'bmp'	=>	array('image/bmp', 'image/x-windows-bmp'),
				'gif'	=>	'image/gif',
				'jpeg'	=>	array('image/jpeg', 'image/pjpeg'),
				'jpg'	=>	array('image/jpeg', 'image/pjpeg'),
				'jpe'	=>	array('image/jpeg', 'image/pjpeg'),
				'png'	=>	array('image/png',  'image/x-png'),
				'tiff'	=>	'image/tiff',
				'tif'	=>	'image/tiff',
				'css'	=>	'text/css',
				'html'	=>	'text/html',
				'htm'	=>	'text/html',
				'shtml'	=>	'text/html',
				'txt'	=>	'text/plain',
				'text'	=>	'text/plain',
				'log'	=>	array('text/plain', 'text/x-log'),
				'rtx'	=>	'text/richtext',
				'rtf'	=>	'text/rtf',
				'xml'	=>	'text/xml',
				'xsl'	=>	'text/xml',
				'mpeg'	=>	'video/mpeg',
				'mpg'	=>	'video/mpeg',
				'mpe'	=>	'video/mpeg',
				'qt'	=>	'video/quicktime',
				'mov'	=>	'video/quicktime',
				'avi'	=>	'video/x-msvideo',
				'movie'	=>	'video/x-sgi-movie',
				'doc'	=>	'application/msword',
				'docx'	=>	'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
				'xlsx'	=>	'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
				'word'	=>	array('application/msword', 'application/octet-stream'),
				'xl'	=>	'application/excel',
				'eml'	=>	'message/rfc822',
				'json' => array('application/json', 'text/json')
			);*/// CI original mimes

// vee w. rewrite mimes.
$mimes = array (
			'7z'	=>	array('application/x-7z-compressed', 'application/octet-stream'),
			'aac'	=>	array('audio/x-aac', 'audio/aacp', 'audio/aac'),
			'ace'	=>	array('application/x-compressed', 'application/x-ace'),
			'ai' => 'application/postscript',
			'aif' => 'audio/x-aiff',
			'aifc' => 'audio/x-aiff',
			'aiff' => 'audio/x-aiff',
			'avi' => 'video/x-msvideo',
			'bin' => 'application/macbinary',
			'bmp' => array('image/bmp', 'image/x-windows-bmp'),
			'class' => 'application/octet-stream',
			'cpt' => 'application/mac-compactpro',
			'css' => 'text/css',
			'csv' => array('application/csv', 'application/excel', 'application/octet-stream', 'application/vnd.ms-excel', 'application/vnd.msexcel', 'application/x-csv', 'text/comma-separated-values', 'text/csv', 'text/x-comma-separated-values', 'text/x-csv'),
			'dcr' => 'application/x-director',
			'dir' => 'application/x-director',
			'dll' => 'application/octet-stream',
			'dms' => 'application/octet-stream',
			'doc' => 'application/msword',
			'docx' => array('application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'),
			'dvi' => 'application/x-dvi',
			'dxr' => 'application/x-director',
			'eml' => 'message/rfc822',
			'eps' => 'application/postscript',
			'exe' => array('application/octet-stream', 'application/x-msdownload'),
			'flv'	=>	array('video/x-flv', 'flv-application/octet-stream', 'video/mp4'),
			'gif' => 'image/gif',
			'gtar' => 'application/x-gtar',
			'gz' => 'application/x-gzip',
			'h264' => array('video/h264'),
			'h.264' => array('video/h264'),
			'hqx' => 'application/mac-binhex40',
			'htm' => 'text/html',
			'html' => 'text/html',
			'jpe' => array('image/jpeg', 'image/pjpeg'),
			'jpeg' => array('image/jpeg', 'image/pjpeg'),
			'jpg' => array('image/jpeg', 'image/pjpeg'),
			'js' => 'application/x-javascript',
			'json' => array('application/json', 'text/json'),
			'lha' => 'application/octet-stream',
			'log' => array('text/plain', 'text/x-log'),
			'lzh' => 'application/octet-stream',
			'mid' => 'audio/midi',
			'midi' => 'audio/midi',
			'mif' => 'application/vnd.mif',
			'mov' => 'video/quicktime',
			'movie' => 'video/x-sgi-movie',
			'mp2' => 'audio/mpeg',
			'mp3' => array('audio/mp3', 'audio/mpeg', 'audio/mpeg3', 'audio/mpg'),
			'mpe' => 'video/mpeg',
			'mpeg' => 'video/mpeg',
			'mpg' => 'video/mpeg',
			'mpga' => 'audio/mpeg',
			'oda' => 'application/oda',
			'pdf' => array('application/pdf', 'application/x-download'),
			'php' => 'application/x-httpd-php',
			'php3' => 'application/x-httpd-php',
			'php4' => 'application/x-httpd-php',
			'phps' => 'application/x-httpd-php-source',
			'phtml' => 'application/x-httpd-php',
			'png' => array('image/png', 'image/x-png'),
			'ppt' => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
			'ps' => 'application/postscript',
			'psd' => 'application/x-photoshop',
			'qt' => 'video/quicktime',
			'ra' => 'audio/x-realaudio',
			'ram' => 'audio/x-pn-realaudio',
			'rm' => 'audio/x-pn-realaudio',
			'rpm' => 'audio/x-pn-realaudio-plugin',
			'rtf' => 'text/rtf',
			'rtx' => 'text/richtext',
			'rv' => 'video/vnd.rn-realvideo',
			'sea' => 'application/octet-stream',
			'shtml' => 'text/html',
			'sit' => 'application/x-stuffit',
			'smi' => 'application/smil',
			'smil' => 'application/smil',
			'so' => 'application/octet-stream',
			'swf' => 'application/x-shockwave-flash',
			'tar' => 'application/x-tar',
			'text' => 'text/plain',
			'tgz' => array('application/x-gzip-compressed', 'application/x-tar'),
			'tif' => 'image/tiff',
			'tiff' => 'image/tiff',
			'txt' => 'text/plain',
			'wav' => array('audio/wav', 'audio/wave', 'audio/x-wav'),
			'wbxml' => 'application/wbxml',
			'webm' => array('audio/webm', 'video/webm'),
			'wmlc' => 'application/wmlc',
			'word' => array('application/msword', 'application/octet-stream'),
			'xht' => 'application/xhtml+xml',
			'xhtml' => 'application/xhtml+xml',
			'xl' => 'application/excel',
			'xls' => array('application/excel', 'application/msexcel', 'application/vnd.ms-excel'),
			'xlsx' => array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'),
			'xml' => 'text/xml',
			'xsl' => 'text/xml',
			'zip' => array('application/x-zip', 'application/x-zip-compressed', 'application/zip', 'application/octet-stream'),
	);
/*
 * use this code to write mimes
require(APPPATH.'/config/mimes.php');
ksort($mimes);
echo '<pre>';
echo '$mimes = array ('."\n";
foreach ($mimes as $key => $item) {
	echo "\t\t\t'$key' => ";
	if (!is_array($item)) {
		echo "'$item',\n";
	} else {
		echo 'array(';
		sort($item);
		foreach ($item as $sitem) {
			echo "'$sitem'";
			if (end($item) != $sitem) {
				echo ', ';
			}
		}
		echo '),';
		echo "\n";
	}
}
echo "\t".');';
echo '</pre>';
 */


/* End of file mimes.php */
/* Location: ./application/config/mimes.php */
