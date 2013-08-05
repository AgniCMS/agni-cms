<h1><?php echo lang('updater_updater'); ?></h1>


<p><strong><?php echo lang('updater_current_agnicms_version'); ?>:</strong> <?php echo $current_version; ?></p>
<p><strong><?php echo lang('updater_most_update_version_available'); ?>:</strong> <?php echo $queue_data['update_version']; ?></p>


<?php echo form_open('site-admin/updater', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<div class="control-group">
		<div class="control-label"><?php echo lang('updater_rewrite_method'); ?>:</div>
		<div class="controls">
			<select name="update_filesys" class="update_filesys" onchange="show_hide_ftp_config($(this));">
				<option value="filesys"<?php if (isset($update_filesys) && $update_filesys == 'filesys') { ?> selected="selected"<?php } ?>><?php echo lang('updater_use_filesystem'); ?></option>
				<option value="ftp"<?php if (isset($update_filesys) && $update_filesys == 'ftp') { ?> selected="selected"<?php } ?>><?php echo lang('updater_use_ftp'); ?></option>
			</select>
		</div>
	</div>

	<div class="ftp-config hide">
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_host'); ?>:</div>
			<div class="controls">
				<input type="text" name="ftp_host" value="<?php if (isset($ftp_host)) {echo $ftp_host;} ?>" maxlength="255" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_username'); ?>:</div>
			<div class="controls">
				<input type="text" name="ftp_username" value="<?php if (isset($ftp_username)) {echo $ftp_username;} ?>" maxlength="255" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_password'); ?>:</div>
			<div class="controls">
				<input type="password" name="ftp_password" value="<?php if (isset($ftp_password)) {echo $ftp_password;} ?>" maxlength="255" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_port'); ?>:</div>
			<div class="controls">
				<input type="text" name="ftp_port" value="<?php if (isset($ftp_port)) {echo $ftp_port;} ?>" maxlength="255" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_passive'); ?>:</div>
			<div class="controls">
				<select name="ftp_passive">
					<option value="true"<?php if (isset($ftp_passive) && $ftp_passive == 'true') { ?> selected="selected"<?php } ?>><?php echo lang('admin_yes'); ?></option>
					<option value="false"<?php if (isset($ftp_passive) && $ftp_passive == 'false') { ?> selected="selected"<?php } ?>><?php echo lang('admin_no'); ?></option>
				</select>
			</div>
		</div>
		
		<div class="control-group">
			<div class="control-label"><?php echo lang('updater_ftp_basepath'); ?>:</div>
			<div class="controls">
				<input type="text" name="ftp_basepath" value="<?php if (isset($ftp_basepath)) {echo $ftp_basepath;} ?>" maxlength="255" />
			</div>
		</div>
	</div>
	
	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary"><?php echo lang('updater_proceed_to_update'); ?></button>
		</div>
	</div>
<?php echo form_close(); ?> 


<script type="text/javascript">
	$(function() {
		if ($('.update_filesys').val() === 'ftp') {
			$('.ftp-config').show();
		}
	});
	
	
	function show_hide_ftp_config(thisobj) {
		if (thisobj.val() === 'ftp') {
			$('.ftp-config').show();
		} else {
			$('.ftp-config').hide();
		}
	}// show_hide_ftp_config
</script>