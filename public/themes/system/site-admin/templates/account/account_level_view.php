<h1><?php echo lang('account_level'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" onclick="window.location=site_url+'site-admin/account-level/add';" class="bb-button standard btn"><?php echo lang('admin_add'); ?></button>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/account-level/process_bulk'); ?> 
	<div class="form-result">
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
	</div>
	
	<table class="table table-striped table-hover" id="sortable">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang('account_level_priority'); ?></th>
				<th><?php echo lang('account_level'); ?></th>
				<th><?php echo lang('account_level_description'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang('account_level_priority'); ?></th>
				<th><?php echo lang('account_level'); ?></th>
				<th><?php echo lang('account_level_description'); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
		<?php foreach ($list_item['items'] as $row): ?><?php $disabled_array = array(1, 2, 999, 1000); ?> 
			<tr class="state-default<?php if (in_array($row->level_priority, $disabled_array, false)): ?> ui-state-disabled<?php endif; ?>" id="listItem_<?php echo $row->level_group_id; ?>">
				<td class="check-column"><?php echo form_checkbox('id[]', $row->level_group_id, '', (in_array($row->level_priority, $disabled_array) ? ' disabled="disabled"' : '')); ?></td>
				<td class="cursor-drag-ns"><?php echo $row->level_priority; ?></td>
				<td><?php echo $row->level_name; ?></td>
				<td><?php echo $row->level_description; ?></td>
				<td><?php echo anchor(current_url().'/edit/'.$row->level_group_id, lang('admin_edit')); ?></td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="5"><?php echo lang('admin_nodata'); ?></td>
			</tr>
		<?php endif; ?> 
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
<?php echo form_close(); ?> 

<script type="text/javascript">
	$(document).ready(function() {
		// Return a helper with preserved width of cells
		var fixHelper = function(e, ui) {
		    ui.children().each(function() {
			  $(this).width($(this).width());
		    });
		    return ui;
		};
		
		// sort level
		$("#sortable tbody").sortable({
			helper: fixHelper,
			stop: function(event, ui) {$('.form-result').html('');},
			start: function(event, ui) {ui.placeholder.html("<td colspan='5'>&nbsp;</td>"); $('.form-result').html('<img src="'+base_url+'public/themes/system/site-admin/images/loading.gif" alt="" />');},
			placeholder: "ui-state-highlight",
			items: "tr:not(.ui-state-disabled)",
			update : function () {
				var orders = $('#sortable tbody').sortable('serialize');
				$.ajax({
					url: site_url+'site-admin/account-level/ajaxsort',
					type: 'POST',
					data: csrf_name+'='+csrf_value+'&'+orders,
					dataType: 'json',
					success: function(data) {
						$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
						setTimeout("clearinfo();", 3000);
					},
					error: function (data, status, e) {
						alert('Fail sort role: '+e);
					}
				});
			}
		});
		$("#sortable tbody").disableSelection();
	});// jquery document.ready
	
	function clearinfo() {
		$(".form-result").html('');
		location.reload();
	}
</script>