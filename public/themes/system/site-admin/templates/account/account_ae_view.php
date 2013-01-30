<h1><?php echo ( $this->uri->segment(3) == 'add' ? lang( 'account_add' ) : lang( 'account_edit' ) ); ?></h1>

<?php echo form_open_multipart(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
	
	<div class=" page-add-edit page-account-ae">
		<label><?php echo lang( 'account_username' ); ?>:<input type="text" name="account_username" value="<?php if ( isset( $account_username ) ) {echo $account_username;} ?>" maxlength="255"<?php if ( $this->uri->segment(3) == 'edit' ) {echo ' disabled="disabled"';} ?> /><span class="txt_require">*</span></label>
		<label><?php echo lang( 'account_email' ); ?>:<input type="text" name="account_email" value="<?php if ( isset( $account_email ) ) {echo $account_email;} ?>" maxlength="255" /><span class="txt_require">*</span></label>
		<label><?php echo lang( 'account_password' ); ?>:<input type="password" name="account_password" value="" maxlength="255" /><?php if ( $this->uri->segment(3) == 'add' ): ?><span class="txt_require">*</span><?php else: ?><span class="txt_comment"><?php echo lang( 'account_enter_current_if_change' ); ?></span><?php endif; ?></label>
		
		<?php if ( $this->uri->segment(3) == 'edit' ): ?> 
		<label><?php echo lang( 'account_new_password' ); ?>:<input type="password" name="account_new_password" value="" maxlength="255" /><span class="txt_comment"><?php echo lang( 'account_enter_if_change' ); ?></span></label>
		<?php endif; ?> 
		
		<?php if ( $this->config_model->load_single( 'allow_avatar' ) == '1' && $this->uri->segment(3) == 'edit' ): ?> 
		<label><?php echo lang( 'account_avatar' ); ?>: 
			<?php if ( isset( $account_avatar ) && $account_avatar != null ): ?>
			<?php echo anchor( '#', lang( 'account_delete_avatar' ), array( 'onclick' => 'return ajax_delete_avatar()' ) ); ?> <span class="ajax_delete_avatar_result"></span><br />
			<div class="account-avatar-wrap">
				<img src="<?php echo $this->base_url.$account_avatar; ?>" alt="<?php echo lang( 'account_avatar' ); ?>" class="account-avatar account-avatar-edit" />
			</div>
			<?php endif; ?>
			<input type="file" name="account_avatar" /><br />
			<span class="txt_comment">&lt;= <?php echo $this->config_model->load_single( 'avatar_size' ); ?>KB. <?php echo str_replace( '|', ', ', $this->config_model->load_single( 'avatar_allowed_types' ) ); ?></span>
		</label>
		<?php endif; ?> 
		
		<label><?php echo lang( 'account_fullname' ); ?>:<input type="text" name="account_fullname" value="<?php if ( isset( $account_fullname ) ) {echo $account_fullname;} ?>" maxlength="255" /></label>
		<label><?php echo lang( 'account_birthdate' ); ?>:<input type="text" name="account_birthdate" value="<?php if ( isset( $account_birthdate ) ) {echo $account_birthdate;} ?>" maxlength="10" /><span class="txt_comment"><?php echo lang( 'account_birthdate_format' ); ?></span></label>
		<?php /*<label><?php echo lang( 'account_signature' ); ?>:<textarea name="account_signature" cols="30" rows="5"><?php if ( isset( $account_signature ) ) {echo $account_signature;} ?></textarea></label>*/ // not use? ?>
		<label><?php echo lang( 'account_timezone' ); ?>:<?php echo timezone_menu( (isset($account_timezone) ? $account_timezone : $this->config_model->load_single( 'site_timezone' )), '', 'account_timezone' ); ?></label>
		<label><?php echo lang( 'account_level' ); ?>:
			<select name="level_group_id">
				<option></option>
				<?php if ( isset($list_level['items']) && is_array($list_level['items']) ): ?>
				<?php foreach ( $list_level['items'] as $key ): ?>
				<option value="<?php echo $key->level_group_id; ?>"<?php if( isset($level_group_id) && $level_group_id == $key->level_group_id ): ?> selected="selected"<?php endif; ?>><?php echo $key->level_name; ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<span class="txt_require">*</span>
		</label>
		<label><?php echo lang( 'account_status' ); ?>:
			<select name="account_status" id="account_status">
				<option value="1"<?php if ( isset($account_status) && $account_status == '1' ): ?> selected="selected"<?php endif; ?>><?php echo lang("account_enable"); ?></option>
				<option value="0"<?php if ( isset($account_status) && $account_status == '0' ): ?> selected="selected"<?php endif; ?>><?php echo lang("account_disable"); ?></option>
			</select>
			<span class="txt_require">*</span>
		</label>
		<label class="account_status_text"><?php echo lang( 'account_status_text' ); ?>:<input type="text" name="account_status_text" value="<?php if ( isset( $account_status_text ) ) {echo $account_status_text;} ?>" maxlength="255" /></label>
		<button type="submit" class="bb-button standard btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
	</div>
<?php echo form_close(); ?> 

<script type="text/javascript">
	$(document).ready(function() {
		$("input[name=account_birthdate]").datepicker({ 
			dateFormat: 'yy-mm-dd',
			changeMonth: true,
			changeYear: true,
			yearRange: '1900:'+(new Date).getFullYear()
		});

		$("#account_status").change(function() {
			if ( $(this).val() == '0' ) {
				$(".account_status_text").show();
			} else {
				$(".account_status_text").hide();
			}
		});
		if ( $("#account_status").val() == '0' ) {
			$(".account_status_text").show();
		}
	});// jquery document ready

	<?php if ( $this->config_model->load_single( 'allow_avatar' ) == '1' && $this->uri->segment(3) == 'edit' ): ?> 
	function ajax_delete_avatar() {
		$confirm = confirm( '<?php echo lang( 'account_are_you_sure_delete_avatar' ); ?>' );
		
		// set loading status
		$('.ajax_delete_avatar_result').html('<img src="'+base_url+'public/themes/system/site-admin/images/loading.gif" alt="" />');
		
		if ( $confirm == true ) {
			$.ajax({
				url: site_url+'site-admin/account/delete_avatar',
				type: 'POST',
				data: csrf_name+'='+csrf_value+'&account_id=<?php echo $account_id; ?>',
				dataType: 'json',
				success: function( data ) {
					if ( data.result == true ) {
						$('.account-avatar-wrap').remove();
					}
					$('.ajax_delete_avatar_result').html('');
				},
				error: function( data, status, e ) {
					alert( e );
				}
			});
			return false;
		} else {
			return false;
		}
	}
	<?php endif; ?> 
</script>