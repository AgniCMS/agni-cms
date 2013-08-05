<?php
if (isset($values['block_title']) && $values['block_title'] != null) {
	echo '<h3>'.$values['block_title'].'</h3>';
}
?>
<form method="get" action="<?php echo site_url('search'); ?>" class="form-inline">
	<input type="text" name="q" value="<?php echo htmlspecialchars(trim($this->input->get('q', true)), ENT_QUOTES, config_item('charset')); ?>" maxlength="255" />
	<button type="submit" class="btn"><?php echo lang('coremd_search_search'); ?></button>
</form>