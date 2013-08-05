<div class="page-edit-media">
	
	<h1><?php echo sprintf(lang('media_edit_file'), $row->file_name); ?></h1>
	
	<?php echo form_open(); ?> 
		<div class="form-result">
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 
		</div>
		
	
	
		<div class="row-fluid">
			<div class="span12">
				<?php if (strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png') { ?> 
				<?php $media_type = 'image'; ?> 
				
				<div class="image-container">
					<a href="<?php echo base_url().$row->file; ?>" class=" btn btn-small"><span class="icon-zoom-in"></span> <?php echo lang('media_view_full_image'); ?></a>
					<div class="space-break"></div>
					<div class="media-screenshot-placeholder">
						<img src="<?php echo base_url().$row->file; ?>" alt="<?php echo $row->file_original_name; ?>" class="media-screenshot" id="media-image" />
					</div>
				</div>
				
				<?php } else { ?> 
					<?php echo $this->modules_plug->do_filter('media_review', $row->file_id); ?> 
				<?php } //endif; ?> 
			</div>
		</div><!--.row-fluid-->
		
	
	
		<div class="container">
			<div class="row edit-image-row">
				<?php list($width, $height) = getimagesize($row->file); ?> 
					
				<div class="span4">
					<div class="row-fluid">
						<label class="span2" for="resize-width"><?php echo lang('media_width'); ?>: </label>
						<div class="span10">
							<input type="text" name="width" value="<?php echo $width; ?>" class="newwidth input-block-level" id="resize-width" />
						</div>
					</div>
				</div>

				<div class="span4">
					<div class="row-fluid">
						<label class="span2" for="resize-height"><?php echo lang('media_height'); ?>: </label>
						<div class="span10">
							<input type="text" name="height" value="<?php echo $height; ?>" class="newheight input-block-level" id="resize-height" />
						</div>
					</div>
				</div>

				<div class="span4">
					<label class="checkbox inline">
						<input type="checkbox" name="aspect_ratio" value="yes" checked="checked" class="resize-ratio" /><?php echo lang('media_aspect_ratio'); ?>
					</label>

					<button type="button" class="bb-button resize-image btn pull-right" onclick="ajax_resize(<?php echo $row->file_id; ?>);"><?php echo lang('media_resize_now'); ?></button>

					<div class="clearfix"></div>
				</div>
				
			</div><!--.edit-image-row-->
			
			
			<div class="row edit-image-crop-row">
				<div class="span12">
					<!--<strong>X1:</strong><span class="crop_x1"></span>
					<strong>Y1:</strong><span class="crop_y1"></span>
					<strong>X2:</strong><span class="crop_x2"></span>
					<strong>Y2:</strong><span class="crop_y2"></span>-->
					<strong><?php echo lang('media_width'); ?>:</strong><span class="crop_w"></span>
					<strong><?php echo lang('media_height'); ?>:</strong><span class="crop_h"></span>
					
					<input type="hidden" name="crop_x1" value="" class="input_crop_x1" />
					<input type="hidden" name="crop_y1" value="" class="input_crop_y1" />
					<input type="hidden" name="crop_x2" value="" class="input_crop_x2" />
					<input type="hidden" name="crop_y2" value="" class="input_crop_y2" />
					<input type="hidden" name="crop_w" value="" class="input_crop_w" />
					<input type="hidden" name="crop_h" value="" class="input_crop_h" />
					<button type="button" class="btn" onclick="ajax_crop(<?php echo $row->file_id; ?>);"><?php echo lang('media_crop_selected_area'); ?></button>
				</div>
			</div><!--.edit-image-row .edit-image-crop-row-->
			
			
			<div class="row edit-info-row">
				<div class="span12">
					<div class="edit-info-column">

						<div class="control-group">
							<label class="control-label"><?php echo lang('media_upload_by'); ?>: </label>
							<div class="controls">
								<?php echo $row->account_username; ?> 
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="media_name"><?php echo lang('media_name'); ?>: </label>
							<div class="controls">
								<input type="text" name="media_name" value="<?php echo $media_name; ?>" maxlength="255" class="input-block-level" id="media_name" />
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="media_description"><?php echo lang('media_description'); ?>: </label>
							<div class="controls">
								<textarea name="media_description" cols="30" rows="7" class="input-block-level" id="media_description"><?php echo $media_description; ?></textarea>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="media_keywords"><?php echo lang('media_keywords'); ?>: </label>
							<div class="controls">
								<input type="text" name="media_keywords" value="<?php echo $media_keywords; ?>" maxlength="255" class="input-block-level" id="media_keywords" />
							</div>
						</div>

						<div class="control-group">
							<div class="controls">
								<button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button>
							</div>
						</div>

					</div>
				</div>
			</div><!--.edit-info-row-->
		</div><!--.container-->
	<?php echo form_close(); ?> 

