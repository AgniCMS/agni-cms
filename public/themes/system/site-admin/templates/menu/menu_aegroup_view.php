<h1><?php echo ($this->uri->segment(3) == 'addgroup' ? lang('menu_add_group') : lang('menu_edit_group')); ?></h1>

<?php echo form_open('', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="page-add-edit">
		<div class="control-group">
			<label class="control-label" for="mg_name"><?php echo lang('menu_group_name'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="text" name="mg_name" value="<?php echo $mg_name; ?>" maxlength="255" id="mg_name" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="mg_description"><?php echo lang('menu_group_description'); ?>: </label>
			<div class="controls">
				<input type="text" name="mg_description" value="<?php echo $mg_description; ?>" maxlength="255" id="mg_description" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>
			</div>
		</div>
	</div>

<?php echo form_close(); ?> 