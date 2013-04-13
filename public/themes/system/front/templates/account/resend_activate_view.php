<article class="general-page-container">
	<h1><?php echo lang( 'account_resend_verify_email' ); ?></h1>

	<?php echo form_open( '', array( 'class' => 'form-horizontal' ) ); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?>

		<div class="control-group">
			<label class="control-label" for="input-email"><?php echo lang( 'account_email' ); ?>: </label>
			<div class="controls">
				<input type="email" name="account_email" value="<?php if ( isset( $account_email ) ) {echo $account_email;} ?>" maxlength="255" id="input-email" />
			</div>
		</div>
	
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo lang( 'account_send' ); ?></button>
			</div>
		</div>

	<?php echo form_close(); ?> 
</article>