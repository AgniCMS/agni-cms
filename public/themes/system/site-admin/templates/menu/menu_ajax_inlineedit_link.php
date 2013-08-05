<?php echo form_open('', array('class' => 'form-horizontal')); ?> 

	<div class="control-group">
		<label class="control-label" for="<?php echo $mi_id; ?>_link_text"><?php echo lang('menu_link_text'); ?>: <span class="txt_require">*</span></label>
		<div class="controls">
			<input type="text" name="link_text" value="<?php echo $link_text; ?>" id="<?php echo $mi_id; ?>_link_text" />
		</div>
	</div>

	<div class="control-group">
		<label class="control-label" for="<?php echo $mi_id; ?>_link_url"><?php echo lang('menu_link_url'); ?>: <span class="txt_require">*</span></label>
		<div class="controls">
			<input type="text" name="link_url" value="<?php echo $link_url; ?>" id="<?php echo $mi_id; ?>_link_url" />
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="bb-button btn btn-primary" onclick="return save_edit_menu_item('<?php echo $mi_id; ?>', $(this).parents('form'));"><?php echo lang('admin_save'); ?></button>
			<button type="button" class="btn" onclick="$('.inline-edit').hide().html('');"><?php echo lang('admin_cancel'); ?></button>
		</div>
	</div>

<?php echo form_close(); ?> 