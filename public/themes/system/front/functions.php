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
$area_navigation = renderArea('navigation');
$area_breadcrumb = renderArea('breadcrumb', (isset($breadcrumb) ? $breadcrumb : ''));
$area_sidebar = renderArea('sidebar');
$area_footer = renderArea('footer');