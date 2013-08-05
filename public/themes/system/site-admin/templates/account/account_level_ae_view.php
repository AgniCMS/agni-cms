<h1><?php if ($this->uri->segment(3) == 'add') {echo lang('account_level_add');} else {echo lang('account_level_edit');} ?></h1>

<?php echo form_open('', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	<div class="page-add-edit">
		<div class="control-group">
			<label class="control-label" for="level_name"><?php echo lang('account_level'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="text" name="level_name" value="<?php if (isset($level_name)) {echo $level_name;} ?>" maxlength="255" id="level_name" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="level_description"><?php echo lang('account_level_description'); ?>: </label>
			<div class="controls">
				<input type="text" name="level_description" value="<?php if (isset($level_description)) {echo $level_description;} ?>" maxlength="255" id="level_description" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="bb-button standard btn btn-primary"><?php echo lang('admin_save'); ?></button>
			</div>
		</div>
	</div>
<?php echo form_close(); ?> 