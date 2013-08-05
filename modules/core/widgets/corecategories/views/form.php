<div class="control-group">
	<label class="control-label" for="block_title"><?php echo lang('block_title'); ?>: </label>
	<div class="controls">
		<input type="text" name="block_title" value="<?php echo $values['block_title']; ?>" maxlength="255" id="block_title" class="input-block-level" />
	</div>
</div>

<div class="control-group">
	<div class="controls">
		<label class="checkbox inline">
			<input type="checkbox" name="block_nohome" value="1"<?php if (isset($values['block_nohome']) && $values['block_nohome'] == '1') {echo ' checked="checked"';} ?> />
			<?php echo lang('coremd_category_nohome'); ?>
		</label>
	</div>
</div>