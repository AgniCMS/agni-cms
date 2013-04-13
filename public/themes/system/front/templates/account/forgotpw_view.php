<article class="general-page-container">
	
	<h1><?php echo lang( 'account_forget_userpass' ); ?></h1>

	<p><?php echo lang( 'account_enter_email_link_you_account_to_reset' ); ?></p>

	<?php echo form_open( '', array( 'class' => 'form-horizontal' ) ); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?>

		<?php if ( !isset( $hide_form ) || ( isset( $hide_form ) && $hide_form == false ) ) { ?> 

		<div class="control-group">
			<label class="control-label" for="account_email"><?php echo lang( 'account_email' ); ?>: </label>
			<div class="controls">
				<input type="email" name="account_email" value="<?php if ( isset( $account_email ) ) {echo $account_email;} ?>" maxlength="255" id="account_email" />
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
		
		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo lang( 'account_send' ); ?></button>
			</div>
		</div>

		<?php } //endif; ?> 

	<?php echo form_close(); ?> 
	
</article>