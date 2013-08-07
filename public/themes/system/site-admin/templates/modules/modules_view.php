<h1><?php echo lang('modules_modules'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button standard btn" onclick="window.location=site_url+'site-admin/module/add';"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('modules_all'), $list_item['total']); ?>
		| <?php echo sprintf(lang('modules_inactive'), ($list_item['total']-$this->db->count_all_results('modules'))); ?>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/module/process_bulk'); ?>
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked);" /></th>
				<th><?php echo lang('modules_name'); ?></th>
				<th><?php echo lang('modules_description'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked);" /></th>
				<th><?php echo lang('modules_name'); ?></th>
				<th><?php echo lang('modules_description'); ?></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?>
		<?php foreach ($list_item['items'] as $key): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $key['module_system_name']); ?></td>
				<td>
					<p>
						<strong>
							<?php if (!empty($key['module_name'])): ?><?php echo $key['module_name']; ?><?php else: ?><em title="<?php echo lang('modules_no_name'); ?>"><?php echo $key['module_system_name']; ?></em><?php endif; ?>
						</strong>
					</p>
					<div>
						<?php if ($current_site_id == '1') { ?> 
						
						<div class="btn-group">
							<a href="#" class="btn btn-mini dropdown-toggle" data-toggle="dropdown"><?php echo lang('modules_activate_deactivate'); ?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?php if (isset($sites['items']) && is_array($sites['items'])) { ?> 
									<?php foreach ($sites['items'] as $site) { ?> 
										<?php if ($this->modules_model->is_activated($key['module_system_name'], $site->site_id) === false) { ?> 
										<li title="<?php echo lang('modules_is_deactivate_in_this_site'); ?>"><?php echo anchor('site-admin/module/activate/'.$key['module_system_name'].'/'.$site->site_id, '<span class="icon-remove"></span> '.$site->site_name); ?></li>
										<?php } else { ?> 
										<li title="<?php echo lang('module_is_activate_in_this_site'); ?>"><?php echo anchor('site-admin/module/deactivate/'.$key['module_system_name'].'/'.$site->site_id, '<span class="icon-ok"></span> '.$site->site_name); ?></li>
										<?php } // endif; ?> 
									<?php } // endforeach; ?> 
								<?php } // endif; ?> 
							</ul>
						</div>
						<?php if ($this->modules_model->is_activated_one($key['module_system_name']) == true) { ?> 
						<?php $find_uninstall = Modules::find($key['module_system_name'].'_uninstall', $key['module_system_name'], 'controllers/');
							if (isset($find_uninstall[0]) && $find_uninstall[0] != null) { ?> 
						<div class="btn-group">
							<a href="#" class="btn btn-mini btn-danger dropdown-toggle" data-toggle="dropdown"><?php echo lang('modules_uninstall'); ?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<?php if (isset($sites['items']) && is_array($sites['items'])) { ?> 
									<?php foreach ($sites['items'] as $site) { ?> 
									<?php if ($this->modules_model->isModuleInstalled($key['module_system_name'], $site->site_id) === true) { ?> 
									<li><?php echo anchor('site-admin/module/uninstall/'.$key['module_system_name'].'/'.$site->site_id, $site->site_name, array('onclick' => 'return ajax_uninstall_module(\''.sprintf(lang('module_are_you_sure_uninstall'), $key['module_name']).'\', $(this));')); ?></li>
									<?php } // endif; ?> 
									<?php } // endforeach; ?> 
								<?php } // endif; ?> 
							</ul>
						</div>
						<?php } //endif; ?>
						<?php } // endif; is_activated_one ?> 
						
						<?php } else { ?> 
						
							<?php if ($this->modules_model->is_activated($key['module_system_name'], $current_site_id) === false) { ?> 
							<?php echo anchor('site-admin/module/activate/'.$key['module_system_name'].'/'.$current_site_id, lang('modules_activate'), array('class' => 'btn btn-mini')); ?>
							<?php } else { ?> 
							<?php echo anchor('site-admin/module/deactivate/'.$key['module_system_name'].'/'.$current_site_id, lang('modules_deactivate'), array('class' => 'btn btn-mini')); ?>
							<?php $find_uninstall = Modules::find($key['module_system_name'].'_uninstall', $key['module_system_name'], 'controllers/');
								if (isset($find_uninstall[0]) && $find_uninstall[0] != null) { ?> 
								<?php echo anchor('site-admin/module/uninstall/'.$key['module_system_name'].'/'.$current_site_id, lang('modules_uninstall'), array('onclick' => 'return ajax_uninstall_module(\''.sprintf(lang('module_are_you_sure_uninstall'), $key['module_name']).'\', $(this));', 'class' => 'btn btn-mini btn-danger')); ?> 
								<?php } //endif; ?>
							<?php } // endif; ?> 
						
						<?php } // endif; $current_site_id == '1' ?> 
						
						<?php if ($this->modules_model->is_activated($key['module_system_name'], $current_site_id) === true && $this->permission_model->hasPermission($key['module_system_name'])) { ?> 
						<?php echo anchor('site-admin/account-permission/module/'.urlencode($key['module_system_name']), lang('modules_set_permission'), array('class' => 'btn btn-mini')); ?> 
						<?php } // endif; ?> 
					</div>
				</td>
				<td>
					<p><?php echo $key['module_description']; ?></p>
					<p>
						<?php echo lang('modules_version'); ?>: <?php echo (!empty($key['module_version']) ? $key['module_version'] : '-'); ?> 
						| <?php echo lang('modules_by'); ?>: <?php if (!empty($key['module_author_name'])) {if (!empty($key['module_author_url'])) {echo anchor($key['module_author_url'], $key['module_author_name']);} else {echo $key['module_author_name'];}} else {echo '-';} ?> 
						<?php if (!empty($key['module_url'])): ?>| <?php echo anchor($key['module_url'], lang('modules_visit_site')); ?><?php endif; ?>
					</p>
				</td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="3"><?php echo lang('admin_nodata'); ?></td>
			</tr>
		<?php endif; ?> 
	</table>
	

	<div class="cmds">
		<div class="cmd-left">
			<select name="act">
				<option value="" selected="selected"></option>
				<option value="activate"><?php echo lang('modules_activate'); ?></option>
				<option value="deactivate"><?php echo lang('modules_deactivate'); ?></option>
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


<script type="text/javascript">
	function ajax_uninstall_module(confirm_msg, thisobj) {
		var confirmval = confirm(confirm_msg);
		var thislink = thisobj.attr('href');
		
		if (confirmval === true) {
			$.ajax({
				url: thislink,
				type: 'POST',
				data: csrf_name+'='+csrf_value+'',
				dataType: 'html',
				success: function(data) {
					window.location.reload();
				}
			});
		}
		
		return false;
	}// ajax_uninstall_module
</script>