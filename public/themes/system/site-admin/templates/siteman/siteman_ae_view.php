<h1><?php echo ($this->uri->segment(3) == 'add' ? lang('siteman_add') : lang('siteman_edit')); ?></h1>

<?php echo form_open('', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class=" page-add-edit page-siteman-ae">
		<div class="control-group">
			<label class="control-label" for="site_name"><?php echo lang('siteman_site_name'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="text" name="site_name" value="<?php if (isset($site_name)) {echo $site_name;} ?>" maxlength="255" id="site_name" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="site_domain"><?php echo lang('siteman_site_domain'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="text" name="site_domain" value="<?php if (isset($site_domain)) {echo $site_domain;} ?>" maxlength="255" placeholder="domain.tld" id="site_domain" class="input-block-level" />
				<span class="help-block"><?php echo lang('siteman_domain_comment'); ?></span>
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('siteman_site_status'); ?>: </label>
			<div class="controls">
				<select name="site_status" class="input-block-level">
					<option value="0"<?php if (isset($site_status) && $site_status == '0') { ?> selected="selected"<?php } ?>><?php echo lang('siteman_disable'); ?></option>
					<option value="1"<?php if (isset($site_status) && $site_status == '1') { ?> selected="selected"<?php } ?>><?php echo lang('siteman_enable'); ?></option>
				</select>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button typebutton="submit" class="bb-button standard btn btn-primary"><?php echo lang('admin_save'); ?></button>
			</div>
		</div>
	</div>

<?php echo form_close(); ?> 