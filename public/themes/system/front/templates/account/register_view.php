<article class="general-page-container">
	
	<h1><?php echo lang( 'account_register' ); ?></h1>

	<?php echo form_open( '', array( 'class' => 'form-horizontal' ) ); ?> 
		<div class="form-status-placeholder"><?php if ( isset( $form_status ) ) {echo $form_status;} ?></div>

		<?php if ( !isset( $hide_register_form ) || ( isset( $hide_register_form ) && $hide_register_form == false ) ): ?> 
		<div class="page-account-register">
			
			<div class="control-group">
				<label class="control-label" for="account_username"><?php echo lang( 'account_username' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="account_username" value="<?php if ( isset( $account_username ) ) {echo $account_username;} ?>" maxlength="255" id="account_username" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_email"><?php echo lang( 'account_email' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
				<input type="email" name="account_email" value="<?php if ( isset( $account_email ) ) {echo $account_email;} ?>" maxlength="255" id="account_email" />
				</div>	
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_password"><?php echo lang( 'account_password' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="password" name="account_password" value="" maxlength="255" id="account_password" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_confirm_password"><?php echo lang( 'account_confirm_password' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="password" name="account_confirm_password" value="" maxlength="255" id="account_confirm_password" />
				</div>
			</div>
			
			<div class="control-group">
				<?php if ( $plugin_captcha != null ) {
					echo $plugin_captcha;
				} else { ?> 
				<label class="control-label captcha-field" for="captcha">
					<?php echo lang( 'account_captcha' ); ?>:
				</label>
				<div class="controls">
					<img src="<?php echo $this->base_url; ?>public/images/securimage_show.php" alt="securimage" class="captcha" />
					<a href="#" onclick="$('.captcha').attr( 'src', '<?php echo $this->base_url; ?>public/images/securimage_show.php?' + Math.random() ); return false" tabindex="-1"><img src="<?php echo $this->base_url; ?>public/images/reload.gif" alt="" /></a>
					<div>
						<input type="text" name="captcha" value="<?php if ( isset( $captcha ) ) {echo $captcha;} ?>" class="input-captcha" autocomplete="off" id="captcha" />
					</div>
				</div>
				<?php } ?> 
			</div>

			<?php echo $this->modules_plug->do_action( 'account_register_form_bottom' ); ?> 
			
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo lang( 'account_register' ); ?></button> 
					<?php if ( $this->config_model->load_single( 'member_verification' ) == '1' ) {echo anchor( 'account/resend-activate', lang( 'account_not_get_verify_email' ) );} ?>
				</div>
			</div>
			
		</div>
		<?php endif; ?> 
	<?php echo form_close(); ?> 
	
</article>