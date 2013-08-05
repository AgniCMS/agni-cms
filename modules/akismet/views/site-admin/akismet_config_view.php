
	<h1><?php echo lang('akismet_configuration'); ?></h1>

	<?php echo form_open('', array('class' => 'form-horizontal')); ?> 
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
		
		<div class="page-add-edit">
			
			<div class="control-group">
				<label class="control-label" for="akismet_api"><?php echo lang('akismet_api'); ?>: </label>
				<div class="controls">
					<input type="text" name="akismet_api" value="<?php if (isset($akismet_api)) {echo $akismet_api;} ?>" maxlength="255" autocomplete="off" id="akismet_api" />
					<span class="help-block"><?php echo lang('akismet_api_key_comment'); ?></span>
				</div>
			</div>
	
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo lang('admin_save'); ?></button>
				</div>
			</div>
		</div>
	<?php echo form_close(); ?> 
