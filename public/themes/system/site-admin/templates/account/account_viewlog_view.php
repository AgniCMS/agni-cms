<h1><?php echo lang('account_view_logins'); ?> : <?php echo (isset($account_username) ? $account_username : ''); ?></h1>

<?php echo form_open('site-admin/account/delete_log/'.$account_id); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th><?php echo anchor(current_url().'?orders=site_id&sort='.$sort, lang('account_website')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_ua&sort='.$sort, lang('account_useragent')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_os&sort='.$sort, lang('account_OS')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_browser&sort='.$sort, lang('account_browser')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_ip&sort='.$sort, lang('account_ipaddress')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_time&sort='.$sort, lang('account_time')); ?></th>
				<th><?php echo anchor(current_url().'?orders=login_attempt&sort='.$sort, lang('account_login_result')); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
		<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td><?php echo $row->site_name; ?> [<?php echo anchor(current_protocol().$row->site_domain.site_path(), '#'); ?>]</td>
				<td><?php echo $row->login_ua; ?></td>
				<td><?php echo $row->login_os; ?></td>
				<td><?php echo $row->login_browser; ?></td>
				<td><?php echo $row->login_ip; ?></td>
				<td><?php echo gmt_date('', $row->login_time_gmt); ?></td>
				<td><span class="ico-<?php echo ($row->login_attempt == '1' ? 'yes' : 'no'); ?>"></span> <?php echo $row->login_attempt_text; ?></td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="6"><?php echo lang('admin_nodata'); ?></td>
			</tr>
		<?php endif; ?> 
		</tbody>
	</table>
	<div class="cmds">
		<div class="cmd-left">
			<select name="act">
				<option value="" selected="selected"></option>
				<option value="del"><?php echo lang('admin_delete'); ?></option>
				<?php if ($this->account_model->showAccountLevelInfo() === '1'): ?><option value="truncate"><?php echo lang('account_delete_all_user_logins'); ?></option><?php endif; ?>
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
		</div>
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?>
		</div>
		<div class="clear"></div>
	</div>
<?php echo form_close(); ?> 

