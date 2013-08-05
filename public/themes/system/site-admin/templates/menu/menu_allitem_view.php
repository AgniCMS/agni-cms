<h1><?php echo lang('menu_menu'); ?>: <?php echo $mg->mg_name; ?></h1>

<?php if ($mg->mg_description): ?><p><?php echo $mg->mg_description; ?></p><?php endif; ?> 

<input type="hidden" name="mg_id" value="<?php echo $mg_id; ?>" id="mg_id" />

<div class="form-result"></div>

<div class="row-fluid page-menu-items">
	
	
	<div class="span3 add-menu-item">
		
		<form class="add-source">
			<div class="item-category">
				<h2><?php echo lang('menu_source_category'); ?></h2>
				<?php echo show_category_check($list_category); ?> 
			</div>
			<div class="button-row">
				<button type="button" class="bb-button btn btn-small" onclick="add_content_item_array($(this).parents('form').serialize(), 'category')"><?php echo lang('admin_add'); ?></button>
			</div>
		</form>
		
		<div class="add-source item-article">
			<h2><?php echo lang('menu_source_article'); ?></h2>
			<input type="text" name="article" value="" class="input-add-articles input-block-level" placeholder="<?php echo lang('menu_search_article_to_add'); ?>" />
		</div>
		
		<form class="add-source">
			<div class="item-page">
				<h2><?php echo lang('menu_source_page'); ?></h2>
				<?php if (isset($list_page['items']) && is_array($list_page['items'])): ?> 
				<ul>
					<?php foreach ($list_page['items'] as $row): ?> 
					<li><label><input type="checkbox" name="type_id[]" value="<?php echo $row->post_id; ?>" /> <?php echo $row->post_name; ?></label></li>
					<?php endforeach; ?> 
				</ul>
				<?php endif; ?> 
				<?php if (isset($pagination)) {echo $pagination;} ?> 
				<?php unset($list_page, $row); ?> 
			</div>
			<div class="button-row">
				<button type="button" class="bb-button btn btn-small" onclick="add_content_item_array($(this).parents('form').serialize(), 'page')"><?php echo lang('admin_add'); ?></button>
			</div>
		</form>
		
		<div class="add-source item-tag">
			<h2><?php echo lang('menu_source_tag'); ?></h2>
			<input type="text" name="tag" value="" class="input-add-tags input-block-level" placeholder="<?php echo lang('menu_search_tag_to_add'); ?>" />
		</div>
		
		<form class="add-source item-link">
			<h2><?php echo lang('menu_source_link'); ?></h2>
			<div class="control-group">
				<label class="control-label" for="link_text"><?php echo lang('menu_link_text'); ?>: </label>
				<div class="controls">
					<input type="text" name="link_text" value="" maxlength="255" id="link_text" class="input-block-level" />
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="link_url"><?php echo lang('menu_link_url'); ?>: </label>
				<div class="controls">
					<input type="text" name="link_url" value="" maxlength="255" id="link_url" class="input-block-level" />
				</div>
			</div>
			<div class="control-group">
				<div class="controls">
					<button type="button" class="bb-button btn btn-small" onclick="add_content_item_link($(this).parents('form').serialize(), 'link');"><?php echo lang('admin_add'); ?></button>
				</div>
			</div>
		</form>
		
		<form class="add-source item-customlink">
			<h2><?php echo lang('menu_custom_link'); ?></h2>
			<textarea name="custom_link" placeholder="&lt;a href=&quot;http://link&quot;&gt;text&lt;/a&gt;" class="custom_link input-block-level"></textarea>
			<div class="button-row">
				<button type="button" class="bb-button btn btn-small" onclick="add_content_item_link($(this).parents('form').serialize(), 'custom_link');"><?php echo lang('admin_add'); ?></button>
			</div>
		</form>
		
	</div>
	
	
	<div class="span9 list-menu-items">
		<div class="stored-items">
			<?php echo show_menuitem_nested_sortable($list_item);// this file is in menu_helper. ?> 
		</div>
	</div>
	
	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('.menu-tree-sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: '.sort-handle',
			helper: 'clone',
			items: 'li',
			placeholder: "ui-state-highlight",
			revert: 250,
			tabSize: 25,
			toleranceElement: '> div',
			update: function(e, ui) {
				order = $(this).nestedSortable('serialize');
				$.ajax({
					url: site_url+'site-admin/menu/ajax_sortitem/<?php echo $mg_id; ?>',
					type: 'POST',
					data: csrf_name+'='+csrf_value+'&'+order,
					dataType: 'html',
					success: function(data) {
						$('.form-result').html(data);
						setTimeout('$(".form-result").html("")', 5000);
					},
					error: function(data, status, e) {
						alert(e);
					}
				});
			}
		});// sortable
		
		
		$('.input-add-articles').autocomplete({
			source: site_url+'site-admin/menu/ajax_searchpost/article',
			select: function(event, ui) {
				add_content_item(ui.item.id, 'article');
				$(this).val('');
				return false;
			}
		})
		.data("autocomplete")._renderItem = function(ul, item) {
			return $("<li></li>")
				.data("item.autocomplete", item)
				.append("<a>" + item.value + "<br /><small>" + item.status + "</small></a>")
				.appendTo(ul);
		};// auto complete articles
		
		
		$('.input-add-tags').autocomplete({
			source: site_url+'site-admin/menu/ajax_searchtag',
			select: function(event, ui) {
				add_content_item(ui.item.id, 'tag');
				$(this).val('');
				return false;
			}
		});// auto complete tags
		
	});// jquery
	
	
	function add_content_item(type_id, mi_type) {
		var mg_id = $('#mg_id').val();
		$.ajax({
			url: site_url+'site-admin/menu/ajax_additem',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&type_id='+type_id+'&mi_type='+mi_type+'&mg_id='+mg_id,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					window.location.reload();// reload menu item list
				} else {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					setTimeout("$('.form-result').html('')", 3000);
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function(data, status, e) {
				alert(e);
			}
		});
	}// add_content_item
	
	
	function add_content_item_array(values, mi_type_val) {
		values = values.replace(/tid/gi, 'type_id');
		var mg_id = $('#mg_id').val();
		
		$.ajax({
			url: site_url+'site-admin/menu/ajax_additem',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&'+values+'&mi_type='+mi_type_val+'&mg_id='+mg_id,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					window.location.reload();// reload menu item list
				} else {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					setTimeout("$('.form-result').html('')", 3000);
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function(data, status, e) {
				alert(e);
			}
		});
	}// add_content_item_array
	
	
	function add_content_item_link(values, mi_type) {
		var mg_id = $('#mg_id').val();
		$.ajax({
			url: site_url+'site-admin/menu/ajax_additem',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&'+values+'&mi_type='+mi_type+'&mg_id='+mg_id,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					window.location.reload();// reload menu item list
				} else {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					setTimeout("$('.form-result').html('')", 3000);
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function(data, status, e) {
				alert(e);
			}
		});
	}// add_content_item_link
	
	
	function delete_menu_item(mi_id) {
		confirmdel = confirm('<?php echo lang('menu_are_you_sure_delete'); ?>');
		if (confirmdel == true) {
			$.ajax({
				url: site_url+'site-admin/menu/ajax_deleteitem',
				type: 'POST',
				data: csrf_name+'='+csrf_value+'&mi_id='+mi_id,
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						$('#list_'+mi_id).remove();
					}
				},
				error: function(data, status, e) {
					alert(e);
				}
			});
			return false;
		} else {
			return false;
		}
	}// delete_menu_item
	
	
	function edit_menu_item(mi_id) {
		$('.inline-edit').hide().html('');
		$.ajax({
			url: site_url+'site-admin/menu/ajax_edititem/'+mi_id,
			type: 'GET',
			dataType: 'html',
			success: function(data) {
				$('#inline-edit-'+mi_id).html(data).show();
			}, error: function(data, status, e) {
				alert(e);
			}
		});
		return false;
	}// edit_menu_item
	function save_edit_menu_item(mi_id, thisobj) {
		var serialize_val = thisobj.serialize();
		$.ajax({
			url: site_url+'site-admin/menu/ajax_edititem/'+mi_id,
			type: 'POST',
			data: serialize_val,
			dataType: 'html',
			success: function(data) {
				if (data == 'true') {
					$('.inline-edit').hide().html('');
					$('.form-result').html('<div class="txt_success alert alert-success"><?php echo lang('admin_saved'); ?></div>');
				} else {
					$('.form-result').html(data);
					$('body,html').animate({scrollTop: 0}, 800);
				}
				setTimeout("$('.form-result').html('')", 3000);
				setTimeout("window.location.reload()", 3500);
				return false;
			}, error: function(data, status, e) {
				alert(e);
			}
		});
		return false;
	}// save_edit_menu_item
</script>