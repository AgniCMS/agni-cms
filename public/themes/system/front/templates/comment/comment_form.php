
	<?php echo form_open( current_url().(isset( $go_to ) ? '?rdr='.$go_to : '' ).'#addcomment', array( 'class' => 'form-horizontal' ) ); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
		<input type="hidden" name="cmd" value="post_comment" />
		<input type="hidden" name="post_id" value="<?php echo $post_id; ?>" />
		<input type="hidden" name="parent_id" value="<?php if ( isset( $comment_id ) && $comment_id != null ) {echo $comment_id;} ?>" />
		
		<div class="comment-add-wrapper">
			
			<div class="control-group">
				<label class="control-label" for="input-name"><?php echo lang( 'comment_name' ); ?></label> 
				<input type="hidden" name="account_id" value="<?php echo $account_id; ?>" />
				<div class="controls">
					<?php if ( $account_id == '0' ) { ?> 
					<input type="text" name="name" value="<?php if ( isset( $name ) ) {echo $name;} ?>" maxlength="255" id="input-name" class="input-block-level" />
					<?php } else { ?> 
					<?php $account_username = show_accounts_info( $account_id ); ?> 
					<input type="text" readonly="" name="" value="<?php echo $account_username; ?>" class="input-block-level" />
					<input type="hidden" name="name" value="<?php echo $account_username; ?>" maxlength="255" />
					<?php } //endif; ?> 
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="input-subject"><?php echo lang( 'comment_subject' ); ?></label>
				<div class="controls">
					<input type="text" name="subject" value="<?php if ( isset( $subject ) ) {echo $subject;} ?>" maxlength="255" id="input-subject" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="input-comment-body"><?php echo lang( 'comment_comment' ); ?></label>
				<div class="controls">
					<textarea name="comment_body_value" cols="60" rows="10" id="input-comment-body" class="input-block-level"><?php if ( isset( $comment_body_value ) ) {echo $comment_body_value;} ?></textarea>
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<button type="submit" class="btn btn-primary"><?php echo lang( 'comment_save' ); ?></button>
				</div>
			</div>
		</div>
		
	<?php echo form_close(); ?> 