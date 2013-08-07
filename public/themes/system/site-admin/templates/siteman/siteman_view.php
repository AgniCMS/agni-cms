<h1><?php echo lang('siteman_siteman'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<?php if ($this->account_model->checkAdminPermission('siteman_perm', 'siteman_add_perm')) { ?> 
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/siteman/add';"><?php echo lang('admin_add'); ?></button>
		<?php } ?> 
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/siteman/multiple'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=site_name&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_name')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_domain&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_domain')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_status&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_status')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_create_gmt&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_create_date')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_update_gmt&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_update_date')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=site_name&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_name')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_domain&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_domain')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_status&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_status')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_create_gmt&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_create_date')); ?></th>
				<th><?php echo anchor(current_url().'?orders=site_update_gmt&amp;sort='.$sort.'&amp;q='.$q, lang('siteman_site_update_date')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (isset($list_websites['items']) && is_array($list_websites['items'])) { ?> 
			<?php foreach ($list_websites['items'] as $row) { ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->site_id, false, ($row->site_id == '1' ? 'disabled="disabled"' : '')); ?></td>
				<td><?php echo $row->site_name; ?></td>
				<td><?php echo $row->site_domain; ?></td>
				<td><span class="<?php if ($row->site_status == '1') {echo 'icon-ok';} elseif ($row->site_status == '0') {echo 'icon-remove';} ?>"></span></td>
				<td><?php echo gmt_date('Y-m-d H:i:s', $row->site_create_gmt); ?></td>
				<td><?php echo gmt_date('Y-m-d H:i:s', $row->site_update_gmt); ?></td>
				<td>
					<?php if ($this->account_model->checkAdminPermission('siteman_perm', 'siteman_edit_perm')) { ?> 
					<?php echo anchor(current_url().'/edit/'.$row->site_id, lang('admin_edit')); ?> 
					<?php } ?> 
				</td>
			</tr>
			<?php } ?> 
			<?php } else { ?> 
			<tr>
				<td colspan="7"><?php echo lang('admin_nodata'); ?></td>
			</tr>
			<?php } ?> 
		</tbody>
	</table>

	<div class="cmds">
		<div class="cmd-left">
			<select name="act">
				<option value="" selected="selected"></option>
				<?php if ($this->account_model->checkAdminPermission('siteman_perm', 'siteman_delete_perm')): ?> 
				<option value="del"><?php echo lang('admin_delete'); ?></option>
				<?php endif; ?> 
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
		</div>
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?>
		</div>
		<div class="clearfix"></div>
	</div>

<?php echo form_close(); ?> 