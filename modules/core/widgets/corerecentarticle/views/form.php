
<div class="control-group">
	<label class="control-label" for="block_title"><?php echo lang('block_title'); ?>: </label>
	<div class="controls">
		<input type="text" name="block_title" value="<?php echo $values['block_title']; ?>" maxlength="255" id="block_title" class="input-block-level" />
	</div>
</div>

<div class="control-group">
	<label class="control-label" for="recent_num"><?php echo lang('coremd_recentarticle_number'); ?>: </label>
	<div class="controls">
		<input type="number" name="recent_num" value="<?php if (isset($values['recent_num'])) {echo $values['recent_num'];} ?>" maxlength="2" id="recent_num" class="input-block-level" />
	</div>
</div>