</div>

<script type="text/javascript">
	$(document).ready(function() {
		<?php if (isset($media_type) && $media_type == 'image'): ?>
		// start preview auto size
		$('.newwidth').keyup(function() {
			preview_autosize('width');
		});
		$('.newheight').keyup(function() {
			preview_autosize('height');
		});
		// end preview auto size
		
		// show cropping image
		init_jcrop();
		<?php endif; ?> 
	});// jquery
	
	
	<?php if (isset($media_type) && $media_type == 'image') { ?> 
	function ajax_crop(file_id) {
		var crop_x1 = $('.input_crop_x1').val();
		var crop_y1 = $('.input_crop_y1').val();
		var crop_x2 = $('.input_crop_x2').val();
		var crop_y2 = $('.input_crop_y2').val();
		var crop_w = $('.input_crop_w').val();
		var crop_h = $('.input_crop_h').val();
		
		if (crop_x1 == '' || crop_y1 == '' || crop_x2 == '' || crop_y2 == '' || crop_w == '' || crop_h == '') {
			// value not set
			return false;
		}
		
		$.ajax({
			url: site_url+'site-admin/media/ajax_crop',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&file_id='+file_id+'&crop_x1='+crop_x1+'&crop_y1='+crop_y1+'&crop_x2='+crop_x2+'&crop_y2='+crop_y2+'&crop_w='+crop_w+'&crop_h='+crop_h,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					$('.media-screenshot-placeholder').html('<img src="'+data.croped_img+'" alt="" id="media-image" />');
					init_jcrop();
					setTimeout('clear_status()', '3000');
				} else {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					setTimeout('clear_status()', '10000');
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function(data, status, e) {
				// 
			}
		});
		
		return false;
	}// ajax_crop
	<?php } ?> 
	
	
	<?php if (isset($media_type) && $media_type == 'image'): ?>
	function ajax_resize(file_id) {
		var new_height = $('.newheight').val();
		var new_width = $('.newwidth').val();
		$.ajax({
			url: site_url+'site-admin/media/ajax_resize',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&file_id='+file_id+'&width='+new_width+'&height='+new_height,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					$('.media-screenshot-placeholder').html('<img src="'+data.resized_img+'" alt="" id="media-image" />');
					init_jcrop();
					setTimeout('clear_status()', '3000');
				} else {
					$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
					setTimeout('clear_status()', '10000');
					$('body,html').animate({scrollTop: 0}, 800);
				}
			},
			error: function(data, status, e) {
				//
			}
		});
	}// ajax_resize
	<?php endif; ?>

	
	function clear_status() {
		$('.form-result').html('');
	}// clear_status
	
	
	<?php if (isset($media_type) && $media_type == 'image') { ?> 
	function init_jcrop() {
		$('#media-image').Jcrop({
			onChange: showCropCoords,
			onSelect: showCropCoords
		});
	}// init_jcrop
	<?php } ?> 
	
	
	<?php if (isset($media_type) && $media_type == 'image'): ?>
	function preview_autosize(which_size) {
		var aspect_ratio = $('.resize-ratio').is(':checked');
		var orig_height = '<?php echo $height; ?>';
		var orig_width = '<?php echo $width; ?>';
		var new_height = $('.newheight').val();
		var new_width = $('.newwidth').val();
		//
		if (aspect_ratio == true) {
			if (!isNumber(new_height) && which_size == 'height') {
				new_height = 1;
			} else if (!isNumber(new_width) && which_size == 'width') {
				new_width = 1;
			}
			//
			if (which_size == 'height') {
				set_width = Math.round((orig_width/orig_height)*new_height);
				$('.newwidth').val(set_width);
			} else if (which_size == 'width') {
				set_height = Math.round((orig_height/orig_width)*new_width);
				$('.newheight').val(set_height);
			}
		}
	}// preview_autosize
	<?php endif; ?> 
	
	
	function isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}// isNumber
	
	
	function showCropCoords(c) {
		// for show debug.
		$('.crop_x1').text(c.x);
		$('.crop_y1').text(c.y);
		$('.crop_x2').text(c.x2);
		$('.crop_y2').text(c.y2);
		$('.crop_w').text(c.w);
		$('.crop_h').text(c.h);
		
		// for input hidden and send to php
		$('.input_crop_x1').val(c.x);
		$('.input_crop_y1').val(c.y);
		$('.input_crop_x2').val(c.x2);
		$('.input_crop_y2').val(c.y2);
		$('.input_crop_w').val(c.w);
		$('.input_crop_h').val(c.h);
	}
</script>