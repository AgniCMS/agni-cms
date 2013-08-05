<h1><?php echo lang('themes_delete'); ?></h1>

<?php if (isset($form_status) && isset($form_status_message)) { ?> 
<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
<?php } ?> 

<div class="page-add-edit">
	<?php if (isset($theme_use_in_site['items']) && is_array($theme_use_in_site['items']) && !empty($theme_use_in_site['items'])) { ?> 


	<p><?php printf(lang('themes_still_in_use_in_these_site'), $theme_name); ?></p>
	<ul>
		<?php foreach ($theme_use_in_site['items'] as $row) { ?> 
		<li><?php echo anchor(current_protocol().$row->site_domain.site_path('site-admin/themes'), $row->site_name); ?></li>
		<?php } // endforeach; ?> 
	</ul>


	<?php } else { ?> 


	<p><?php echo sprintf(lang('themes_are_you_sure_delete'), $theme_name); ?></p>

	<?php echo form_open(); ?> 
		<input type="hidden" name="confirm" value="yes" />
		<button type="submit" class="bb-button red btn btn-danger"><?php echo lang('themes_yes'); ?></button>
		<button type="button" class="bb-button btn" onclick="window.location='<?php echo site_url('site-admin/themes'); ?>';"><?php echo lang('themes_no'); ?></button>
	<?php echo form_close(); ?> 


	<?php } // endif; ?> 
</div>
