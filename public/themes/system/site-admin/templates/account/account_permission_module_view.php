<h1><?php printf($this->lang->line('account_permission_module'), $module->module_name); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="btn" onclick="window.location='<?php echo site_url('site-admin/modules'); ?>';"><span class="icon-chevron-left"></span> <?php echo lang('account_permission_back_to_modules_page'); ?></button>
		<button type="button" class="btn" onclick="window.location='<?php echo site_url('site-admin/account-permission'); ?>';"><span class="icon-chevron-left"></span> <?php echo lang('account_permission_back_to_main_permission_page'); ?></button>
	</div>
	<div class="cmd-right">
		<?php if (isset($pagination)) {echo $pagination;} ?> 
	</div>
	<div class="clearfix"></div>
</div>

<?php $this->load->view('site-admin/templates/account/account_permission_partial_view'); ?> 

