<h1><?php echo lang('comment_are_you_sure'); ?></h1>

<?php echo form_open(); ?> 
	<input type="hidden" name="act" value="<?php echo $act; ?>" />
	<input type="hidden" name="confirm" value="yes" />
	<?php echo $input_ids; ?> 
	<ul>
		<?php foreach ($list_comments as $item): ?> 
		<li>
			<h3><?php echo $item['subject']; ?></h3>
			<div><?php echo mb_strimwidth(strip_tags($item['comment_body_value']), 0, 90, '...'); ?></div>
		</li>
		<?php endforeach; ?> 
	</ul>
	<blockquote>
		<p><?php echo lang('comment_delete_will_delete_child'); ?></p>
	</blockquote>
	<button type="submit" class="bb-button btn btn-danger"><?php echo lang('admin_yes'); ?></button>
	<button type="button" class="bb-button btn" onclick="window.location='<?php echo site_url('site-admin/comment'); ?>';"><?php echo lang('admin_no'); ?></button>
<?php echo form_close(); ?> 