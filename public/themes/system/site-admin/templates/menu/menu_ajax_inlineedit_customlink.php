<?php echo form_open(); ?> 

	<div class="control-group">
		<div class="controls">
			<textarea name="custom_link" rows="5" class="span4"><?php echo $custom_link; ?></textarea>
		</div>
	</div>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="bb-button btn btn-primary" onclick="return save_edit_menu_item('<?php echo $mi_id; ?>', $(this).parents('form'));"><?php echo lang('admin_save'); ?></button>
			<button type="button" class="btn" onclick="$('.inline-edit').hide().html('');"><?php echo lang('admin_cancel'); ?></button>
		</div>
	</div>

<?php echo form_close(); ?> 