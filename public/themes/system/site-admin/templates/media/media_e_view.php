<div class="page-edit-media">
	
	<h1><?php echo sprintf( lang( 'media_edit_file' ), $row->file_name ); ?></h1>
	
	<?php echo form_open( '', array( 'class' => 'form-horizontal' ) ); ?> 
		<div class="form-result"><?php if ( isset( $form_status ) ) {echo $form_status;} ?> </div>
		
		<div class="row-fluid">
			<div class="span6">
				<?php if ( strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png' ): ?> 
				<?php $media_type = 'image'; ?> 
				
				<div class="image-container">
					<a href="<?php echo base_url().$row->file; ?>" class="media-screenshot-placeholder"><img src="<?php echo base_url().$row->file; ?>" alt="<?php echo $row->file_original_name; ?>" class="media-screenshot img-polaroid" /></a>
				</div>
				
				
				<div class="row-fluid editing-image-row">
					<?php list( $width, $height ) = getimagesize( $row->file ); ?> 
					
					<div class="span4">
						<div class="row-fluid">
							<label class="span2" for="resize-width"><?php echo lang( 'media_width' ); ?>: </label>
							<div class="span10">
								<input type="text" name="width" value="<?php echo $width; ?>" class="newwidth input-block-level" id="resize-width" />
							</div>
						</div>
					</div>
					
					<div class="span4">
						<div class="row-fluid">
							<label class="span2" for="resize-height"><?php echo lang( 'media_height' ); ?>: </label>
							<div class="span10">
								<input type="text" name="height" value="<?php echo $height; ?>" class="newheight input-block-level" id="resize-height" />
							</div>
						</div>
					</div>
					
					<div class="span4">
						<label class="checkbox inline">
							<input type="checkbox" name="aspect_ratio" value="yes" checked="checked" class="resize-ratio" /><?php echo lang( 'media_aspect_ratio' ); ?>
						</label>
						
						<button type="button" class="bb-button resize-image btn pull-right" onclick="ajax_resize( <?php echo $row->file_id; ?> );"><?php echo lang( 'media_resize_now' ); ?></button>
						
						<div class="clearfix"></div>
					</div>
					
				</div>
				
				<?php else: ?> 
					<?php $this->modules_plug->do_action( 'media_review', $row->file_id ); ?> 
				<?php endif; ?> 
			</div>
		
			<div class="span6">
				<div class="edit-info-column">
					
					<hr class="media-edit-seperate visible-phone visible-tablet" />

					<div class="control-group">
						<label class="control-label"><?php echo lang( 'media_upload_by' ); ?>: </label>
						<div class="controls">
							<?php echo $row->account_username; ?> 
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="media_name"><?php echo lang( 'media_name' ); ?>: </label>
						<div class="controls">
							<input type="text" name="media_name" value="<?php echo $media_name; ?>" maxlength="255" class="input-block-level" id="media_name" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="media_description"><?php echo lang( 'media_description' ); ?>: </label>
						<div class="controls">
							<textarea name="media_description" cols="30" rows="7" class="input-block-level" id="media_description"><?php echo $media_description; ?></textarea>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="media_keywords"><?php echo lang( 'media_keywords' ); ?>: </label>
						<div class="controls">
							<input type="text" name="media_keywords" value="<?php echo $media_keywords; ?>" maxlength="255" class="input-block-level" id="media_keywords" />
						</div>
					</div>

					<div class="control-group">
						<div class="controls">
							<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
						</div>
					</div>
				
				</div>
			</div>
		</div>
	<?php echo form_close(); ?> 

</div>

<script type="text/javascript">
	$(document).ready(function() {
		<?php if ( isset( $media_type ) && $media_type == 'image' ): ?>
		// start preview auto size
		$('.newwidth').keyup(function() {
			preview_autosize( 'width' );
		});
		$('.newheight').keyup(function() {
			preview_autosize( 'height' );
		});
		// end preview auto size
		<?php endif; ?> 
	});// jquery
	
	
	<?php if ( isset( $media_type ) && $media_type == 'image' ): ?>
	function ajax_resize( file_id ) {
		var new_height = $('.newheight').val();
		var new_width = $('.newwidth').val();
		$.ajax({
			url: site_url+'site-admin/media/ajax_resize',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&file_id='+file_id+'&width='+new_width+'&height='+new_height,
			dataType: 'json',
			success: function( data ) {
				if ( data.result == true ) {
					$('.form-result').html(data.form_status);
					$('.media-screenshot-placeholder').html('<img src="'+data.resized_img+'" alt="" />');
					setTimeout('clear_status()', '3000');
				} else {
					$('.form-result').html(data.form_status);
					setTimeout('clear_status()', '10000');
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function( data, status, e ) {
				//
			}
		});
	}// ajax_resize
	<?php endif; ?>

	
	function clear_status() {
		$('.form-result').html('');
	}// clear_status
	
	
	<?php if ( isset( $media_type ) && $media_type == 'image' ): ?>
	function preview_autosize( which_size ) {
		var aspect_ratio = $('.resize-ratio').is(':checked');
		var orig_height = '<?php echo $height; ?>';
		var orig_width = '<?php echo $width; ?>';
		var new_height = $('.newheight').val();
		var new_width = $('.newwidth').val();
		//
		if ( aspect_ratio == true ) {
			if ( !isNumber( new_height ) && which_size == 'height' ) {
				new_height = 1;
			} else if ( !isNumber( new_width ) && which_size == 'width' ) {
				new_width = 1;
			}
			//
			if ( which_size == 'height' ) {
				set_width = Math.round( (orig_width/orig_height)*new_height );
				$('.newwidth').val(set_width);
			} else if ( which_size == 'width' ) {
				set_height = Math.round( (orig_height/orig_width)*new_width );
				$('.newheight').val(set_height);
			}
		}
	}// preview_autosize
	<?php endif; ?> 
	
	
	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}// isNumber
</script>