<h1><?php echo lang('menu_group'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/menu/addgroup'"><?php echo lang('menu_add_group'); ?></button>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/menu/process_group'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
			<th><?php echo anchor(current_url().'?orders=mg_name&amp;sort='.$sort, lang('menu_group_name')); ?></th>
			<th><?php echo lang('menu_group_description'); ?></th>
			<th></th>
		</thead>
		<tfoot>
			<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
			<th><?php echo anchor(current_url().'?orders=mg_name&amp;sort='.$sort, lang('menu_group_name')); ?></th>
			<th><?php echo lang('menu_group_description'); ?></th>
			<th></th>
		</tfoot>
		<tbody>
			<?php if (isset($list_group['items']) && is_array($list_group['items'])): ?> 
			<?php foreach ($list_group['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->mg_id); ?></td>
				<td><?php echo $row->mg_name; ?></td>
				<td><?php echo $row->mg_description; ?></td>
				<td>
					<?php echo anchor('site-admin/menu/editgroup/'.$row->mg_id, lang('admin_edit')); ?> 
					| <?php echo anchor('site-admin/menu/item/'.$row->mg_id, lang('menu_list_items')); ?> 
				</td>
			</tr>
			<?php endforeach; ?> 
			<?php else: ?> 
			<tr>
				<td colspan="4"><?php echo lang('admin_nodata'); ?></td>
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
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?>
		</div>
		<div class="clearfix"></div>
	</div>
	
<?php echo form_close(); ?> 