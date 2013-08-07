<h1><?php echo lang('comment_comment'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<?php echo anchor('site-admin/comment', sprintf(lang('admin_total'), $this->db->count_all_results('comments'))); ?> 
		| <?php $count = $this->db->where('comment_status', '0')->where('comment_spam_status', 'normal')->count_all_results('comments');
		echo anchor(current_url().'?filter=comment_status&amp;filter_val=0', sprintf(lang('comment_total_unapprove'), $count)); ?> 
		| <?php $count = $this->db->where('comment_status', '1')->count_all_results('comments');
		echo anchor(current_url().'?filter=comment_status&amp;filter_val=1', sprintf(lang('comment_total_approved'), $count)); ?> 
		<?php echo $this->modules_plug->do_filter('comment_admin_index_top'); ?> 
	</div>
	<div class="cmd-right">
		<form method="get" class="form-search">
			<input type="hidden" name="filter" value="<?php echo $filter; ?>" />
			<input type="hidden" name="filter_val" value="<?php echo $filter_val; ?>" />
			<input type="text" name="q" value="<?php echo $q; ?>" maxlength="255" />
			<button type="submit" class="bb-button standard btn"><?php echo lang('comment_search'); ?></button>
		</form>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/comment/process_bulk'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th class="comment-user-column"><?php echo anchor(current_url().'?orders=name&amp;sort='.$sort.'&amp;q='.$q.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val, lang('comment_name')); ?></th>
				<th class="comment-column"><?php echo anchor(current_url().'?orders=comment_add&amp;sort='.$sort.'&amp;q='.$q.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val, lang('comment_date')); ?> - <?php echo lang('comment_comment'); ?></th>
				<th><?php echo anchor(current_url().'?orders=post_id&amp;sort='.$sort.'&amp;q='.$q.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val, lang('comment_on_post')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=name&amp;sort='.$sort.'&amp;q='.$q.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val, lang('comment_name')); ?></th>
				<th><?php echo lang('comment_comment'); ?></th>
				<th><?php echo anchor(current_url().'?orders=post_id&amp;sort='.$sort.'&amp;q='.$q.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val, lang('comment_on_post')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
			<?php foreach ($list_item['items'] as $row): ?> 
			<tr class="<?php echo ($row->comment_status == '1' ? 'comment-approved-row' : 'comment-un-approve-row'); ?>">
				<td class="check-column"><?php echo form_checkbox('id[]', $row->comment_id); ?></td>
				<td>
					<div class="comment-account-info">
						<img src="<?php echo ($row->account_avatar != null ? base_url().$row->account_avatar : base_url().'public/images/default-avatar.png'); ?>" alt="" class="avatar" />
						<?php echo ($row->c1_account_id != '0' && $row->c1_account_id != null ? anchor('site-admin/account/edit/'.$row->c1_account_id, $row->name) : $row->name); ?> 
						<div class="clearfix"></div>
					</div>
					<div class="comment-user-data"><?php echo $row->ip_address; ?><br />
					<?php echo $row->user_agent; ?></div>
				</td>
				<td>
					<div class="comment-dates"><?php echo sprintf(lang('comment_submitted_on'), gmt_date('Y-m-d H:i:s', $row->comment_add_gmt)); ?></div>
					<?php echo $this->comments_model->modifyCommentContent($row->comment_body_value); ?> 
				</td>
				<td><?php if ($row->post_type == 'page') {
					echo anchor($row->post_uri_encoded, $row->post_name);
					echo ' '.anchor('site-admin/page/edit/'.$row->post_id, '#');
				} elseif ($row->post_type == 'article') {
					echo anchor('post/'.$row->post_uri_encoded, $row->post_name);
					echo ' '.anchor('site-admin/article/edit/'.$row->post_id, '#');
				} ?><br />
				<?php echo sprintf(lang('comment_total_comments'), $row->comment_count); ?>
				</td>
				<td><?php echo anchor('site-admin/comment/edit/'.$row->comment_id, lang('comment_edit')); ?></td>
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
				<?php echo $this->modules_plug->do_filter('comment_admin_index_options'); ?> 
				<?php if ($this->account_model->checkAdminPermission('comment_perm', 'comment_approve_unapprove_perm')): ?> 
				<option value="approve"><?php echo lang('comment_approve'); ?></option>
				<option value="unapprove"><?php echo lang('comment_unapprove'); ?></option>
				<option value="">---------</option>
				<?php endif; ?> 
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

