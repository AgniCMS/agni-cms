<?php 
	include_once( config_item( 'modules_uri' ).$row->block_file );
	$block_title = ucfirst( $row->block_name );
	if ( class_exists( $row->block_name ) ) {
		$fileobj = new $row->block_name;
		if ( property_exists( $fileobj, 'title' ) ) {
			$block_title = $fileobj->title;
		}
	}
	?> 
<h1><?php echo lang( 'block_block' ) . ': ' . $block_title; ?></h1>

<div class="page-add-edit block-edit">
	
	<div class="cmds">
		<div class="cmd-left">
			<button type="button" class="bb-button btn" onclick="window.location='<?php echo site_url( 'site-admin/block?theme_system_name='.$row->theme_system_name ); ?>';"><?php echo lang( 'block_go_back' ); ?></button>
		</div>
		<div class="clear"></div>
	</div>

	<?php echo form_open(); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
		<input type="hidden" name="block_id" value="<?php echo $row->block_id; ?>" />

		<label><?php echo lang( 'block_theme' ); ?>: <input type="text" name="theme_system_name" value="<?php echo ucfirst( $row->theme_system_name ); ?>" readonly="" disabled="disabled" /></label>
		<label><?php echo lang( 'block_area' ); ?>: <input type="text" name="area_name" value="<?php echo ucfirst( $row->area_name ); ?>" readonly="" disabled="disabled" /></label>
		<?php 
		if ( isset( $fileobj ) && is_object( $fileobj ) ) {
			if ( method_exists( $fileobj, 'block_show_form' ) ) {
				echo $fileobj->block_show_form( $row );
			}
		}
		unset( $fileobj );
		?> 
		<label><?php echo lang( 'block_enable' ); ?>: <input type="checkbox" name="block_status" value="1"<?php if ( isset( $block_status ) && $block_status == '1' ) {echo ' checked="checked"';} ?> /></label>
		<div id="accordion">
			<h3><a href="#"><?php echo lang( 'block_except_uri' ); ?></a></h3>
			<div>
				<textarea name="block_except_uri" class="except-uri"><?php echo $block_except_uri; ?></textarea>
				<span class="txt_comment"><?php echo lang( 'block_except_uri_comment' ); ?></span>
			</div>
			<h3><a href="#"><?php echo lang( 'block_only_uri' ); ?></a></h3>
			<div>
				<textarea name="block_only_uri" class="except-uri"><?php echo $block_only_uri; ?></textarea>
				<span class="txt_comment"><?php echo lang( 'block_only_uri_comment' ); ?></span>
			</div>
		</div>
		<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>

	<?php echo form_close(); ?> 
	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#accordion').accordion({ 
			autoHeight: false
		});// accordion
	});// jquery start
</script>