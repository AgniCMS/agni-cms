<h1><?php echo lang( 'modules_add' ); ?></h1>

<?php echo form_open_multipart(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
	
	<input type="hidden" name="upload" value="module-zip" />
	<label>
		<?php echo lang( 'modules_select_file' ); ?>: <input type="file" name="module_file" />
		<span class="txt_require">*</span>
		<span class="txt_comment">&lt;= <?php echo ini_get( 'upload_max_filesize' ); ?>; <?php echo lang( 'modules_add_comment' ); ?></span>
	</label>
	<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
<?php echo form_close(); ?> 