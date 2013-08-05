<h1><?php echo lang('post_revert'); ?></h1>

<?php echo form_open(); ?> 

	<input type="hidden" name="confirm" value="yes" />
	<p><?php echo lang('post_are_you_sure'); ?></p>
	<button type="submit" class="bb-button btn btn-warning"><?php echo lang('post_yes'); ?></button> &nbsp; 
	<button type="button" class="bb-button btn" onclick="window.history.go(-1);"><?php echo lang('post_no'); ?></button>
	
<?php echo form_close(); ?> 