<h1><?php echo lang( 'comment_delete_comment' ); ?></h1>

<p><strong><?php echo lang( 'comment_are_you_sure' ); ?></strong><br />
	<?php echo lang( 'comment_delete_will_delete_child' ); ?>
</p>

<div class="comment-sample-delete">
	<strong><?php echo $subject; ?></strong><br />
	<?php echo $comment_body_value; ?>
</div>

<?php echo form_open( current_url().(isset( $go_to ) ? '?rdr='.$go_to : '' ) ); ?> 
	<input type="hidden" name="confirm" value="yes" />
	
	<button type="submit" class="btn btn-danger"><?php echo lang( 'comment_yes' ); ?></button>
	<button type="button" class="btn" onclick="window.location='<?php echo (isset( $go_to ) ? urldecode( $go_to ) : site_url() ); ?>';"><?php echo lang( 'comment_no' ); ?></button>
<?php echo form_close(); ?> 