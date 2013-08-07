<h1><?php echo lang('media_media'); ?></h1>


<div class="row-fluid">
	
	<div class="span4 media-column-list-folder">
		<h3><?php echo lang('media_folder'); ?></h3>
		<button type="button" class="btn btn-small" role="button" data-toggle="modal" data-target="#modal-create-folder"><?php echo lang('media_new_folder'); ?></button>
		<div class="list-folder-container">
			<div class="list-folder-limit-height">
				<?php
				require_once(dirname(__FILE__).'/media_list_folder_recursive_function.php');
				?> 
				<ul class="folder-tree list-folder-tree">
					<li class="<?php if (!empty($list_folder)) {echo 'has-subfolder';} ?><?php if ($current_path == $this->media_filesys->base_dir) {echo ' current-path current active';} ?>">
						<div class="folder-item-container">
							<span class="icon-folder-open"></span> 
							<a href="<?php echo current_url().'?current_path='.urlencode($this->media_filesys->base_dir).($orders != null ? '&amp;orders='.$orders : '').($cur_sort != null ? '&amp;sort='.$cur_sort : '').($filter != null ? '&amp;filter='.$filter : '').($filter_val != null ? '&amp;filter_val='.$filter_val : '').($q != null ?'&amp;q='.$q : ''); ?>" class="folder-link">media</a>
						</div>
				<?php
				$data = $output;
				echo list_folder_recursive($list_folder, $data);
				unset($data);
				?> 
					</li>
				</ul>
			</div>
		</div>
	</div>
	
	<div class="span8 media-column-list-files">
		<div class="cmds">
			<div class="span6">
				<?php echo sprintf(lang('admin_total'), $list_item['total']); ?> 
				| <?php echo anchor('site-admin/media?current_path='.urlencode($current_path).'&amp;orders='.$orders.'&amp;sort='.$cur_sort.'&amp;filter=files.account_id&amp;filter_val='.$my_account_id, lang('media_my_file_only')); ?> 


				<?php if ($this->account_model->checkAdminPermission('media_perm', 'media_upload_perm')): ?> 
				<?php echo form_open_multipart('site-admin/media/upload', array('class' => 'media-upload-form', 'id' => 'form-upload', 'target' => 'upload_target', 'onsubmit' => 'return silent_upload()')); ?> 
					<div id="upload-msg"></div>

					<input type="hidden" name="current_path" value="<?php echo $current_path; ?>" />
					<input type="file" name="file" id="file-selector" />
					<button type="submit" class="bb-button media-upload-button btn btn-primary" id="upload-button"><?php echo lang('media_upload'); ?></button>

					<span class="help-inline">&lt; <?php echo ini_get('upload_max_filesize'); ?></span>

					<iframe id="upload_target" name="upload_target" src="" style="border: none; height: 0; width: 0;"></iframe>
				<?php echo form_close(); ?> 
				<?php endif; ?> 


			</div>
			<div class="span6 cmd-right-align">
				<form method="get" class="form-search">
					<input type="hidden" name="current_path" value="<?php echo $current_path; ?>" />
					<input type="hidden" name="filter" value="<?php echo $filter; ?>" />
					<input type="hidden" name="filter_val" value="<?php echo $filter_val; ?>" />
					<input type="text" name="q" value="<?php echo $q; ?>" maxlength="255" />
					<button type="submit" class="bb-button search-button btn"><?php echo lang('media_search'); ?></button>
				</form>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<h3><?php echo lang('media_files'); ?></h3>
		
		<?php echo form_open('site-admin/media/process_bulk'); ?> 
			<?php if (isset($form_status) && isset($form_status_message)) { ?> 
			<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
			<?php } ?> 

			<div class="list-items-placeholder">
				<?php $this->load->view('site-admin/templates/media/media_ajax_list_view'); ?> 
			</div>

			<div class="cmds">
				<div class="span6">
					<select name="act">
						<option value="" selected="selected"></option>
						<option value="del"><?php echo lang('admin_delete'); ?></option>
						<option value="move_to_folder"><?php echo lang('media_move_to_folder'); ?></option>
					</select>
					<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
				</div>
				<div class="span6 cmd-right-align">
					<?php if (isset($pagination)) {echo $pagination;} ?>
				</div>
				<div class="clearfix"></div>
			</div>

		<?php echo form_close(); ?> 
	</div>
	
</div>


<!-- modal dialog create folder -->
<div id="modal-create-folder" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-create-folder-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="<?php echo lang('media_close'); ?>">×</button>
		<h3 id="modal-create-folder-label"><?php echo lang('media_new_folder'); ?></h3>
	</div>
	<div class="modal-body">
		<div class="form-new-folder-result"></div>
		<div><?php printf(lang('media_new_folder_will_be_create_in'), $current_path); ?></div>
		<input type="hidden" name="current_path" value="<?php echo $current_path; ?>" class="create-folder-currentpath" />
		<?php echo lang('media_folder_name'); ?>: <input type="text" name="folder_name" value="" class="create-folder-name" />
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="return ajax_new_folder();"><?php echo lang('admin_save'); ?></button>
	</div>
