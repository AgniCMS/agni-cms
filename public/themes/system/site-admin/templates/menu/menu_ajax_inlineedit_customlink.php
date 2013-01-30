<?php echo form_open(); ?> 
<textarea name="custom_link"><?php echo $custom_link; ?></textarea>
<button type="submit" class="bb-button btn btn-primary" onclick="return save_edit_menu_item( '<?php echo $mi_id; ?>', $(this).parent() );"><?php echo lang( 'admin_save' ); ?></button>
<?php echo form_close(); ?> 