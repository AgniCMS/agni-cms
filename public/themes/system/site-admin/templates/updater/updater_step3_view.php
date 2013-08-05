<h1><?php echo lang('updater_updater'); ?></h1>

<p><?php printf(lang('updater_update_completed_now_you_are_running_agnicms_version'), $agni_version); ?></p>

<button type="button" class="btn" onclick="window.location='<?php echo site_url('site-admin'); ?>';"><?php echo lang('updater_go_admin_home'); ?></button>