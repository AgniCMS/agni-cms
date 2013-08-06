<h1><?php echo lang('themes_themes'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/themes/add';"><?php echo lang('admin_add'); ?></button>
	</div>
	<div class="clearfix"></div>
</div>

<?php if (isset($form_status) && isset($form_status_message)) { ?> 
<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
<?php } ?> 


<h2><?php echo lang('themes_enabled_themes'); ?></h2>
<div class="list-themes list-themes-enabled">
	<?php if (isset($list_enabled['items']) && is_array($list_enabled['items'])): ?> 
	<?php foreach ($list_enabled['items'] as $key): ?> 
	<div class="each-theme each-theme-enabled">
		<?php $ss_large = $this->themes_model->showThemeScreenshot($key->theme_system_name, 'large');
		if ($ss_large != null) {echo '<a href="'.$ss_large.'">';} ?> 
		<img src="<?php echo $this->themes_model->showThemeScreenshot($key->theme_system_name); ?>" alt="<?php echo $key->theme_system_name; ?>" />
		<?php if ($ss_large != null) {echo '</a>';} ?> 
		<div class="theme-name">
			<?php if (!empty($key->theme_name)): ?><?php echo $key->theme_name; ?><?php else: ?><em title="<?php echo lang('themes_no_name'); ?>"><?php echo $key->theme_system_name; ?></em><?php endif; ?>
			<?php if ($key->theme_default == '1'): ?> (<?php echo lang('themes_default_theme'); ?>)<?php endif; ?>
			<?php if ($key->theme_default_admin == '1'): ?> (<?php echo lang('themes_default_admin_theme'); ?>)<?php endif; ?>
		</div>
		<?php if (!empty($key->theme_description)): ?><div class="theme-description"><?php echo $key->theme_description; ?></div><?php endif; ?> 
		<ul class="theme-cmd actions-inline">
			<?php if ($key->theme_default == '0' && $key->theme_default_admin == '0'): ?> 
			<li><?php echo anchor('site-admin/themes/disable/'.urlencode($key->theme_system_name), lang('themes_disable')); ?></li>
			<?php endif; ?> 
			<?php if ($key->theme_default == '0'): ?> 
			<li><?php echo anchor('site-admin/themes/defaults/'.urlencode($key->theme_system_name), lang('themes_default')); ?></li>
			<?php endif; ?> 
		</ul>
	</div>
	<?php endforeach; ?> 
	<?php endif; ?> 
</div>


<h2><?php echo lang('themes_disabled_themes'); ?></h2>
<div class="list-themes">
	<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
	<?php foreach ($list_item['items'] as $key): ?> 
	<?php if ($key['theme_enabled'] == false): ?> 
	<div class="each-theme">
		<?php if ($key['theme_screenshot_large'] != null) {echo '<a href="'.$key['theme_screenshot_large'].'">';} ?> 
		<img src="<?php echo $key['theme_screenshot']; ?>" alt="<?php echo $key['theme_system_name']; ?>" />
		<?php if ($key['theme_screenshot_large'] != null) {echo '</a>';} ?> 
		<div class="theme-name"><?php if (!empty($key['theme_name'])): ?><?php echo $key['theme_name']; ?><?php else: ?><em title="<?php echo lang('themes_no_name'); ?>"><?php echo $key['theme_system_name']; ?></em><?php endif; ?></div>
		<?php if (!empty($key['theme_description'])): ?><div class="theme-description"><?php echo $key['theme_description']; ?></div><?php endif; ?> 
		<ul class="theme-cmd actions-inline">
			<li><?php echo anchor('site-admin/themes/enable/'.urlencode($key['theme_system_name']), lang('themes_enable')); ?></li>
			<li><?php echo anchor('site-admin/themes/defaults/'.urlencode($key['theme_system_name']), lang('themes_enable_and_default')); ?></li>
			<li><?php echo anchor('site-admin/themes/delete/'.urlencode($key['theme_system_name']), lang('admin_delete')); ?></li>
		</ul>
	</div>
	<?php endif; ?> 
	<?php endforeach; ?> 
	<?php endif; ?> 
</div>
<?php if (isset($pagination)) {echo $pagination;} ?>


<?php echo form_open('site-admin/themes/defaultadmin/', array('class' => 'form-inline')); ?> 
<div class="theme-admin">
	<h2><?php echo lang('themes_administration'); ?></h2>
	
	<div class="control-group">
		<div class="controls">
			<select name="theme_system_name">
				<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
				<?php foreach ($list_item['items'] as $key): ?> 
				<?php if ($key['theme_admin'] == true): ?> 
				<option value="<?php echo $key['theme_system_name']; ?>"<?php if ($theme_admin_name == $key['theme_system_name']): ?> selected="selected"<?php endif; ?>><?php if (!empty($key['theme_name'])): ?><?php echo $key['theme_name']; ?><?php else: ?><?php echo $key['theme_system_name']; ?><?php endif; ?></option>
				<?php endif; ?> 
				<?php endforeach; ?> 
				<?php endif; ?> 
			</select>
			<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>
		</div>
	</div>
	
</div>
<?php echo form_close(); ?> 