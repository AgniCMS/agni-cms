<h1><?php echo lang( 'themes_delete' ); ?></h1>

<?php if ( isset( $form_status ) ) {echo $form_status;} ?>

<p><?php echo sprintf( lang( 'themes_are_you_sure_delete' ), $theme_name ); ?></p>

<?php echo form_open(); ?> 
	<input type="hidden" name="confirm" value="yes" />
	<button type="submit" class="bb-button red btn btn-danger"><?php echo lang( 'themes_yes' ); ?></button>
	<button type="button" class="bb-button btn" onclick="window.location='<?php echo site_url( 'site-admin/themes' ); ?>';"><?php echo lang( 'themes_no' ); ?></button>
<?php echo form_close(); ?> 