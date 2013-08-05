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


/**
 * list_folder_recursive
 * @param array $array
 * @param array $data
 * @param boolean $first
 * @param string $previous_path
 * @return mixed
 */
function list_folder_recursive($array = array(), $data = array(), $first = true, $previous_path = '') {
	if (!is_array($array))
		return null;
	
	// get CI instance
	$ci =& get_instance();
	
	if ($previous_path == null) {
		$previous_path = $ci->config->item('agni_upload_path').'media/';
	}
	
	// open <ul>
	if ($first === true) {
		$first_ul_attribute = 'class="first-sub-folder-tree list-first-sub-folder-tree" id="media-list-folder-tree"';
		if (isset($data['first_ul_attribute'])) {
			$first_ul_attribute = $data['first_ul_attribute'];
		}
		
		$output = '<ul'.($first_ul_attribute != null ? ' '.$first_ul_attribute : '').'>';
		
		unset($first_ul_attribute);
	} else {
		$ul_attribute = 'class="sub-folder-tree list-sub-folder-tree"';
		
		if (isset($data['ul_attribute'])) {
			$ul_attribute = $data['ul_attribute'];
		}
		
		$output = '<ul'.($ul_attribute != null ? ' '.$ul_attribute : '').'>';
		
		unset($ul_attribute);
	}
	
	// sort current folders
	ksort($array, (phpversion() >= '5.4.0' ? SORT_NATURAL : SORT_STRING));
	
	// loop list folders
	foreach ($array as $key => $item) {
		if (is_array($item)) {
			// open <li>
			$output .= '<li';
			
			if (is_array($item) && !empty($item)) {
				$li_class[] = 'has-sub-folder';
			}
			
			if ($ci->input->get('current_path') == $previous_path.$key) {
				$li_class[] = 'current-path current active';
			}
			
			if (isset($li_class) && is_array($li_class) && !empty($li_class)) {
				$output .= ' class="';
				foreach ($li_class as $class) {
					$output .= $class;
					if (end($li_class) != $class) {
						$output .= ' ';
					}
				}
				$output .= '"';
			}
			unset($li_class, $class);
			
			$output .= '>';

			$output .= '<div class="folder-item-container">';
			$output .= '<span class="icon-folder-open"></span> ';
			$output .= '<a href="'.current_url().'?current_path='.urlencode($previous_path.$key).($data['orders'] != null ? '&amp;orders='.$data['orders'] : '').($data['cur_sort'] != null ? '&amp;sort='.$data['cur_sort'] : '').($data['filter'] != null ? '&amp;filter='.$data['filter'] : '').($data['filter_val'] != null ? '&amp;filter_val='.$data['filter_val'] : '').($data['q'] != null ?'&amp;q='.$data['q'] : '').'" class="folder-link">'.$key.'</a>';
			if (!isset($data['show_delete_folder']) || (isset($data['show_delete_folder']) && $data['show_delete_folder'] === true)) {
				$output .= '<div class="pull-right">';
				$output .= '<a href="#" onclick="return ajax_rename_folder_setup(\''.$previous_path.$key.'\', \''.$key.'\', $(this));" class="rename-folder" title="'.lang('media_rename_folder').'"><span class="icon-pencil"></span></a>';
				$output .= '<a href="#modal-rename-folder" onclick="return ajax_delete_folder(\''.$previous_path.$key.'\', $(this));" class="text-error delete-folder" title="'.lang('admin_delete').'"><span class="icon-remove"></span></a>';
				$output .= '</div>';
				$output .= '<div class="clearfix"></div>';
			}
			$output .= '</div>';
			
			// has sub folder
			if (is_array($item) && !empty($item)) {
				$output .= list_folder_recursive($item, $data, false, $previous_path.$key.'/');
			}

			// close </li>
			$output .= '</li>';
		}
	}
	
	// close </ul>
	$output .= '</ul>';
	
	return $output;
}// list_folder_recursive


/**
 * list_folder_recursive_selectbox
 * @param array $array
 * @param array $data
 * @param string $previous_path
 * @return mixed
 */
function list_folder_recursive_selectbox($array = array(), $data = array(), $previous_path = '') {
	if (!is_array($array))
		return null;
	
	// get CI instance
	$ci =& get_instance();
	
	if ($previous_path == null) {
		$previous_path = $ci->config->item('agni_upload_path').'media/';
	}
	
	// sort current folders
	ksort($array, (phpversion() >= '5.4.0' ? SORT_NATURAL : SORT_STRING));
	
	$output = '';
	
	// loop list folders
	foreach ($array as $key => $item) {
		if (is_array($item)) {
			$tmp_path = explode('/', $previous_path);
			
			$output .= '<option value="'.$previous_path.$key.'"'.(isset($data['selected_option']) && $data['selected_option'] == $previous_path.$key ? ' selected="selected"' : '').'>'.str_repeat('&mdash;', (count($tmp_path)-3)).$key.'</option>';
			$output .= list_folder_recursive_selectbox($item, $data, $previous_path.$key.'/');
		}
	}
	
	return $output;
}// list_folder_recursive_selectbox