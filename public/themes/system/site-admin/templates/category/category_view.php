<h1><?php echo lang('category_category'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/category/add';"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('admin_total'), $total_item); ?>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/category/process_bulk'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="row-fluid">
		<div class="span8">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
						<th><?php echo lang('category_name'); ?></th>
						<th><?php echo lang('category_total_posts'); ?></th>
						<th><?php echo lang('admin_theme'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
						<th><?php echo lang('category_name'); ?></th>
						<th><?php echo lang('category_total_posts'); ?></th>
						<th><?php echo lang('admin_theme'); ?></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
					<?php 
					echo show_category_table_adminpage($list_item);
					?> 
				</tbody>
			</table>
			
			<div class="cmds">
				<div class="cmd-left">
					<select name="act">
						<option value="" selected="selected"></option>
						<option value="del"><?php echo lang('admin_delete'); ?></option>
					</select>
					<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<?php if ($this->account_model->checkAdminPermission('category_perm', 'category_sort_perm')): ?> 
		<div class="span4">
			<h2><?php echo lang('category_reposition'); ?></h2>
			<div class="sort-result"></div>
			<?php echo show_category_nested_sortable($list_item); ?>
		</div>
		<?php endif; ?> 
	</div>
<?php echo form_close(); ?> 

<script type="text/javascript">
	$(document).ready(function() {
		$('.category-tree-sortable').nestedSortable({
			forcePlaceholderSize: true,
			handle: '.icon-move',
			helper: 'clone',
			items: 'li',
			placeholder: "ui-state-highlight",
			revert: 250,
			tabSize: 25,
			toleranceElement: '> div',
			update: function(e, ui) {
				order = $(this).nestedSortable('serialize');
				$.ajax({
					url: site_url+'site-admin/category/ajax_sort',
					type: 'POST',
					data: csrf_name+'='+csrf_value+'&'+order,
					dataType: 'html',
					success: function(data) {
						$('.sort-result').html(data);
						setTimeout('$(".sort-result").html("")', 5000);
					},
					error: function(data, status, e) {
						alert(e);
					}
				});
				setTimeout(update_category_list, 1000);
			}
		});
	});
	
	function update_category_list() {
		$.get('<?php echo site_url('site-admin/category?ajax'); ?>', function(data) {
			$('.list-items tbody').html(data);
		});
	}
</script>
