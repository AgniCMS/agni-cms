<?php $this->load->view( 'site-admin/inc_html_head' ); ?>
		
		
		<?php if ( isset( $browser_check ) && $browser_check != 'yes' ): ?><div class="browser-check-no"><?php echo lang( 'account_get_modern_browser' ); ?></div><?php endif; ?> 
		<div class="login-cloak">
			<div class="login-container">
				<h1><?php echo config_load( 'site_name' ); ?></h1>
				<?php echo form_open( current_url().( isset( $go_to ) ? '?rdr='.$go_to : '' ), array( 'onsubmit' => 'return ajax_admin_login($(this));' ) ); ?> 
					<noscript><div class="txt_error alert alert-error"><?php echo lang( 'account_please_enable_javascript' ); ?></div></noscript>
					<div class="form-status"><?php if ( isset( $form_status ) ) {echo $form_status;} ?></div>
					
					<div class="language"><?php echo language_switch_admin(); ?></div>
					
					<label><?php echo lang( 'account_username' ); ?>:<input type="text" name="username" value="<?php if ( isset( $username ) ) {echo $username;} ?>" class="login-username" /></label>
					<label><?php echo lang( 'account_password' ); ?>:<input type="password" name="password" value="" /></label>
					<label class="captcha-field<?php if ( isset( $show_captcha ) && $show_captcha == true ): ?> show<?php endif; ?>"><?php echo lang( 'account_captcha' ); ?>:<br />
						<img src="<?php echo base_url(); ?>public/images/securimage_show.php" alt="securimage" class="captcha" />
						<a href="#" onclick="$('.captcha').attr( 'src', '<?php echo base_url(); ?>public/images/securimage_show.php?' + Math.random() ); return false" tabindex="-1"><img src="<?php echo base_url(); ?>public/images/reload.gif" alt="" /></a>
						<input type="text" name="captcha" value="<?php if ( isset( $captcha ) ) {echo $captcha;} ?>" class="input-captcha" />
					</label>
					<button type="submit" class="bb-button orange login-button"><?php echo lang( 'account_login' ); ?></button>
					
				<?php echo form_close(); ?> 
				<?php echo $this->modules_plug->do_action( 'admin_login_page' ); ?>
			</div>
			
			<div class="requirement-check">
				<span><?php echo lang( 'admin_webbrowser' ); ?>: <small class="ico-<?php if ( isset( $browser_check ) ) {echo $browser_check;} ?>"></small></span>
				<span><?php echo lang( 'admin_javascript' ); ?>: <small class="ico-no" id="js-check"></small></span>
			</div>
			
			<span class="forget-toggle"><?php echo lang( 'account_forget_userpass' ); ?></span>
			<div class="forget-form">
				<p><?php echo lang( 'account_enter_email_link_you_account_to_reset' ); ?></p>
				<div class="form-status-fpw"></div>
				
				<?php echo form_open( '', array( 'onsubmit' => 'return ajax_admin_fpw($(this));', 'class' => 'form-fpw' ) ); ?>
					<label><?php echo lang( 'account_email' ); ?>:<input type="text" name="email" value="" /></label>
					<label class="captcha-field-fpw"><?php echo lang( 'account_captcha' ); ?>:<br />
						<img src="<?php echo base_url(); ?>public/images/securimage_show.php" alt="securimage" class="captcha" />
						<a href="#" onclick="$('.captcha').attr( 'src', '<?php echo base_url(); ?>public/images/securimage_show.php?' + Math.random() ); return false" tabindex="-1"><img src="<?php echo base_url(); ?>public/images/reload.gif" alt="" /></a>
						<input type="text" name="captcha" value="<?php if ( isset( $captcha ) ) {echo $captcha;} ?>" class="input-captcha captcha-fpw" />
					</label>
					<button type="submit" class="bb-button standard fpw-button"><?php echo lang( 'admin_submit' ); ?></button>
				<?php echo form_close(); ?> 
			</div>
		</div>
		
<?php $this->load->view( 'site-admin/inc_html_foot' ); ?>