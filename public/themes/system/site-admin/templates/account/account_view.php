<h1><?php echo lang('account_accounts'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" onclick="window.location=site_url+'site-admin/account/add';" class="bb-button standard btn"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('admin_total'), $list_item['total']); ?> 
	</div>
	<div class="cmd-right">
		<form method="get" class="form-search">
			<input type="text" name="q" value="<?php echo htmlspecialchars(trim($this->input->get('q'))); ?>" maxlength="255" />
			<button type="submit" class="bb-button standard btn"><?php echo lang('account_search'); ?></button>
		</form>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/account/process_bulk'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=accounts.account_id&amp;sort='.$sort, 'ID'); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_username&amp;sort='.$sort, lang('account_username')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_email&amp;sort='.$sort, lang('account_email')); ?></th>
				<th><?php echo lang('account_level'); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_create&amp;sort='.$sort, lang('account_registered_since')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_last_login&amp;sort='.$sort, lang('account_last_login')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_status&amp;sort='.$sort, lang('account_status')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=accounts.account_id&amp;sort='.$sort, 'ID'); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_username&amp;sort='.$sort, lang('account_username')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_email&amp;sort='.$sort, lang('account_email')); ?></th>
				<th><?php echo lang('account_level'); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_create&amp;sort='.$sort, lang('account_registered_since')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_last_login&amp;sort='.$sort, lang('account_last_login')); ?></th>
				<th><?php echo anchor(current_url().'?'.generate_querystring_except(array('orders', 'sort', 'per_page')).'&amp;orders=account_status&amp;sort='.$sort, lang('account_status')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
		<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->account_id); ?></td>
				<td><?php echo $row->account_id; ?></td>
				<td title="<?php echo htmlspecialchars($row->account_fullname); ?>">
					<?php if ($row->account_avatar != null) {echo '<a href="'.$this->base_url.$row->account_avatar.'" target="_new"><img src="'.$this->base_url.$row->account_avatar.'" alt="avatar" class="list-item-avatar" /></a>';} ?>
					<?php echo htmlspecialchars($row->account_username); ?>
				</td>
				<td><?php echo $row->account_email; ?></td>
				<td><?php echo $row->level_name; ?></td>
				<td><?php echo gmt_date('', $row->account_create_gmt, $row->account_timezone); ?></td>
				<td><?php echo gmt_date('', $row->account_last_login_gmt, $row->account_timezone); ?></td>
				<td><span class="ico-<?php echo ($row->account_status == '1' ? 'yes' : 'no'); ?>"></span> <?php if ($row->account_status == '0') {echo $row->account_status_text;} ?></td>
				<td><?php if ($row->account_id !== '0'): ?>
					<?php echo anchor('site-admin/account/edit/'.$row->account_id, lang('admin_edit')); ?> 
					| <?php echo anchor('site-admin/account/viewlog/'.$row->account_id, lang('account_view_logins')); ?> 
				<?php endif; ?></td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="9"><?php echo lang('admin_nodata'); ?></td>
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