</div>
<!-- end modal dialog create folder -->


<!-- model dialog rename folder -->
<div id="modal-rename-folder" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="modal-create-folder-label" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true" title="<?php echo lang('media_close'); ?>">×</button>
		<h3 id="modal-create-folder-label"><?php echo lang('media_rename_folder'); ?></h3>
	</div>
	<div class="modal-body">
		<div class="form-rename-folder-result"></div>
		<div><?php echo lang('media_folder_to_rename'); ?> <span class="folder_to_rename"></span></div>
		<input type="hidden" name="current_path" value="" class="folder-rename" />
		<input type="hidden" name="current_folder" value="" class="current-folder-to-rename" />
		<?php echo lang('media_folder_new_name'); ?>: <input type="text" name="folder_new_name" value="" class="folder-new-name" />
	</div>
	<div class="modal-footer">
		<button class="btn btn-primary" onclick="return ajax_rename_folder();"><?php echo lang('admin_save'); ?></button>
	</div>
</div>
<!-- end model dialog rename folder -->


<script type="text/javascript">
	function ajax_delete_folder(path, thisobj) {
		confirm_val = confirm('<?php echo lang('media_are_you_sure_delete_folder'); ?>'+"\n"+path);
		
		if (confirm_val === true) {
			$.ajax({
				url: '<?php echo site_url('site-admin/media/ajax_delete_folder'); ?>',
				type: 'POST',
				data: csrf_name+'='+csrf_value+'&folder='+path,
				dataType: 'json',
				success: function(data) {
					if (data.result == true) {
						thisobj.parent().parent().parent().remove();
						window.location.reload();
					} else {
						alert('<?php echo lang('media_unable_to_delete_folder'); ?>'+"\n"+path);
					}
				}
			});
		}
		
		return false;
	}// ajax_delete_folder
	
	
	function ajax_new_folder() {
		$.ajax({
			url: '<?php echo site_url('site-admin/media/ajax_new_folder'); ?>',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&current_path='+$('.create-folder-currentpath').val()+'&folder_name='+$('.create-folder-name').val(),
			dataType: 'json',
			success: function(data) {
				if (data.result === true) {
					// done
					$('.form-new-folder-result').html('');
					$('#modal-create-folder').modal('hide');
					window.location.reload();
				} else {
					$('.form-new-folder-result').html('<div class="alert alert-error">'+data.result_text+'</div>');
				}
			}
		});
		
		return false;
	}// ajax_new_folder
	
	
	function ajax_rename_folder() {
		$.ajax({
			url: '<?php echo site_url('site-admin/media/ajax_rename_folder'); ?>',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&current_path='+$('.folder-rename').val()+'&current_folder='+$('.current-folder-to-rename').val()+'&folder_new_name='+$('.folder-new-name').val(),
			dataType: 'json',
			success: function(data) {
				if (data.result === true) {
					// done
					$('.form-rename-folder-result').html('');
					$('#modal-rename-folder').modal('hide');
					window.location.reload();
				} else {
					$('.form-rename-folder-result').html('<div class="alert alert-error">'+data.result_text+'</div>');
				}
			}
		});
	}// ajax_rename_folder
	
	
	function ajax_rename_folder_setup(path, new_name, thisobj) {
		$('.folder_to_rename').text(path);
		$('.folder-rename').val(path);
		$('.current-folder-to-rename').val(new_name);
		$('.folder-new-name').val(new_name);
		$('#modal-rename-folder').modal();
		return false;
	}// ajax_rename_folder_setup	
	
	
	function clear_status() {
		$('#upload-msg').html('');
		// reload list
		$.get(site_url+'site-admin/media?<?php echo $this->input->server('QUERY_STRING'); ?>', function(data) {
			$('.list-items-placeholder').html(data);
		});
	}// clear_status
	
	
	function silent_upload() {
		if ($('#file-selector').val() == '') {
			return false;
		}
		$('#upload-msg').html('<img src="<?php echo $this->theme_path; ?>site-admin/images/loading.gif" alt="" />');
		$('#upload-button').attr('disabled', 'disabled');
		return true;
	}// silent_upload
	
	
	function upload_status(msg) {
		$('#upload-msg').html(msg);
		$('#upload-button').removeAttr('disabled');
		setTimeout('clear_status()', '3000');
		// reset input file
		$('#file-selector').val('');
		$('#file-selector').replaceWith($('#file-selector').clone(true));// for ie
		$('#upload-button').removeAttr('disabled');
	}// upload_status
</script>