<h1><?php echo lang('media_move_file'); ?></h1>


<?php echo form_open('site-admin/media/move_file', array('class' => 'form-inline')); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="page-add-edit">
		<?php if (isset($files) && is_array($files) && !empty($files)) { ?> 
		<ul>
			<?php foreach ($files as $row) { ?> 
			<li>
				<?php echo base_url($row->file); ?> 
				<input type="hidden" name="id[]" value="<?php echo $row->file_id; ?>" />
			</li>
			<?php } // endforeach; ?> 
		</ul>
		<?php } ?> 
		
		<hr />
		
		<p><?php echo lang('media_you_are_moving_these_files_to'); ?></p>
		<select name="target_folder">
			<option value=""></option>
			<option value="<?php echo $this->media_filesys->base_dir; ?>">media</option>
			<?php
			require_once(dirname(__FILE__).'/media_list_folder_recursive_function.php');
			echo list_folder_recursive_selectbox($list_folder, array('selected_option' => $target_folder));
			?> 
		</select>
		<button type="submit" class="btn btn-primary"><?php echo lang('media_move'); ?></button>
		
	</div>
<?php echo form_close(); ?> 


