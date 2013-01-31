<h1><?php echo ( $this->uri->segment(3) == 'addgroup' ? lang( 'menu_add_group' ) : lang( 'menu_edit_group' ) ); ?></h1>

<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

	<div class="page-add-edit">
		<label><?php echo lang( 'menu_group_name' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="mg_name" value="<?php echo $mg_name; ?>" maxlength="255" />
		</label>
		<label><?php echo lang( 'menu_group_description' ); ?>: <input type="text" name="mg_description" value="<?php echo $mg_description; ?>" maxlength="255" /></label>
		
		<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
	</div>

<?php echo form_close(); ?> 