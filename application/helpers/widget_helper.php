<?php
/**
 * 
 * Widget Plugin from http://codeigniter.com/forums/viewthread/109584
 * 
 * Install this file as application/plugins/widget_pi.php
 * 
 * @version:     0.21
 * $copyright     Copyright (c) Wiredesignz 2009-09-07
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

class widget 
{
	
	
	public $module_path;
	
	
	public function __get($var) 
	{
		global $CI;
		return $CI->$var;
	}
	
	
	/**
	 *@author (modified from main code) vee w.
	 * @param string $name
	 * @param string $file
	 * @param mixed $values
	 * @return type 
	 */
	public function run($name = '', $file = '', $values = '', $dbobj = '') 
	{
		$args = func_get_args();
		
		$this->module_path = config_item('modules_uri');
		
		include_once $this->module_path.$file;

		if (class_exists($name)) {
			$name = strtolower($name);
			$widget = new $name();

			return call_user_func_array(array($widget, 'run'), array_slice($args, 1));
		}
	}// run
	
	
}