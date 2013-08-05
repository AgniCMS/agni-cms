
<div class="control-group">
	<label class="control-label" for="block_title"><?php echo lang('block_title'); ?>: </label>
	<div class="controls">
		<input type="text" name="block_title" value="<?php echo $values['block_title']; ?>" maxlength="255" id="block_title" class="input-block-level" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label class="checkbox inline">
			<input type="checkbox" name="show_admin_link" value="1"<?php if (isset($values['show_admin_link']) && $values['show_admin_link'] == '1') {echo ' checked="checked"';} ?> />
			<?php echo lang('coremd_login_showadmin_link'); ?>
		</label>
	</div>
</div>