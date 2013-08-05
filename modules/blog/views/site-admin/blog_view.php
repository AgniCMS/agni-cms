<h1><?php echo lang('blog_blog'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button blog-add-post-button" onclick="window.location=site_url+'blog/site-admin/blog/add';"><?php echo lang('blog_new_post'); ?></button>
	</div>
	<div class="clear"></div>
</div>

<?php echo form_open('blog/site-admin/blog/multiple'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<table class="list-items">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang('blog_title'); ?></th>
				<th><?php echo lang('blog_author'); ?></th>
				<th><?php echo lang('blog_date'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang('blog_title'); ?></th>
				<th><?php echo lang('blog_author'); ?></th>
				<th><?php echo lang('blog_date'); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
			<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
			<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->blog_id); ?></td>
				<td><?php echo $row->blog_title; ?></td>
				<td><?php echo anchor('site-admin/account/edit/'.$row->account_id, $row->account_username); ?></td>
				<td><?php echo date('Y-m-d H:i:s', $row->blog_date); ?></td>
				<td><?php echo anchor('blog/site-admin/blog/edit/'.$row->blog_id, lang('admin_edit')); ?></td>
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
				<option value=""></option>
				<option value="del"><?php echo lang('admin_delete'); ?></option>
			</select>
			<button type="submit" class="bb-button"><?php echo lang('admin_submit'); ?></button>
		</div>
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?> 
		</div>
		<div class="clear"></div>
	</div>
	
<?php echo form_close(); ?> 