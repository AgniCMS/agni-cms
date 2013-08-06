<h1><?php echo lang('tag_tag'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/tag/add';"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('admin_total'), $list_item['total']); ?>
	</div>
	<div class="cmd-right">
		<form method="get" class="form-search">
			<input type="search" name="q" value="<?php echo $q; ?>" />
			<button type="submit" class="bb-button btn"><?php echo lang('tag_search'); ?></button>
		</form>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/tag/process_bulk'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=t_name&sort='.$sort.'&q='.$q, lang('tag_name')); ?></th>
				<th><?php echo anchor(current_url().'?orders=t_total&sort='.$sort.'&q='.$q, lang('tag_total_post')); ?></th>
				<th><?php echo anchor(current_url().'?orders=theme_system_name&sort='.$sort.'&q='.$q, lang('admin_theme')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=t_name&sort='.$sort.'&q='.$q, lang('tag_name')); ?></th>
				<th><?php echo anchor(current_url().'?orders=t_total&sort='.$sort.'&q='.$q, lang('tag_total_post')); ?></th>
				<th><?php echo anchor(current_url().'?orders=theme_system_name&sort='.$sort.'&q='.$q, lang('admin_theme')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
		<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->tid); ?></td>
				<td><?php echo $row->t_name; ?></td>
				<td><?php echo $row->t_total; ?></td>
				<td><?php
				// read theme name
				if ($row->theme_system_name != null) {
					$theme_data = $this->themes_model->readThemeMetadata($row->theme_system_name.'/'.$row->theme_system_name.'.info');
					echo anchor('area/demo/'.$row->theme_system_name, $theme_data['name']);
					unset($theme_data);
				}
				?></td>
				<td><?php echo anchor('site-admin/tag/edit/'.$row->tid, lang('admin_edit')); ?></td>
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