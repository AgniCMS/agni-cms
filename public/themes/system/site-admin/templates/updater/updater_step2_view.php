<h1><?php echo lang('updater_updater'); ?></h1>


<?php if (isset($form_status) && isset($form_status_message)) { ?> 
<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
<?php } ?> 


<button type="button" class="btn" onclick="window.location='<?php echo site_url('site-admin/updater'); ?>';"><span class="icon-chevron-left"></span> <?php echo lang('updater_go_back'); ?></button>
