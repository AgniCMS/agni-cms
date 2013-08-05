<h1><?php echo lang('modules_add'); ?></h1>

<?php echo form_open_multipart('', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="page-add-edit page-add-module">
	
		<input type="hidden" name="upload" value="module-zip" />
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('modules_select_file'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="file" name="module_file" />
				<span class="help-block">&lt;= <?php echo ini_get('upload_max_filesize'); ?>; <?php echo lang('modules_add_comment'); ?></span>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>
			</div>
		</div>
	</div>
<?php echo form_close(); ?> 