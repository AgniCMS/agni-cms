<h1><?php echo lang('post_delete_article'); ?></h1>

<div class="page-add-edit">
	<h2><?php echo anchor('site-admin/article/edit/'.$row->post_id, $row->post_name); ?></h2>
	<div class="sample-content">
		<?php echo $row->body_value; ?> 
	</div>
	
	<hr />
	<?php echo form_open(current_url().'?rdr='.urlencode($rdr)); ?> 
		<input type="hidden" name="confirm" value="yes" />
		<p><?php echo lang('post_are_you_sure'); ?></p>
		<button type="submit" class="bb-button btn btn-danger"><?php echo lang('post_yes'); ?></button> &nbsp; 
		<button type="button" class="bb-button btn" onclick="window.history.go(-1);"><?php echo lang('post_no'); ?></button>
	<?php echo form_close(); ?> 
</div>