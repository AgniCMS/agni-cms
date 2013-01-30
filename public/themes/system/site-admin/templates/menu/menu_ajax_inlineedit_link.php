<?php echo form_open(); ?> 
<label><?php echo lang( 'menu_link_text' ); ?>: <span class="txt_require">*</span>
	<input type="text" name="link_text" value="<?php echo $link_text; ?>" />
</label>
<label><?php echo lang( 'menu_link_url' ); ?>: <span class="txt_require">*</span>
	<input type="text" name="link_url" value="<?php echo $link_url; ?>" />
</label>
<button type="submit" class="bb-button btn btn-primary" onclick="return save_edit_menu_item( '<?php echo $mi_id; ?>', $(this).parent() );"><?php echo lang( 'admin_save' ); ?></button>
<?php echo form_close(); ?> 