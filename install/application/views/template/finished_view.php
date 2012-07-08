<h1><?php echo lang( 'agni_install_finished' ); ?></h1>

<p class="alert alert-success"><?php echo lang( 'agni_install_finished_msg' ); ?></p>
<p class="alert alert-warning"><?php echo lang( 'agni_install_please_remove_install_dir' ); ?></p>

<div class="button-cmd finished-buttons">
	<div class="next-btn">
		<button type="button" class="btn btn-large btn-primary" onclick="window.location='<?php echo str_replace( 'install/', '', base_url() ); ?>';"><?php echo lang( 'agni_fin_btn_goto_front' ); ?></button>
		<button type="button" class="btn btn-large btn-warning" onclick="window.location='<?php echo str_replace( 'install/', '', base_url() ); ?>site-admin';"><?php echo lang( 'agni_fin_btn_goto_admin' ); ?></button>
	</div>
	<div class="clear"></div>
</div>