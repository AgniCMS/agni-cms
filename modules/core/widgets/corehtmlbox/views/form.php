
<div class="control-group">
	<label class="control-label" for="block_title"><?php echo lang('block_title'); ?>: </label>
	<div class="controls">
		<input type="text" name="block_title" value="<?php echo $values['block_title']; ?>" maxlength="255" id="block_title" class="input-block-level" />
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="html"><?php echo lang('coremd_htmlbox_html'); ?>: </label>
	<div class="controls">
		<textarea name="html" cols="30" rows="10" id="html" class="input-block-level"><?php if (isset($values['html'])) {echo htmlspecialchars($values['html'], ENT_QUOTES, config_item('charset'));} ?></textarea>
	</div>
</div>