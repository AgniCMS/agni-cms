<article class="general-page-container">
	
	<h1><?php echo lang( 'account_login' ); ?></h1>

	<?php echo form_open( current_url().( isset( $go_to ) ? '?rdr='.$go_to : '' ), array( 'class' => 'form-horizontal' ) ); ?> 
	
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

		<div class="control-group">
			<label class="control-label" for="account_username"><?php echo lang( 'account_username' ); ?>: </label>
			<div class="controls">
				<input type="text" name="account_username" value="<?php if ( isset( $account_username ) ) {echo $account_username;} ?>" maxlength="255" id="account_username" />
			</div>
		</div>
	
		<div class="control-group">
			<label class="control-label" for="account_password"><?php echo lang( 'account_password' ); ?>: </label>
			<div class="controls">
				<input type="password" name="account_password" maxlength="255" id="account_password" />
			</div>
		</div>
	
		<div class="control-group">
			<div class="controls">
				<label class="checkbox inline"><input type="checkbox" name="remember" value="yes" /> <?php echo lang( 'account_remember_my_login' ); ?></label>
			</div>
		</div>

		
		<?php if ( isset( $show_captcha ) && $show_captcha == true ): ?> 
		<div class="control-group">
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
		</div>
		<?php endif; ?> 
		

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary"><?php echo lang( 'account_login' ); ?></button> <?php echo anchor( 'account/forgotpw', lang( 'account_forget_userpass' ) ); ?> 
			</div>
		</div>

	<?php echo form_close(); ?> 

</article>