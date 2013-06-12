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
 * render_area
 * use in template to reader blocks in specific area
 * @param string $area_name
 * @param mixed $attributes send values as attirbutes from controller, view
 * @return string 
 */
function render_area($area_name = '', $attributes = '') {
	$ci =& get_instance();
	return $ci->themes_model->render_area($area_name, $attributes);
}// render_area