<h1><?php echo lang( 'agni_install_db' ); ?></h1>


<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
	<input type="hidden" name="method" value="post" />
	
	<label><?php echo lang( 'agni_db_name' ); ?>: <span class="txt_require">*</span></label>
	<input type="text" name="db_name" value="<?php echo strip_tags( trim( $this->input->post( 'db_name', true ) ) ); ?>" maxlength="255" class="span6 db_name" />
	<span class="help-block"><?php echo lang( 'agni_db_name_must_exist_before_install' ); ?></span>
	
	<label><?php echo lang( 'agni_db_username' ); ?>: <span class="txt_require">*</span></label>
	<input type="text" name="db_username" value="<?php echo strip_tags( trim( $this->input->post( 'db_username', true ) ) ); ?>" maxlength="255" class="span6 db_username" />
	
	<label><?php echo lang( 'agni_db_password' ); ?>: <span class="txt_require">*</span></label>
	<input type="password" name="db_password" value="" maxlength="255" class="span6 db_password" />
	
	<label><?php echo lang( 'agni_db_host' ); ?>:</label>
	<input type="text" name="db_host" value="<?php echo ( $this->input->post( 'db_host' ) != null ? strip_tags( trim( $this->input->post( 'db_host', true ) ) ) : 'localhost' ); ?>" maxlength="255" class="span6 db_host" />
	
	<label><?php echo lang( 'agni_db_port' ); ?>:</label>
	<input type="text" name="db_port" value="<?php echo strip_tags( trim( $this->input->post( 'db_port', true ) ) ); ?>" maxlength="255" class="span6 db_port" />
	
	<label><button type="button" class="btn" onclick="test_db_connection();"><?php echo lang( 'agni_test_db_connect' ); ?></button> <span class="test-result"></span></label>
	
	<label><?php echo lang( 'agni_db_table_prefix' ); ?>:</label>
	<input type="text" name="db_table_prefix" value="<?php echo ( $this->input->post( 'db_table_prefix' ) != null ? strip_tags( trim( $this->input->post( 'db_table_prefix', true ) ) ) : 'an_' ); ?>" maxlength="255" class="span6 db_table_prefix" />
	
	<div class="space-break"></div>

	<div class="button-cmd form-actions">
		<div class="prev-btn">
			<button type="button" onclick="window.location='<?php echo site_url(); ?>';" class="btn btn-inverse"><?php echo lang( 'agni_prev_step' ); ?></button>
		</div>
		<div class="next-btn">
			<button type="submit" class="btn btn-primary"><?php echo lang( 'agni_next_step' ); ?></button>
		</div>
		<div class="clear"></div>
	</div>
	
<?php echo form_close(); ?> 
	
<script>
	
	function test_db_connection() {
		var db_name_val = $('.db_name').val();
		var db_username_val = $('.db_username').val();
		var db_password_val = $('.db_password').val();
		var db_host_val = $('.db_host').val();
		var db_port_val = $('.db_port').val();
		$.ajax({
			url: '<?php echo site_url( 'index/ajax_test_db' ); ?>',
			type: 'POST',
			data: ({ <?php echo config_item( 'csrf_token_name' ); ?>:'<?php echo $this->security->get_csrf_hash(); ?>', db_name:db_name_val, db_username:db_username_val, db_password:db_password_val, db_host:db_host_val, db_port:db_port_val }),
			dataType: 'json',
			success: function( data ) {
				$('.test-result').html( data.result_text );
			},
			error: function( data, status, e) {
				alert( e );
			}
		});
	}
	
</script>