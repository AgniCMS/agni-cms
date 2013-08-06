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
 * render area
 * use in template to reader blocks in specific area
 * @param string $area_name
 * @param mixed $attributes send values as attirbutes from controller, view
 * @return string 
 */
function renderArea($area_name = '', $attributes = '') 
{
	$ci =& get_instance();
	return $ci->themes_model->renderArea($area_name, $attributes);
}// renderArea


/**
 * alias of function renderArea
 */
function render_area($area_name = '', $attributes = '') 
{
	return renderArea($area_name, $attributes);
}// render_area