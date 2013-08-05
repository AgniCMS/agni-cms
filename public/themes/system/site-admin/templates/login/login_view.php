<?php $this->load->view('site-admin/inc_html_head'); ?>
		
		
		<?php if (isset($browser_check) && $browser_check != 'yes'): ?><div class="browser-check-no"><?php echo lang('account_get_modern_browser'); ?></div><?php endif; ?> 
		<div class="container">
			<div class="row">
				<div class="span4 offset4">
					<div class="login-cloak">
						<div class="login-container">
							<h1><?php echo config_load('site_name'); ?></h1>
							<?php echo form_open(current_url().(isset($go_to) ? '?rdr='.$go_to : ''), array('onsubmit' => 'return ajax_admin_login($(this));', 'class' => 'form-full-width')); ?> 
								<noscript><div class="txt_error alert alert-error"><?php echo lang('account_please_enable_javascript'); ?></div></noscript>
								<div class="form-status">
									<?php if (isset($form_status) && isset($form_status_message)) { ?> 
									<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
									<?php } ?> 
								</div>

								<div class="control-group">
									<div class="controls">
										<div class="language"><?php echo language_switch_admin(); ?></div>
									</div>
								</div>

								<div class="control-group">
									<label class="control-label" for="username"><?php echo lang('account_username'); ?>: </label>
									<div class="controls">
										<input type="text" name="username" value="<?php if (isset($username)) {echo $username;} ?>" class="login-username input-block-level" id="username" />
									</div>
								</div>
								
								<div class="control-group">
									<label class="control-label" for="password"><?php echo lang('account_password'); ?>: </label>
									<div class="controls">
										<input type="password" name="password" value="" class="input-block-level" id="password" />
									</div>
								</div>
								
								<div class="control-group captcha-field<?php if (isset($show_captcha) && $show_captcha == true): ?> show<?php endif; ?>">
									<label class="control-label" for="captcha"><?php echo lang('account_captcha'); ?>: </label>
									<div class="controls">
										<img src="<?php echo base_url(); ?>public/images/securimage_show.php" alt="securimage" class="captcha" />
										<a href="#" onclick="$('.captcha').attr('src', '<?php echo base_url(); ?>public/images/securimage_show.php?' + Math.random()); return false" tabindex="-1"><img src="<?php echo base_url(); ?>public/images/reload.gif" alt="" /></a>
										<input type="text" name="captcha" value="<?php if (isset($captcha)) {echo $captcha;} ?>" class="input-captcha" id="captcha" />
									</div>
								</div>
								
								<div class="control-group">
									<div class="controls">
										<button type="submit" class="bb-button orange login-button btn btn-primary"><?php echo lang('account_login'); ?></button>
										<span class="ajax_status"></span>
									</div>
								</div>

							<?php echo form_close(); ?> 
							<?php echo $this->modules_plug->do_filter('admin_login_page'); ?>
						</div>


						<div class="requirement-check">
							<span><?php echo lang('admin_webbrowser'); ?>: <small class="icon-<?php if (isset($browser_check)) {echo str_replace(array('yes', 'no', 'unknow'), array('ok', 'remove', 'exclamation-sign'), $browser_check);} ?>"></small></span>
							<span><?php echo lang('admin_javascript'); ?>: <small class="icon-remove" id="js-check"></small></span>
						</div>
						

						<span class="forget-toggle"><?php echo lang('account_forget_userpass'); ?></span>
						<div class="forget-form">
							<p><?php echo lang('account_enter_email_link_you_account_to_reset'); ?></p>
							<div class="form-status-fpw"></div>

							<?php echo form_open('', array('onsubmit' => 'return ajax_admin_fpw($(this));', 'class' => 'form-fpw')); ?>
								<div class="control-group">
									<label class="control-label" for="fpw-email"><?php echo lang('account_email'); ?>: </label>
									<div class="controls">
										<input type="text" name="email" value="" class="input-block-level" id="fpw-email" />
									</div>
								</div>
								
								<div class="control-group captcha-field-fpw">
									<label class="control-label" for="fpw-captcha"><?php echo lang('account_captcha'); ?>: </label>
									<div class="controls">
										<img src="<?php echo base_url(); ?>public/images/securimage_show.php" alt="securimage" class="captcha" />
										<a href="#" onclick="$('.captcha').attr('src', '<?php echo base_url(); ?>public/images/securimage_show.php?' + Math.random()); return false" tabindex="-1"><img src="<?php echo base_url(); ?>public/images/reload.gif" alt="" /></a>
										<input type="text" name="captcha" value="<?php if (isset($captcha)) {echo $captcha;} ?>" class="input-captcha captcha-fpw input-block-level" id="fpw-captcha" />
									</div>
								</div>
							
								<div class="control-group">
									<div class="controls">
										<button type="submit" class="bb-button standard fpw-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
										<span class="ajax_fpw_status"></span>
									</div>
								</div>
							<?php echo form_close(); ?> 
						</div>
						
						
					</div><!--.login-cloak-->
				</div><!--.span-->
			</div><!--.row-->
		</div><!--.container-->
		
<?php $this->load->view('site-admin/inc_html_foot'); ?>