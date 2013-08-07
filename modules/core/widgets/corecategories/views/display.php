<?php

if (!function_exists('show_category_nested_block')) {
	function show_category_nested_block($array, $nohome = false) 
	{
		$ci =& get_instance();

		if (!is_array($array))
			return '';

		$fp_category = null;
		if ($nohome)
			$fp_category = $ci->config_model->loadSingle('content_frontpage_category', $ci->lang->get_current_lang());

		$output = '<ul class="category-tree">';
		foreach ($array as $item) {

			if (($nohome == false) || ($nohome == true && $item->tid != $fp_category)) {

				$output .= '<li>' . anchor($item->t_uris, $item->t_name);

				if (property_exists($item, 'childs')) {
					$output .= show_category_nested_block($item->childs, $nohome);
				}

				$output .= '</li>';

			}

		}
		$output .= '</ul>';
		return $output;
	}// show_category_nested_block
}
