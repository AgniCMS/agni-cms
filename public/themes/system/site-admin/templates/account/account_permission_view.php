<h1><?php echo lang('account_permission'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button red btn btn-danger" id="reset-permission"><?php echo lang('account_permission_reset'); ?></button>
	</div>
	<div class="cmd-right">
		<?php if (isset($pagination)) {echo $pagination;} ?> 
	</div>
	<div class="clearfix"></div>
</div>

<?php $this->load->view('site-admin/templates/account/account_permission_partial_view'); ?> 

