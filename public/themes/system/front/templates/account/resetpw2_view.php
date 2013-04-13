<article class="general-page-container">
	<h1><?php echo lang( 'account_reset_password' ); ?></h1>

	<?php if ( isset( $form_status ) ) {echo $form_status;} ?>

	<?php if ( isset( $show_changepw_form ) && $show_changepw_form === true ): ?> 
	<?php echo form_open( '', array( 'class' => 'form-horizontal' ) ); ?> 
	
		<div class="control-group">
			<label class="control-label" for="input-newpass"><?php echo lang( 'account_new_password' ); ?>: <span class="txt_require">*</span></label>
			<div class="controls"><input type="password" name="new_password" value="" id="input-newpass" /></div>
		</div>
	
		<div class="control-group">
			<label class="control-label" for="input-confirmpass"><?php echo lang( 'account_confirm_new_password' ); ?>: <span class="txt_require">*</span></label>
			<div class="controls">
				<input type="password" name="conf_new_password" value="" id="input-confirmpass" />
			</div>
		</div>
	
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo lang( 'account_change_password' ); ?></button>
			</div>
		</div>
	<?php echo form_close(); ?> 
	<?php endif; ?> 
	
</article>