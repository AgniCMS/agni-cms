<h1><?php echo lang( 'comment_edit_comment' ); ?></h1>

<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

	<div class="page-add-edit">
		<label><?php echo lang( 'comment_name' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="name" value="<?php echo $name; ?>"<?php if ( $row->account_id != '0' && $row->account_id != null ) {echo ' readonly=""';} ?> maxlength="255" />
		</label>
		<label><?php echo lang( 'comment_email' ); ?>: <input type="text" name="email" value="<?php echo $email; ?>" maxlength="255" /></label>
		<label><?php echo lang( 'comment_homepage' ); ?>: <input type="text" name="homepage" value="<?php echo $homepage; ?>" maxlength="255" /></label>
		<label><?php echo lang( 'comment_subject' ); ?>: <input type="text" name="subject" value="<?php echo $subject; ?>" maxlength="255" /></label>
		<label><?php echo lang( 'comment_comment' ); ?>: <span class="txt_require">*</span>
			<textarea name="comment_body_value" cols="30" rows="10"><?php echo htmlspecialchars( $comment_body_value, ENT_QUOTES, config_item( 'charset' ) ); ?></textarea>
		</label>
		<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
	</div>

<?php echo form_close(); ?> 