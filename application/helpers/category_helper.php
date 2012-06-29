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
 * show_category_nested
 * !! prototype of nested list from array object !!
 * @author iambriansreed
 * @link http://stackoverflow.com/questions/10309006/php-function-that-create-nested-ul-li-from-arrayobject
 * @param array $array
 * @return string 
 */
if ( !function_exists( 'show_category_nested' ) ) {
	function show_category_nested( $array ) {
		if (!is_array($array))
			return '';

		$output = '<ul class="category-tree">';
		foreach ($array as $item) {

			$output .= '<li>' . anchor( $item->t_uris, $item->t_name );

			if (property_exists($item, 'childs')) {
				$output .= show_category_nested( $item->childs );
			}

			$output .= '</li>';
		}
		$output .= '</ul>';
		return $output;
	}// show_category_nested
}// prototype of nested list from array object


# end prototype ############################################################


function show_category_check( $array, $first = true, $checked_id = array() ) {
	if ( !is_array( $array ) || !is_array( $checked_id ) )
		return '';
	if ( $first === true ) {
		$output = '<ul class="category-check-list">';
	} else {
		$output = '<ul>';
	}
	foreach ( $array as $item ) {
		$output .= '<li id="item_'.$item->tid.'" class="item-level-'.$item->nlevel.'"><label><input type="checkbox" name="tid[]" value="'.$item->tid.'"'.(in_array($item->tid, $checked_id) ? ' checked="checked"' : '' ).' /> '.$item->t_name.'</label>';
		if ( property_exists( $item, 'childs' ) ) {
			$output .= show_category_check( $item->childs, false, $checked_id );
		}
		$output .= '</li>';
	}
	$output .= '</ul>';
	return $output;
}// show_category_check


/**
 * for display categories sortable in admin.
 * @param array $array
 * @param boolean $first
 * @return string 
 */
if ( !function_exists( 'show_category_nested_sortable' ) ) {
	function show_category_nested_sortable( $array, $first = true ) {
		if (!is_array($array))
			return '';
		if ( $first === true ) {
			$output = '<ol class="category-tree category-tree-sortable sortable tree-sortable">';
		} else {
			$output = '<ol>';
		}
		foreach ($array as $item) {

			$output .= '<li id="list_'.$item->tid.'"><div><span class="sort-handle">&nbsp;</span> ' . anchor( 'site-admin/category/edit/'.$item->tid, $item->t_name ) . '</div>';

			if (property_exists($item, 'childs')) {
				$output .= show_category_nested_sortable( $item->childs, false );
			}

			$output .= '</li>';
		}
		$output .= '</ol>';
		return $output;
	}// show_category_nested_sortable
}


/**
 * show category select lists in admin.
 * @param array $array
 * @param integer $select_id
 * @return string 
 */
if ( !function_exists( 'show_category_select' ) ) {
	function show_category_select( $array, $select_id = '' ) {
		if ( !is_array( $array ) )
			return '';
		$output = '';
		foreach ( $array as $item ) {
			$output .= '<option value="'.$item->tid.'"';
			if ( intval($select_id) == intval($item->tid) ) {
				$output .= ' selected="selected"';
			}
			$output .= '>'.str_repeat( '-', $item->nlevel-1 ).$item->t_name.'</option>';
			if (property_exists($item, 'childs')) {
				$output .= show_category_select( $item->childs, $select_id );
			}
		}
		return $output;
	}// show_category_select
}


/**
 * for show categories table in admin
 * @param array $array
 * @return string 
 */
if ( !function_exists( 'show_category_table_adminpage' ) ) {
	function show_category_table_adminpage( $array ) {
		if ( !is_array( $array ) )
			return '';
		//
		$ci =& get_instance();
		//
		$output = '';
		foreach ( $array as $item ) {
			$output .= "\t\t".'<tr>'."\n";
			$output .= "\t\t\t".'<td class="check-column">' . form_checkbox( 'id[]', $item->tid ) . '</td>'."\n";
			$output .= "\t\t\t".'<td class="category-name">';
			$output .= str_repeat( '-', $item->nlevel-1 ).$item->t_name;
			$output .= '</td>'."\n";
			$output .= "\t\t\t".'<td>'.$item->t_total.'</td>'."\n";
			// read theme name
			if ( $item->theme_system_name != null ) {
				$theme_data = $ci->themes_model->read_theme_metadata( $item->theme_system_name.'/'.$item->theme_system_name.'.info' );
				$theme_name = $theme_data['name'];
				unset( $theme_data );
			} else {
				$theme_name = null;
			}
			$output .= "\t\t\t".'<td>'.( $theme_name != null ? anchor( 'area/demo/'.$item->theme_system_name, $theme_name ) : null ).'</td>'."\n";
			$output .= "\t\t\t".'<td>';
			$output .= anchor( 'site-admin/category/edit/'.$item->tid, lang( 'admin_edit' ) );
			$output .= '</td>'."\n";
			$output .= "\t\t".'</tr>'."\n";
			if (property_exists($item, 'childs')) {
				$output .= show_category_table_adminpage( $item->childs );
			}
		}
		return $output;
	}// show_category_table
}
