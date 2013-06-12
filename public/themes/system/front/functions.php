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

// set variables for echo in areas.
$area_navigation = render_area('navigation');
$area_breadcrumb = render_area('breadcrumb', (isset($breadcrumb) ? $breadcrumb : ''));
$area_sidebar = render_area('sidebar');
$area_footer = render_area('footer');