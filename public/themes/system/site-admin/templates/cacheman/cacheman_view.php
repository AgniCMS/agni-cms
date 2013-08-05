<h1><?php echo lang('cache_manager'); ?></h1>

<div class="page-add-edit page-cacheman">
	<?php echo form_open('site-admin/cacheman/do_action'); ?> 
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
		
		<div class="form-cache-container">
			<div class="control-group">
				<div class="control-label"><?php echo lang('cache_please_select_action'); ?>: </div>
				<div class="controls">
					<select name="cache_act" class="select-inline">
						<option value=""></option>
						<option value="clear"><?php echo lang('cache_clear_all'); ?></option>
					</select>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
				</div>
			</div>
		</div>
		
	<?php echo form_close(); ?> 
</div>