<div class="page-add-edit">
	<h1><?php echo lang( 'akismet_configuration' ); ?></h1>

	<?php echo form_open(); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

		<label><?php echo lang( 'akismet_api' ); ?>: 
			<input type="text" name="akismet_api" value="<?php if ( isset( $akismet_api ) ) {echo $akismet_api;} ?>" maxlength="255" autocomplete="off" />
			<span class="txt_comment"><?php echo lang( 'akismet_api_key_comment' ); ?></span>
		</label>
	
	<button type="submit" class="bb-button"><?php echo lang( 'admin_save' ); ?></button>

	<?php echo form_close(); ?> 
</div>