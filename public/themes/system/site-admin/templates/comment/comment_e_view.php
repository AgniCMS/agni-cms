<h1><?php echo lang('comment_edit_comment'); ?></h1>

<?php echo form_open('', array('class' => 'form-horizontal')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="page-add-edit">
		<div class="control-group">
			<label class="control-label" for="name"><?php echo lang('comment_name'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="text" name="name" value="<?php echo $name; ?>"<?php if ($row->account_id != '0' && $row->account_id != null) {echo ' readonly=""';} ?> maxlength="255" id="name" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="email"><?php echo lang('comment_email'); ?>: </label>
			<div class="controls">
				<input type="text" name="email" value="<?php echo $email; ?>" maxlength="255" id="email" class="input-block-level" />
			</div>
		</div>
		
		<?php /*
		<div class="control-group">
			<label class="control-label" for="homepage"><?php echo lang('comment_homepage'); ?>: </label>
			<div class="controls">
				<input type="text" name="homepage" value="<?php echo $homepage; ?>" maxlength="255" id="homepage" />
			</div>
		</div>*/ ?> 
		
		<div class="control-group">
			<label class="control-label" for="subject"><?php echo lang('comment_subject'); ?>: </label>
			<div class="controls">
				<input type="text" name="subject" value="<?php echo $subject; ?>" maxlength="255" id="subject" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="comment_body_value"><?php echo lang('comment_comment'); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<textarea name="comment_body_value" cols="30" rows="10" id="comment_body_value" class="input-block-level"><?php echo htmlspecialchars($comment_body_value, ENT_QUOTES, config_item('charset')); ?></textarea>
			</div>
		</div>
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>
			</div>
		</div>
	</div>

<?php echo form_close(); ?> 