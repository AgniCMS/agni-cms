<div class="admin-dashboard">
	
	
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<?php if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_viewall_perm')) { ?> 
	<div class="admin-block">
		<h3><?php echo anchor('site-admin/article', lang('admin_block_articles')); ?></h3>
		<?php
		// list item
		$this->lang->load('post');
		$this->load->model('posts_model');
		$this->load->helper('date');
		$this->posts_model->post_type = 'article';
		$list_item = $this->posts_model->listPost('admin');
		if (is_array($list_item['items'])) {
			$i = 1;
		?> 
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th><?php echo lang('post_article_name'); ?></th>
					<th><?php echo lang('post_author_name'); ?></th>
					<th><?php echo lang('post_status'); ?></th>
					<th><?php echo lang('post_date'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
		<?php
			foreach ($list_item['items'] as $row) {
		?> 
				<tr>
					<td><?php echo anchor('post/'.$row->post_uri_encoded, $row->post_name); ?></td>
					<td><?php echo anchor('site-admin/account/edit/'.$row->account_id, $row->account_username); ?></td>
					<td><?php echo ($row->post_status == '1' ? lang('post_published') : lang('post_draft')); ?></td>
					<td>
						<?php echo lang('post_add_since'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_add_gmt); ?><br />
						<?php echo lang('post_update_since'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_update_gmt); ?><br />
						<?php echo lang('post_publish'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_publish_date_gmt); ?>
					</td>
					<td><?php echo anchor('site-admin/article/edit/'.$row->post_id, lang('admin_edit')); ?></td>
				</tr>
		<?php
				$i++;
				if ($i > 10) {
					break;
				}
			} // endforeach;
		?> 
			</tbody>
		</table>
		<?php
		} // endif; is_array
		?> 
		<?php
		// clear unuse var.
		unset($list_item, $i, $row);
		?> 
	</div>
	<?php } //endif; ?> 
	
	
	<?php if ($this->account_model->checkAdminPermission('comment_perm', 'comment_viewall_perm')) { ?> 
	<div class="admin-block">
		<h3><?php echo anchor('site-admin/comment', lang('admin_block_comment')); ?></h3>
		<p>
			<?php
			$count_comment = $this->db->where('comment_status', '0')->where('comment_spam_status', 'normal')->count_all_results('comments');
			if ($count_comment > 0) {
				echo sprintf(lang('admin_block_new_comment'), $count_comment);
			} else {
				echo lang('admin_block_nonew_comment');
			}
			?> 
		</p>
		<?php 
		// clear unuse var.
		unset($count_comment);
		?> 
	</div>
	<?php } //endif; ?> 
	
	
	<?php if ($this->account_model->checkAdminPermission('account_perm', 'account_manage_perm')) { ?> 
	<div class="admin-block">
		<h3><?php echo anchor('site-admin/account', lang('admin_block_users')); ?></h3>
		<p>
			<?php 
			$count_user = $this->db->count_all_results('accounts');
			echo sprintf(lang('admin_block_total_user'), $count_user);
			?> 
		</p>
	</div>
	<?php } //endif; ?> 
	
	
</div>