<h1><?php echo lang('urls_url_redirect'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/urls/add';"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('admin_total'), $list_item['total']); ?> 
	</div>
	<div class="cmd-right">
		<form method="get" class="search">
			<input type="text" name="q" value="<?php echo $q; ?>" maxlength="255" />
			<button type="submit" class="bb-button btn"><?php echo lang('urls_search'); ?></button>
		</form>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/urls/multiple'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=uri&amp;sort='.$sort, lang('urls_uri')); ?></th>
				<th><?php echo anchor(current_url().'?orders=redirect_to&amp;sort='.$sort, lang('urls_redirect_to')); ?></th>
				<th><?php echo anchor(current_url().'?orders=redirect_code&amp;sort='.$sort, lang('urls_redirect_code')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=uri&amp;sort='.$sort, lang('urls_uri')); ?></th>
				<th><?php echo anchor(current_url().'?orders=redirect_to&amp;sort='.$sort, lang('urls_redirect_to')); ?></th>
				<th><?php echo anchor(current_url().'?orders=redirect_code&amp;sort='.$sort, lang('urls_redirect_code')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
			<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->alias_id); ?></td>
				<td><?php echo anchor($row->uri_encoded, $row->uri, array('target' => '_review')); ?></td>
				<td><?php echo anchor($row->redirect_to_encoded, $row->redirect_to, array('target' => '_review')); ?></td>
				<td><?php echo $this->lang->line('urls_redirect_'.$row->redirect_code); ?></td>
				<td>
					<ul class="actions-inline">
						<li><?php echo anchor('site-admin/urls/edit/'.$row->alias_id, lang('admin_edit')); ?></li>
					</ul>
				</td>
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
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?>
		</div>
		<div class="clearfix"></div>
	</div>
<?php echo form_close(); ?> 