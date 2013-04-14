<article class="general-page-container">
	
	<h1><?php echo lang( 'account_edit_profile' ); ?></h1>

	<?php echo form_open_multipart( '', array( 'class' => 'form-horizontal' ) ); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

		<div class="page-edit-profile">
			<div class="control-group">
				<label class="control-label" for="account_username"><?php echo lang( 'account_username' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="account_username" value="<?php if ( isset( $account_username ) ) {echo $account_username;} ?>" maxlength="255" disabled="disabled" id="account_username" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_email"><?php echo lang( 'account_email' ); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="email" name="account_email" value="<?php if ( isset( $account_email ) ) {echo $account_email;} ?>" maxlength="255" id="account_email" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_password"><?php echo lang( 'account_password' ); ?>: </label>
				<div class="controls">
					<input type="password" name="account_password" value="" maxlength="255" id="account_password" />
					<?php if ( $this->uri->segment(3) == 'add' ): ?><span class="txt_require">*</span><?php else: ?><span class="help-inline"><?php echo lang( 'account_enter_current_if_change' ); ?></span><?php endif; ?> 
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_new_password"><?php echo lang( 'account_new_password' ); ?>: </label>
				<div class="controls">
					<input type="password" name="account_new_password" value="" maxlength="255" id="account_new_password" />
					<span class="help-inline"><?php echo lang( 'account_enter_if_change' ); ?></span>
				</div>
			</div>
			
			<?php if ( $allow_avatar == '1' ): ?> 
			<div class="control-group">
				<label class="control-label"><?php echo lang( 'account_avatar' ); ?>: </label>
				<div class="controls">
					<?php if ( isset( $account_avatar ) && $account_avatar != null ): ?> 
						<div class="account-avatar-wrap">
							<?php echo anchor( '#', lang( 'account_delete_avatar' ), array( 'onclick' => 'return ajax_delete_avatar();' ) ); ?><span class="remove-status-container"></span><br />
							<img src="<?php echo $this->base_url.$account_avatar; ?>" alt="<?php echo lang( 'account_avatar' ); ?>" class="account-avatar account-avatar-edit" />
						</div>
					<?php endif; ?> 
					<input type="file" name="account_avatar" />
					<span class="help-inline">&lt;= <?php echo $avatar_size; ?>KB. <?php echo str_replace( '|', ', ', $avatar_allowed_types ); ?></span>
				</div>
			</div>
			<?php endif; ?> 
			
			<div class="control-group">
				<label class="control-label" for="account_fullname"><?php echo lang( 'account_fullname' ); ?>: </label>
				<div class="controls">
					<input type="text" name="account_fullname" value="<?php if ( isset( $account_fullname ) ) {echo $account_fullname;} ?>" maxlength="255" id="account_fullname" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="account_birthdate"><?php echo lang( 'account_birthdate' ); ?>: </label>
				<div class="controls">
					<input type="date" name="account_birthdate" value="<?php if ( isset( $account_birthdate ) ) {echo $account_birthdate;} ?>" maxlength="10" id="account_birthdate" />
					<span class="help-inline"><?php echo lang( 'account_birthdate_format' ); ?></span>
				</div>
			</div>
			
			<?php /*<label class="form-label"><?php echo lang( 'account_signature' ); ?>:<textarea name="account_signature" cols="30" rows="5"><?php if ( isset( $account_signature ) ) {echo $account_signature;} ?></textarea></label>*/ // not use? ?>
			<div class="control-group">
				<label class="control-label"><?php echo lang( 'account_timezone' ); ?>: </label>
				<div class="controls">
					<?php echo timezone_menu( (isset($account_timezone) ? $account_timezone : $this->config_model->load_single( 'site_timezone' )), '', 'account_timezone' ); ?> 
				</div>
			</div>

			<?php echo $this->modules_plug->do_action( 'account_edit_profile_form_bottom' ); ?> 
			
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo lang( 'account_save' ); ?></button> <?php echo anchor( 'account/view-logins', lang( 'account_view_logins' ) ); ?>
				</div>
			</div>
		</div>
	<?php echo form_close(); ?> 
	
</article>

<script type="text/javascript">
$(document).ready(function() {
	$("input[name=account_birthdate]").datepicker({ 
		dateFormat: 'yy-mm-dd',
		changeMonth: true,
		changeYear: true,
		yearRange: '1900:'+(new Date).getFullYear()
	});
});// jquery document ready

function ajax_delete_avatar() {
	$confirm = confirm( '<?php echo lang( 'account_are_you_sure_delete_avatar' ); ?>' );
	
	if ( $confirm == true ) {
		$('.remove-status-container').html('<img src="<?php echo $this->theme_path; ?>site-admin/images/loading.gif" alt="" />');
		
		$.ajax({
			url: site_url+'account/edit-profile/delete-avatar',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&account_id=<?php echo $account_id; ?>',
			dataType: 'json',
			success: function( data ) {
				if ( data.result == true ) {
					$('.account-avatar-wrap').remove();
				}
				$('.remove-status-container').html('');
			},
			error: function( data, status, e ) {
				alert( e );
				$('.remove-status-container').html('');
			}
		});
		return false;
	} else {
		return false;
	}
}
</script>