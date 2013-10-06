<h1><?php echo ($this->uri->segment(3) == 'add' ? lang('post_add_article') : lang('post_edit_article')); ?></h1>

<?php echo form_open(); ?>
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div id="tabs" class="page-tabs post-article-tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo lang('post_info'); ?></a></li>
			<li><a href="#tabs-scriptstyle"><?php echo lang('post_script_style'); ?></a></li>
			<li><a href="#tabs-category"><?php echo lang('post_categories'); ?></a></li>
			<li><a href="#tabs-tag"><?php echo lang('post_tags'); ?></a></li>
			<li><a href="#tabs-2"><?php echo lang('admin_seo'); ?></a></li>
			<li><a href="#tabs-3"><?php echo lang('admin_theme'); ?></a></li>
			<li><a href="#tabs-6"><?php echo lang('post_other_settings'); ?></a></li>
			<?php if ($this->uri->segment(3) == 'edit' && $count_revision > 1): ?><li><a href="#tabs-revision"><?php echo lang('post_revision_history'); ?></a></li><?php endif; ?> 
		</ul>
		
		
		<div id="tabs-1">
			<div class="control-group">
				<label class="control-label" for="post_name"><?php echo lang('post_article_name'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="post_name" value="<?php if (isset($post_name)) {echo $post_name;} ?>" maxlength="255" class="post_name input-block-level" id="post_name" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="body_summary"><?php echo lang('post_summary'); ?>: </label>
				<div class="controls">
					<textarea name="body_summary" class="post-summary" id="body_summary"><?php if (isset($body_summary)) {echo $body_summary;} ?></textarea>
					<span class="help-block"><?php echo lang('admin_html_allowed'); ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="body_value"><?php echo lang('post_content'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<!--insert media-->
					<button type="button" class="btn btn-small insert-media" title="<?php echo lang('post_insert_media'); ?>" onclick="$('#media-popup').dialog('open');"><span class="icon-picture"></span> <?php echo lang('post_insert_media'); ?></button>
					<div id="media-popup" title="<?php echo lang('post_insert_media'); ?>" class="dialog"><iframe name="media-browser" id="media-browser" src="<?php echo site_url('site-admin/media/popup'); ?>" class="media-browser-dialog iframe-in-dialog"></iframe></div>
					<script type="text/javascript">
						$(document).ready(function() {
							$('#media-popup').dialog({
								autoOpen: false,
								height: '600',
								hide: 'fade',
								modal: true,
								show: 'fade',
								width: '960'
							});
						});

						function close_dialog() {
							$(".dialog").dialog("close");
							return false;
						}
					</script>
					<!--end insert media-->
					<?php echo $this->modules_plug->do_filter('post_admin_abovebody'); ?> 
					<textarea name="body_value" class="post-body" id="body_value"><?php if (isset($body_value)) {echo $body_value;} ?></textarea>
					<span class="help-block"><?php echo lang('admin_html_allowed'); ?></span>
					<?php echo $this->modules_plug->do_filter('post_admin_belowbody'); ?> 
				</div>
			</div>
			
			<div id="accordion">
				<h3><a href="#"><?php echo lang('post_revision_information'); ?></a></h3>
				<div>
					<div class="control-group">
						<div class="controls">
							<label class="checkbox inline"><input type="checkbox" name="new_revision" value="1"<?php if (isset($new_revision) && $new_revision == '1') {echo 'checked="checked"';} ?> class="revision-check" /><?php echo lang('post_new_revision'); ?></label>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label label-inline" for="revision_log"><?php echo lang('post_revision_log_msg'); ?>: </label>
						<div class="controls">
							<textarea name="revision_log" class="input-block-level revision-log" id="revision_log"><?php if (isset($revision_log)) {echo $revision_log;} ?></textarea>
						</div>
					</div>
				</div>
				<h3><a href="#"><?php echo lang('post_comment_setting'); ?></a></h3>
				<div>
					<label class="radio"><input type="radio" name="post_comment" value="1"<?php if (isset($post_comment) && $post_comment == '1') {echo ' checked="checked"';} ?> /><?php echo lang('post_comment_on'); ?></label>
					<label class="radio"><input type="radio" name="post_comment" value="0"<?php if (isset($post_comment) && $post_comment == '0') {echo ' checked="checked"';} ?> /><?php echo lang('post_comment_off'); ?></label>
				</div>
				<?php if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm')): ?> 
				<h3><a href="#"><?php echo lang('post_publishing_option'); ?></a></h3>
				<div>
					<label class="checkbox"><input type="checkbox" name="post_status" value="1"<?php if (isset($post_status) && $post_status == '1') {echo ' checked="checked"';} ?> /><?php echo lang('post_published'); ?></label>
				</div>
				<?php endif; ?> 
				<h3><a href="#"><?php echo lang('post_feature_image'); ?></a></h3>
				<div>
					<!--insert media-->
					<button type="button" class="btn btn-small insert-media" title="<?php echo lang('post_select_feature_image'); ?>" onclick="$('#media-popup').dialog('open');"><span class="icon-picture"></span> <?php echo lang('post_select_feature_image'); ?></button>
					<div id="media-popup-feature-img" title="<?php echo lang('post_select_feature_image'); ?>" class="dialog"><iframe name="media-browser" id="media-browser-feature-img" src="<?php echo site_url('site-admin/media/popup'); ?>" class="media-browser-dialog iframe-in-dialog"></iframe></div>
					<script type="text/javascript">
						$(document).ready(function() {
							$('#media-popup-feature-img').dialog({
								autoOpen: false,
								height: '600',
								hide: 'fade',
								modal: true,
								show: 'fade',
								width: '960'
							});
						});

						function close_dialog() {
							$(".dialog").dialog("close");
							return false;
						}
					</script>
					<!--end insert media-->
					<input type="hidden" name="post_feature_image" value="<?php if (isset($post_feature_image)) {echo $post_feature_image;} ?>" id="input-feature-image" />
					<div class="feature-image-img">
						<?php if (isset($post_feature_image) && is_numeric($post_feature_image)): ?> 
						<?php $this->load->module('site-admin/media');
						echo $this->media->get_img($post_feature_image); ?> 
						<div>
							<a href="#" onclick="return remove_feature_image();" class="btn btn-warning btn-mini"><i class="icon-remove"></i> <?php echo lang('post_remove'); ?></a>
						</div>
						<?php endif; ?> 
					</div>
					
				</div>
			</div>
		</div>
		
		
		<div id="tabs-scriptstyle">
			<div>
				<div class="control-group">
					<label class="control-label" for="header_value"><?php echo lang('post_script_or_stylesheet'); ?>:</label>
					<div class="controls">
						<textarea name="header_value" placeholder="<script>...</script>" rows="10" id="header_value" class="post-header-tags input-block-level"><?php if (isset($header_value)) {echo $header_value;} ?></textarea>
					</div>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-category">
			<div class="categories-check-list">
				<?php echo show_category_check($list_category, true, (isset($tid) ? $tid : array())); ?> 
			</div>
		</div>
		
		
		<div id="tabs-tag">
			<div class="added-tags">
				<?php if (isset($tagid) && is_array($tagid)): ?>
				<?php foreach ($tagid as $a_tagid): ?>
				<span class="each-added-tag">
					<?php $this->taxonomy_model->tax_type = 'tag'; echo $this->taxonomy_model->showTaxTermInfo($a_tagid); ?><input type="hidden" name="tagid[]" value="<?php echo $a_tagid; ?>" /><i class="remove-added-tag icon-remove" onclick="added_tag_remove($(this));"></i>
				</span>
				<?php endforeach; ?>
				<?php endif; ?>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="post-tag"><?php echo lang('post_tags'); ?>: </label>
				<div class="controls">
					<input type="text" name="tag" value="" class="input-add-tags input-block-level" onkeypress="return noenter(event);" id="post-tag" />
					<span class="help-block"><?php echo lang('post_tag_usage_comment'); ?></span>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-2">
			<div class="control-group">
				<label class="control-label" for="post_uri"><?php echo lang('admin_uri'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="post_uri" value="<?php if (isset($post_uri)) {echo $post_uri;} ?>" maxlength="200" id="post_uri" class="post_uri input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="meta_title"><?php echo lang('admin_meta_title'); ?>: </label>
				<div class="controls">
					<input type="text" name="meta_title" value="<?php if (isset($meta_title)) {echo $meta_title;} ?>" maxlength="255" id="meta_title" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="meta_description"><?php echo lang('admin_meta_description'); ?>: </label>
				<div class="controls">
					<input type="text" name="meta_description" value="<?php if (isset($meta_description)) {echo $meta_description;} ?>" maxlength="255" id="meta_description" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="meta_keywords"><?php echo lang('admin_meta_keywords'); ?>: </label>
				<div class="controls">
					<input type="text" name="meta_keywords" value="<?php if (isset($meta_keywords)) {echo $meta_keywords;} ?>" maxlength="255" id="meta_keywords" class="input-block-level" />
				</div>
			</div>
		</div>
		
		
		<div id="tabs-3">
			<div class="theme-select">
				<label>
					<img src="<?php echo $this->themes_model->showThemeScreenshot(''); ?>" alt="" /><br />
					<input type="radio" name="theme_system_name" value=""<?php if (!isset($theme_system_name) || (isset($theme_system_name) && $theme_system_name == null)) {echo ' checked="checked"';} ?> /> <?php echo lang('post_no_theme'); ?> 
				</label>
			</div>
			<?php if (isset($list_theme['items'])): ?> 
			<?php foreach ($list_theme['items'] as $row): ?> 
			<div class="theme-select">
				<label>
					<img src="<?php echo $this->themes_model->showThemeScreenshot($row->theme_system_name); ?>" alt="<?php echo $row->theme_name; ?>" /><br />
					<input type="radio" name="theme_system_name" value="<?php echo $row->theme_system_name; ?>"<?php if (isset($theme_system_name) && $theme_system_name == $row->theme_system_name) {echo ' checked="checked"';} ?> /> <?php echo $row->theme_name; ?> 
				</label>
			</div>
			<?php endforeach; ?> 
			<?php endif; ?> 
		</div>
		
		
		<div id="tabs-6">
			<div class="control-group">
				<label class="control-label"><?php echo lang('post_content_show_title'); ?>: </label>
				<div class="controls">
					<select name="content_show_title" class="input-block-level">
						<option value=""><?php echo lang('post_use_default_setting'); ?></option>
						<option value="1"<?php if (isset($content_show_title) && $content_show_title == '1') {echo ' selected="selected"';} ?>><?php echo lang('post_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_title) && $content_show_title == '0') {echo ' selected="selected"';} ?>><?php echo lang('post_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('post_content_show_time'); ?>: </label>
				<div class="controls">
					<select name="content_show_time" class="input-block-level">
						<option value=""><?php echo lang('post_use_default_setting'); ?></option>
						<option value="1"<?php if (isset($content_show_time) && $content_show_time == '1') {echo ' selected="selected"';} ?>><?php echo lang('post_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_time) && $content_show_time == '0') {echo ' selected="selected"';} ?>><?php echo lang('post_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('post_content_show_author'); ?>: </label>
				<div class="controls">
					<select name="content_show_author" class="input-block-level">
						<option value=""><?php echo lang('post_use_default_setting'); ?></option>
						<option value="1"<?php if (isset($content_show_author) && $content_show_author == '1') {echo ' selected="selected"';} ?>><?php echo lang('post_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_author) && $content_show_author == '0') {echo ' selected="selected"';} ?>><?php echo lang('post_no'); ?></option>
					</select>
				</div>
			</div>
			
			<?php echo $this->modules_plug->do_filter('post_admin_bottomtab6'); ?> 
		</div>
		
		
		<?php if ($this->uri->segment(3) == 'edit' && $count_revision > 1): ?> 
		<div id="tabs-revision">
			<?php if (isset($list_revision)): ?> 
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th><?php echo lang('post_author_name'); ?></th>
						<th><?php echo lang('post_content'); ?></th>
						<th><?php echo lang('post_revision_log_msg'); ?></th>
						<th><?php echo lang('post_date'); ?></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th><?php echo lang('post_author_name'); ?></th>
						<th><?php echo lang('post_content'); ?></th>
						<th><?php echo lang('post_revision_log_msg'); ?></th>
						<th><?php echo lang('post_date'); ?></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
			<?php foreach ($list_revision as $rev): ?> 
					<tr>
						<td><?php echo anchor('site-admin/account/edit/'.$rev->account_id, $rev->account_username); ?></td>
						<td><?php echo anchor('post/revision/'.$post_id.'/'.$rev->revision_id, mb_strimwidth(strip_tags($rev->body_value), 0, 90, '...')); ?></td>
						<td><?php echo $rev->log; ?></td>
						<td><?php echo gmt_date('Y-m-d H:i:s', $rev->revision_date_gmt); ?></td>
						<td>
							<?php if ($revision_id == $rev->revision_id) {echo lang('post_current');} else { ?> 
							<?php echo anchor('site-admin/article/revert/'.$post_id.'/'.$rev->revision_id, lang('post_revert')); ?> 
							| <?php echo anchor('site-admin/article/del_rev/'.$post_id.'/'.$rev->revision_id, lang('admin_delete')); ?> 
							<?php } ?> 
						</td>
					</tr>
			<?php endforeach; ?> 
				</tbody>
			</table>
			<?php endif; ?> 
		</div>
		<?php endif; ?> 
		
		
		<div class="ui-tabs-panel button-panel">
			<button type="submit" class="bb-button btn btn-primary" name="button" value="save"><?php echo lang('admin_save'); ?></button>&nbsp;
			<button type="button" class="bb-button btn" name="button" value="preview" id="preview_button" onclick="preview_post($(this));"><?php echo lang('post_preview'); ?></button>
		</div>
	</div>
	
<?php echo form_close(); ?> 

<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/tinymce4/jquery.tinymce.min.js"></script>
<?php /*script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/tiny_mce/jquery.tinymce.js"></script>*/ // this code is for TinyMCE v.3 ?>
<script type="text/javascript">
	make_tabs();
	
	
	$(document).ready(function() {
		$('#accordion').accordion({ 
			autoHeight: false
		});// accordion
		
		
		$('.input-add-tags').keyup(function(e) {
			if (e.keyCode == 13 && $(this).val() != '') {
				input_add_tags($(this).val());
				clear_input_add_tags();
			}
		});// add tag from input
		$('.input-add-tags').autocomplete({
			source: site_url+'site-admin/article/ajax_searchtag',
			select: function(event, ui) {
				//$('.tags-input').html("Selected: " + ui.item.value + " aka " + ui.item.id);// use for debug.
				input_add_tags(ui.item.value, ui.item.id);
				clear_input_add_tags();
				return false;
			}
		});// auto complete tags
		
		
		<?php if ($this->uri->segment(3) == 'add'): ?> 
		// convert from name to uri (php+ajax)
		$(".post_name").keyup(function() {
			var postname_val = $(this).val();
			ajax_check_uri(postname_val);
		});// name to uri
		<?php endif; ?> 


		// check for no duplicate uri while entering
		$(".post_uri").keyup(function() {
			var uri_val = $(this).val();
			delay(function(){ajax_check_uri(uri_val);}, 2000);
		});// check uri
		
		
		$('.revision-log').keyup(function() {
			$('.revision-check').attr('checked', 'checked');
		});// auto check new revision
		
		
		$('.post-body, .post-header-tags').tabby();// use tab in textarea
		
		
		$('.post-summary').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo $this->theme_path; ?>share-js/tinymce4/tinymce.min.js',
			content_css : '<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css',
			// fix bug when open and tinymce not show in first time.
			height: '150px',
			width: '100%',
			
			image_advtab: true,
			remove_script_host: false,
			schema: 'html5',
			theme : "modern",
			plugins: ['code', 'image', 'link', 'textcolor'],
			
			// HTML5 formats
			style_formats : [
				{title: 'Headers', items: [
					{title: 'Header 1', block: 'h1'},
					{title: 'Header 2', block: 'h2'},
					{title: 'Header 3', block: 'h3'},
					{title: 'Header 4', block: 'h4'},
					{title: 'Header 5', block: 'h5'},
					{title: 'Header 6', block: 'h6'}
				]},
			
				{title: 'Inline', items: [
					{title: 'B Bold', inline: 'strong'},
					{title: 'I Italic', inline: 'em'},
					{title: 'U Underline', inline: 'span', styles: {'text-decoration': 'underline'}},
					{title: 'S Strikethrough', inline: 'span', styles: {'text-decoration': 'line-through'}},
					{title: 'x² Superscript', inline: 'sup'},
					{title: 'x₂ Subscript', inline: 'sub'},
					{title: '<> Code', inline: 'code'}
				]},
			
				{title: 'Blocks', items: [
					{title: 'Paragraph', block: 'p'},
					{title: 'Blockquote', block: 'blockquote'},
					{title: 'Div', block: 'div'},
					{title: 'Pre', block: 'pre'}
				]},

				{title: 'Containers', items: [
					{title: 'section', block: 'section', wrapper: true, merge_siblings: false},
					{title: 'article', block: 'article', wrapper: true, merge_siblings: false},
					{title: 'blockquote', block: 'blockquote', wrapper: true},
					{title: 'hgroup', block: 'hgroup', wrapper: true},
					{title: 'aside', block: 'aside', wrapper: true},
					{title: 'figure', block: 'figure', wrapper: true}
				]}
			],
			
			toolbar1: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | forecolor backcolor | link image'
			
			// the source code below for use with TinyMCE v.3 ---------------------------
			/*// Location of TinyMCE script
			script_url : '<?php //echo $this->theme_path; ?>share-js/tiny_mce/tiny_mce.js',
			content_css : '<?php //echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css',
			// fix bug when open and tinymce not show in first time.
			height: '150px',
			width: '100%',
			theme : "advanced",
			plugins: "inlinepopups",
			schema: 'html5',
			
			// HTML5 formats
			style_formats : [
					{title : 'h1', block : 'h1'},
					{title : 'h2', block : 'h2'},
					{title : 'h3', block : 'h3'},
					{title : 'h4', block : 'h4'},
					{title : 'h5', block : 'h5'},
					{title : 'h6', block : 'h6'},
					{title : 'p', block : 'p'},
					{title : 'div', block : 'div'},
					{title : 'pre', block : 'pre'},
					{title : 'section', block : 'section', wrapper: true, merge_siblings: false},
					{title : 'article', block : 'article', wrapper: true, merge_siblings: false},
					{title : 'blockquote', block : 'blockquote', wrapper: true},
					{title : 'hgroup', block : 'hgroup', wrapper: true},
					{title : 'aside', block : 'aside', wrapper: true},
					{title : 'figure', block : 'figure', wrapper: true}
			],
			
			theme_advanced_toolbar_align : "left",
			theme_advanced_toolbar_location : "top",
			theme_advanced_buttons1: "bold, italic , underline , strikethrough, forecolor, backcolor, link, unlink, image, removeformat, code",
			theme_advanced_buttons2: "",
			theme_advanced_buttons3: "",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false*/
			// end source code for TinyMCE v.3 -------------------------------------------
		});// tinymce summary
		
		
		$('.post-body').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo $this->theme_path; ?>share-js/tinymce4/tinymce.min.js',
			content_css : '<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css',
			
			// fix bug when open and tinymce not show in first time.
			height: '400px',
			width: '100%',
			
			convert_urls : true,
			document_base_url : base_url,
			image_advtab: true,
			inline_styles : true,
			preformatted : false,
			relative_urls : false,
			remove_script_host: false,
			schema: 'html5',
			theme : "modern",
			
			plugins: [
					"advlist autolink lists link image charmap print preview hr anchor pagebreak",
					"searchreplace wordcount visualblocks visualchars code fullscreen",
					"insertdatetime media nonbreaking save table contextmenu directionality",
					"emoticons template paste textcolor"
			],
			
			toolbar1: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
			toolbar2: "print preview media | forecolor backcolor emoticons"
			
			// the source code below for use with TinyMCE v.3 ---------------------------
			/*/ Location of TinyMCE script
			script_url : '<?php //echo $this->theme_path; ?>share-js/tiny_mce/tiny_mce.js',
			apply_source_formatting : true,
			content_css : '<?php //echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css',
			convert_urls : false,
			document_base_url : base_url,
			inline_styles : true,
			preformatted : false,
			relative_urls : false,
			// fix bug when open and tinymce not show in first time.
			height: '400px',
			width: '100%',
			// General options
			theme : "advanced",
			plugins : "autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
			schema: 'html5',
			
			// HTML5 formats
			style_formats : [
					{title : 'h1', block : 'h1'},
					{title : 'h2', block : 'h2'},
					{title : 'h3', block : 'h3'},
					{title : 'h4', block : 'h4'},
					{title : 'h5', block : 'h5'},
					{title : 'h6', block : 'h6'},
					{title : 'p', block : 'p'},
					{title : 'div', block : 'div'},
					{title : 'pre', block : 'pre'},
					{title : 'section', block : 'section', wrapper: true, merge_siblings: false},
					{title : 'article', block : 'article', wrapper: true, merge_siblings: false},
					{title : 'blockquote', block : 'blockquote', wrapper: true},
					{title : 'hgroup', block : 'hgroup', wrapper: true},
					{title : 'aside', block : 'aside', wrapper: true},
					{title : 'figure', block : 'figure', wrapper: true}
			],

			// Theme options
			theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_toolbar_align : "left",
			theme_advanced_toolbar_location : "top",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false*/
			// end source code for TinyMCE v.3 -------------------------------------------
		});// tinymce post-body
	});// jquery
	
	
	function added_tag_remove(tag) {
		$(tag).parent().remove();
	}
	
	
	function ajax_check_uri(inputval) {
		$.ajax({
			url: site_url+'site-admin/article/ajax_nameuri',
			type: 'POST',
			data: ({ <?php echo $this->security->get_csrf_token_name(); ?>:csrf_value, post_name:inputval<?php if ($this->uri->segment(3) == 'edit'): ?>, nodupedit:'true', id:'<?php echo $post_id; ?>'<?php endif; ?> }),
			dataType: 'json',
			success: function(data) {
				$('.post_uri').val(data.post_uri);
			},
			error: function(data, status, e) {
				$('.post_uri').val('');
				alert(e);
			}
		});
	}
	
	
	function clear_input_add_tags() {
		$('.input-add-tags').val('');
	}
	
	
	function input_add_tags(tag_name, tag_id) {
		// can't get tag_id, add new tag.
		if (tag_id == '' || tag_id == undefined) {
			$.ajax({
				url: site_url+'site-admin/tag/add',
				type: "POST",
				data: ({ <?php echo $this->security->get_csrf_token_name(); ?>: csrf_value, t_name: tag_name, t_uri: tag_name }),
				dataType: 'json',
				async: false,
				success: function(data) {
					if (data.tid != '') {
						tag_id = data.tid;
					}
					return;
				},
				error: function(data, status, e) {
					alert(e);
				}
			});
		}
		// after add, check tag_id again.
		if (tag_id != '' && tag_id != undefined) {
			$('.added-tags').append('<span class="each-added-tag">'+tag_name+'<input type="hidden" name="tagid[]" value="'+tag_id+'" /><i class="remove-added-tag icon-remove" onclick="added_tag_remove($(this))"></i></span>');
		}
		return false;
	}
	
	
	function preview_post(thisobj) {
		// modify target and action
		$(thisobj).parents('form').attr('target', '_preview').attr('action', site_url+'post/preview');
		$(thisobj).parents('form').submit();
		// restore target and action
		$(thisobj).parents('form').attr('target', '_self').attr('action', '<?php echo current_url(); ?>');
	}
	
	
	function update_feature_image(num) {
		$.ajax({
			url: site_url+'site-admin/media/get_img/'+num,
			type: 'GET',
			success: function(data) {
				$('.feature-image-img').html(data+'<div><a href="#" onclick="return remove_feature_image()" class="btn btn-mini btn-warning"><i class="icon-remove"></i><?php echo lang('post_remove'); ?></a></div>');
			}
		});
	}
	
	// modules plug script
	<?php echo $this->modules_plug->do_filter('post_admin_script'); ?> 
</script>