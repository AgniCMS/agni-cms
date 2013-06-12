<h1><?php echo lang( 'agni_configure_website' ); ?></h1>


<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

	<fieldset>
		<legend><?php echo lang( 'agni_install_sample_data' ); ?></legend>
		
		<label><?php echo lang( 'agni_install_select_sample_data_set' ); ?>: </label>
		<select name="sample_data" class="sample_data select-inline">
			<option value="agni-sampledata-empty"><?php echo lang( 'agni_install_sample_data_none' ); ?></option>
			<option value="agni-sampledata-simple"><?php echo lang( 'agni_install_sample_data_simple' ); ?></option>
		</select>
		
		<button type="button" class="btn" onclick="ajax_install_sample_data();"><?php echo lang( 'agni_install' ); ?></button><span class="install_sample_data_result"></span>
	</fieldset>
	
	<fieldset>
		<legend><?php echo lang( 'agni_config_site_info' ); ?></legend>
		
		<label><?php echo lang( 'agni_config_site_name' ); ?>: <span class="txt_require">*</span></label>
		<input type="text" name="site_name" value="<?php echo htmlspecialchars( trim( $this->input->post( 'site_name' ) ) ); ?>" class="span6 site_name" />
		
		<label><?php echo lang( 'agni_config_site_email' ); ?>: <span class="txt_require">*</span></label>
		<input type="text" name="sender_email" value="<?php echo trim( $this->input->post( 'sender_email', true ) ); ?>" class="span6 sender_email" />
		<span class="help-block"><?php echo lang( 'agni_config_site_email_sent_from_this_address' ); ?></span>
		
		<label><?php echo lang( 'agni_config_timezone' ); ?>:</label>
		<?php echo timezone_menu( ( $this->input->post( 'timezones' ) == null ? 'UP7' : $this->input->post( 'timezones' ) ), 'span6 timezones' ); ?>
		
	</fieldset>

	<fieldset>
		<legend><?php echo lang( 'agni_config_superadmin_account' ); ?></legend>
		
		<label><?php echo lang( 'agni_config_sa_username' ); ?>: <span class="txt_require">*</span></label>
		<input type="text" name="account_username" value="<?php echo htmlspecialchars( trim( $this->input->post( 'account_username' ) ) ); ?>" class="span6 account_username" />
		
		<label><?php echo lang( 'agni_config_sa_email' ); ?>: <span class="txt_require">*</span></label>
		<input type="text" name="account_email" value="<?php echo trim( $this->input->post( 'account_email', true ) ); ?>" class="span6 account_email" />
		
		<label><?php echo lang( 'agni_config_sa_password' ); ?>: <span class="txt_require">*</span></label>
		<input type="password" name="account_password" value="" class="span3" />
		
		<label><?php echo lang( 'agni_config_sa_confirm_password' ); ?>: <span class="txt_require">*</span></label>
		<input type="password" name="account_cf_password" value="" class="span3" />
		
	</fieldset>
	
	<div class="button-cmd form-actions">
		<div class="next-btn">
			<button type="submit" class="btn btn-primary"><?php echo lang( 'agni_next_step' ); ?></button>
		</div>
		<div class="clear"></div>
	</div>
	
<?php echo form_close(); ?> 


<script type="text/javascript">
	function ajax_install_sample_data() {
		var sample_data_set = $('.sample_data').val();
		$('.install_sample_data_result').html('');
		
		if ( sample_data_set != '' ) {
			$.ajax({
				url: '<?php echo site_url( 'index/ajax_install_sample_data' ); ?>',
				type: 'POST',
				data: ({ <?php echo config_item( 'csrf_token_name' ); ?>:'<?php echo $this->security->get_csrf_hash(); ?>', sample_data:sample_data_set }),
				dataType: 'json',
				success: function(data) {
					if ( data.result == true ) {
						$('.install_sample_data_result').html('<span class="icon-ok"></span>');
					} else {
						$('.install_sample_data_result').html('<span class="icon-remove"></span> '+data.result_text);
					}
				},
				error: function(data) {
					$('.install_sample_data_result').html('<span class="icon-remove"></span> <?php echo lang( 'agni_install_sample_data_error_please_select_none_first' ); ?>');
				}
			});
		}
	}// ajax_install_sample_data
</script>