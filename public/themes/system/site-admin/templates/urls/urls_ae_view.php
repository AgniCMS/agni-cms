<h1><?php if ( $this->uri->segment(3) == 'add' ) {echo lang( 'urls_add' );} else {echo lang( 'urls_edit' );} ?></h1>

<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
	
	<div class=" page-add-edit page-url-redirect-ae">
		<label><?php echo lang( 'urls_uri' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="uri" value="<?php if ( isset( $uri ) ) {echo $uri;} ?>" maxlength="255" class="input-uri" />
			<span class="txt_comment"><?php echo lang( 'urls_uri_comment' ); ?></span>
		</label>
		<label><?php echo lang( 'urls_redirect_to' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="redirect_to" value="<?php if ( isset( $redirect_to ) ) {echo $redirect_to;} ?>" maxlength="255" />
			<span class="txt_comment"><?php echo lang( 'urls_redirect_comment' ); ?></span>
		</label>
		<label><?php echo lang( 'urls_redirect_code' ); ?>: <span class="txt_require">*</span>
			<select name="redirect_code">
				<option value="301"<?php if ( isset( $redirect_code ) && $redirect_code == '301' ) {echo ' selected="selected"';} ?>><?php echo lang( 'urls_redirect_301' ); ?></option>
				<option value="302"<?php if ( isset( $redirect_code ) && $redirect_code == '302' ) {echo ' selected="selected"';} ?>><?php echo lang( 'urls_redirect_302' ); ?></option>
				<option value="303"<?php if ( isset( $redirect_code ) && $redirect_code == '303' ) {echo ' selected="selected"';} ?>><?php echo lang( 'urls_redirect_303' ); ?></option>
			</select>
		</label>
		<button type="submit" class="bb-button standard btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
	</div>
	
<?php echo form_close(); ?> 
<script type="text/javascript">
	$(document).ready(function() {
		// check for no duplicate uri while entering
		$(".input-uri").keyup(function() {
			var uri_val = $(this).val();
			delay(function(){ajax_check_uri(uri_val);}, 2000);
		});// check uri
	});
	
	function ajax_check_uri(inputval) {
		$.ajax({
			url: site_url+'site-admin/urls/ajax_check_uri',
			type: 'POST',
			data: ({ <?php echo $this->security->get_csrf_token_name(); ?>:csrf_value, uri:inputval<?php if ( $this->uri->segment(3) == 'edit' ): ?>, nodupedit:'true', id:'<?php echo $alias_id; ?>'<?php endif; ?> }),
			dataType: 'json',
			success: function( data ) {
				$('.input-uri').val(data.input_uri);
			},
			error: function( data, status, e) {
				$('.input-uri').val('');
				alert( e );
			}
		});
	}
</script>