<div class="control-group">
	<label class="control-label" for="block_title"><?php echo lang('block_title'); ?>: </label>
	<div class="controls">
		<input type="text" name="block_title" value="<?php echo $values['block_title']; ?>" maxlength="255" id="block_title" class="input-block-level" />
	</div>
</div>

<div class="control-group">
	<label class="control-label"><?php echo lang('coremd_link_menu'); ?>: </label>
	<div class="controls">
		<?php $this->load->model('menu_model');?> 
		<select name="mg_id" class="input-block-level">
			<option value=""></option>
			<?php
			$list_mg = $this->menu_model->listMenuGroup(false);
			if (is_array($list_mg['items'])) {
				foreach ($list_mg['items'] as $row) {
					echo '<option value="'.$row->mg_id.'"'.(isset($values['mg_id']) && $values['mg_id'] == $row->mg_id ? ' selected="selected"' : '').'>'.$row->mg_name.'</option>';
				}
			}
			?>
		</select>
	</div>
</div>