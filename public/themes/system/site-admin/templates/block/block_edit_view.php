<?php 
	include_once(config_item('modules_uri').$row->block_file);
	$block_title = ucfirst($row->block_name);
	if (class_exists($row->block_name)) {
		$fileobj = new $row->block_name;
		if (property_exists($fileobj, 'title')) {
			$block_title = $fileobj->title;
		}
	}
	?> 
<h1><?php echo lang('block_block') . ': ' . $block_title; ?></h1>

<div class="page-add-edit block-edit">
	
	<div class="cmds">
		<div class="cmd-left">
			<button type="button" class="bb-button btn" onclick="window.location='<?php echo site_url('site-admin/block?theme_system_name='.$row->theme_system_name); ?>';"><span class="icon-chevron-left"></span> <?php echo lang('block_go_back'); ?></button>
		</div>
		<div class="clearfix"></div>
	</div>

	<?php echo form_open(); ?> 
	
		<?php if (isset($form_status) && isset($form_status_message)) { ?> 
		<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
		<?php } ?> 
		<input type="hidden" name="block_id" value="<?php echo $row->block_id; ?>" />

		<div class="control-group">
			<label class="control-label"><?php echo lang('block_theme'); ?>: </label>
			<div class="controls">
				<input type="text" name="theme_system_name" value="<?php echo ucfirst($row->theme_system_name); ?>" readonly="" disabled="disabled" class="input-block-level" />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label"><?php echo lang('block_area'); ?>: </label>
			<div class="controls">
				<input type="text" name="area_name" value="<?php echo ucfirst($row->area_name); ?>" readonly="" disabled="disabled" class="input-block-level" />
			</div>
		</div>
		
		<?php 
		if (isset($fileobj) && is_object($fileobj)) {
			if (method_exists($fileobj, 'block_show_form')) {
				echo $fileobj->block_show_form($row);
			}
		}
		unset($fileobj);
		?> 
		
		<div class="control-group">
			<div class="controls">
				<label class="checkbox inline">
					<input type="checkbox" name="block_status" value="1"<?php if (isset($block_status) && $block_status == '1') {echo ' checked="checked"';} ?> />
					<?php echo lang('block_enable'); ?>
				</label>
			</div>
		</div>
		
		<div id="accordion" class="block-accordion-row">
			<h3><a href="#"><?php echo lang('block_except_uri'); ?></a></h3>
			<div>
				<textarea name="block_except_uri" rows="3" class="except-uri  input-block-level"><?php echo $block_except_uri; ?></textarea>
				<span class="help-block"><?php echo lang('block_except_uri_comment'); ?></span>
			</div>
			<h3><a href="#"><?php echo lang('block_only_uri'); ?></a></h3>
			<div>
				<textarea name="block_only_uri" rows="3" class="except-uri input-block-level"><?php echo $block_only_uri; ?></textarea>
				<span class="help-block"><?php echo lang('block_only_uri_comment'); ?></span>
			</div>
		</div>
		<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>

	<?php echo form_close(); ?> 
	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#accordion').accordion({ 
			autoHeight: false
		});// accordion
	});// jquery start
</script>