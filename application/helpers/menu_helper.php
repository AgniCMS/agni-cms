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
 *  show_menuitem_nested_sortable
 * @param array $array
 * @param boolean $first
 * @return string
 */
if (!function_exists('show_menuitem_nested_sortable')) {
	function show_menuitem_nested_sortable($array, $first = true) {
		if (!is_array($array))
			return '';
		
		$ci =& get_instance();
		
		if ($first === true) {
			$output = '<ol class="menu-tree menu-tree-sortable sortable tree-sortable">';
		} else {
			$output = '<ol>';
		}
		
		foreach ($array as $item) {

			$output .= '<li id="list_'.$item->mi_id.'"><div><span class="sort-handle icon-move"></span> <span class="item-name">';
			
			if ($item->custom_link != null) {
				$output .= $item->custom_link;
			} else {
				switch ($item->mi_type) {
					case 'category':
						$output .= anchor($item->link_url, $item->link_text);
						break;
					case 'tag':
						$output .= anchor('tag/'.$item->link_url, $item->link_text);
						break;
					case 'article':
						$output .= anchor('post/'.$item->link_url, $item->link_text);
						break;
					case 'page':
						$output .= anchor($item->link_url, $item->link_text);
						break;
					default:
						$output .= anchor($item->link_url, $item->link_text);
						break;
				}
			}
			
			$output .= '</span>';
			$output .= ' &nbsp; &nbsp; <span class="item-actions">';
			
			if ($ci->account_model->checkAdminPermission('menu_perm', 'menu_edit_perm')) {
				// show edit source link
				// those category, tag, article, page are source and editable.
				switch ($item->mi_type) {
					case 'category':
						$has_edit_source = true;
						$edit_link = site_url('site-admin/category/edit/'. $item->type_id);
						break;
					case 'tag':
						$has_edit_source = true;
						$edit_link = site_url('site-admin/tag/edit/'. $item->type_id);
						break;
					case 'article':
						$has_edit_source = true;
						$edit_link = site_url('site-admin/article/edit/'. $item->type_id);
						break;
					case 'page':
						$has_edit_source = true;
						$edit_link = site_url('site-admin/page/edit/'. $item->type_id);
						break;
					default:
						break;
				}
				
				if (isset($has_edit_source) && $has_edit_source === true) {
					$output .= '<a href="'.$edit_link.'" title="'.lang('admin_edit_source').'"><i class="icon-edit"></i> '.lang('admin_edit_source').'</a> | ';
				}
				// remove unused variables
				unset($has_edit_source, $edit_link);
				
				// show edit menu item link
				$output .= '<a href="#" title="'.lang('admin_edit').'" onclick="return edit_menu_item(\''.$item->mi_id.'\');"><i class="icon-pencil"></i> '.lang('admin_edit').'</a>';
			}
			
			if ($ci->account_model->checkAdminPermission('menu_perm', 'menu_edit_perm') && $ci->account_model->checkAdminPermission('menu_perm', 'menu_delete_perm')) {
				$output .= ' | ';
			}
			
			if ($ci->account_model->checkAdminPermission('menu_perm', 'menu_delete_perm')) {
				$output .= '<a href="#" title="'.lang('admin_delete').'" onclick="return delete_menu_item(\''.$item->mi_id.'\');"><i class="icon-trash"></i> '.lang('admin_delete').'</a>';
			}
			
			$output .= '</span>';
			$output .= '<div class="inline-edit" id="inline-edit-'.$item->mi_id.'"></div>';
			$output .= '</div>';

			if (property_exists($item, 'childs')) {
				$output .= show_menuitem_nested_sortable($item->childs, false);
			}

			$output .= '</li>';
		}
		
		$output .= '</ol>';
		return $output;
	}// show_menuitem_nested_sortable
}


if (!function_exists('show_menuitem_nested')) {
	function show_menuitem_nested($array, $first = true) {
		if (!is_array($array))
			return '';
		
		$ci =& get_instance();
		
		if ($first === true) {
			$output = '<ul class="menu-tree">';
		} else {
			$output = '<ul>';
		}
		
		foreach ($array as $item) {

			$output .= '<li id="list_'.$item->mi_id.'"';
			$output .= ' class=" link-item';
			// check for active link
			if ($item->custom_link != null) {
				if (strpos($item->custom_link, current_url()) !== false) {
					$output .= ' active current';
				}
			} else {
				switch ($item->mi_type) {
					case 'category':
						if (current_url() == site_url($item->link_url)) {
							$output .= ' active current';
						}
						break;
					case 'tag':
						if (current_url() == site_url('tag/'.$item->link_url)) {
							$output .= ' active current';
						}
						break;
					case 'article':
						if (current_url() == site_url('post/'.$item->link_url)) {
							$output .= ' active current';
						}
						break;
					case 'page':
						if (current_url() == site_url($item->link_url)) {
							$output .= ' active current';
						}
						break;
					default:
						if (rtrim(current_url(), '/') == rtrim(site_url($item->link_url), '/')) {
							$output .= ' active current';
						}
						break;
				}
			}
			$output .= '"';
			$output .= '>';
			if ($item->custom_link != null) {
				$output .= $item->custom_link;
			} else {
				switch ($item->mi_type) {
					case 'category':
						$output .= anchor($item->link_url, $item->link_text);
						break;
					case 'tag':
						$output .= anchor('tag/'.$item->link_url, $item->link_text);
						break;
					case 'article':
						$output .= anchor('post/'.$item->link_url, $item->link_text);
						break;
					case 'page':
						$output .= anchor($item->link_url, $item->link_text);
						break;
					default:
						$output .= anchor($item->link_url, $item->link_text);
						break;
				}
			}

			if (property_exists($item, 'childs')) {
				$output .= show_menuitem_nested($item->childs, false);
			}

			$output .= '</li>';
		}
		
		$output .= '</ul>';
		return $output;
	}// show_menuitem_nested
}