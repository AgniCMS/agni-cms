<h1><?php echo ( $this->uri->segment(3) == 'add' ? lang( 'post_add_page' ) : lang( 'post_edit_page' ) ); ?></h1>

<?php echo form_open(); ?>
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?>

	<div id="tabs" class="page-tabs post-article-tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo lang( 'post_info' ); ?></a></li>
			<li><a href="#tabs-scriptstyle"><?php echo lang( 'post_script_style' ); ?></a></li>
			<li><a href="#tabs-2"><?php echo lang( 'admin_seo' ); ?></a></li>
			<li><a href="#tabs-3"><?php echo lang( 'admin_theme' ); ?></a></li>
			<li><a href="#tabs-6"><?php echo lang( 'post_other_settings' ); ?></a></li>
			<?php if ( $this->uri->segment(3) == 'edit' && $count_revision > 1 ): ?><li><a href="#tabs-revision"><?php echo lang( 'post_revision_history' ); ?></a></li><?php endif; ?> 
		</ul>
		
		
		<div id="tabs-1">
			<label><?php echo lang( 'post_page_name' ); ?>:<span class="txt_require">*</span><input type="text" name="post_name" value="<?php if ( isset( $post_name ) ) {echo $post_name;} ?>" maxlength="255" class="post_name" /></label>
			<label><?php echo lang( 'post_summary' ); ?>:
				<textarea name="body_summary" class="post-summary"><?php if ( isset( $body_summary ) ) {echo $body_summary;} ?></textarea>
				<span class="txt_comment"><?php echo lang( 'admin_html_allowed' ); ?></span>
			</label>
			<label><?php echo lang( 'post_content' ); ?>:<span class="txt_require">*</span>
				<!--insert media-->
				<span class="ico16-media-insert insert-media" title="<?php echo lang( 'post_insert_media' ); ?>" onclick="$('#media-popup').dialog('open');"><?php echo lang( 'post_insert_media' ); ?></span>
				<div id="media-popup" title="<?php echo lang( 'post_insert_media' ); ?>" class="dialog"><iframe name="media-browser" id="media-browser" src="<?php echo site_url( 'site-admin/media/popup' ); ?>" class="media-browser-dialog iframe-in-dialog"></iframe></div>
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
				<?php echo $this->modules_plug->do_action( 'post_admin_abovebody' ); ?> 
				<textarea name="body_value" class="post-body"><?php if ( isset( $body_value ) ) {echo $body_value;} ?></textarea>
				<span class="txt_comment"><?php echo lang( 'admin_html_allowed' ); ?></span>
				<?php echo $this->modules_plug->do_action( 'post_admin_belowbody' ); ?> 
			</label>
			<div id="accordion">
				<h3><a href="#"><?php echo lang( 'post_revision_information' ); ?></a></h3>
				<div>
					<label><input type="checkbox" name="new_revision" value="1"<?php if ( isset( $new_revision ) && $new_revision == '1' ) {echo 'checked="checked"';} ?> class="revision-check" /><?php echo lang( 'post_new_revision' ); ?></label>
					<label class="label-inline">
						<?php echo lang( 'post_revision_log_msg' ); ?>
						<textarea name="revision_log" class="revision-log"><?php if ( isset( $revision_log ) ) {echo $revision_log;} ?></textarea>
					</label>
				</div>
				<h3><a href="#"><?php echo lang( 'post_comment_setting' ); ?></a></h3>
				<div>
					<label><input type="radio" name="post_comment" value="1"<?php if ( isset( $post_comment ) && $post_comment == '1' ) {echo ' checked="checked"';} ?> /><?php echo lang( 'post_comment_on' ); ?></label>
					<label class="label-inline"><input type="radio" name="post_comment" value="0"<?php if ( isset( $post_comment ) && $post_comment == '0' ) {echo ' checked="checked"';} ?> /><?php echo lang( 'post_comment_off' ); ?></label>
				</div>
				<?php if ( $this->account_model->check_admin_permission( 'post_page_perm', 'post_page_publish_unpublish_perm' ) ): ?> 
				<h3><a href="#"><?php echo lang( 'post_publishing_option' ); ?></a></h3>
				<div>
					<label class="label-inline"><input type="checkbox" name="post_status" value="1"<?php if ( isset( $post_status ) && $post_status == '1' ) {echo ' checked="checked"';} ?> /><?php echo lang( 'post_published' ); ?></label>
				</div>
				<?php endif; ?> 
				<h3><a href="#"><?php echo lang( 'post_feature_image' ); ?></a></h3>
				<div>
					<input type="hidden" name="post_feature_image" value="<?php if ( isset( $post_feature_image ) ) {echo $post_feature_image;} ?>" id="input-feature-image" />
					<div class="feature-image-img">
						<?php if ( isset( $post_feature_image ) && is_numeric( $post_feature_image ) ): ?> 
						<?php $this->load->module( 'site-admin/media' );
						echo $this->media->get_img( $post_feature_image ); ?> 
						<div>
							<a href="#" onclick="return remove_feature_image()"><?php echo lang( 'post_remove' ); ?></a>
						</div>
						<?php endif; ?> 
					</div>
					
				</div>
			</div>
		</div>
		
		
		<div id="tabs-scriptstyle">
			<label>
				<?php echo lang( 'post_script_or_stylesheet' ); ?>:
				<textarea name="header_value" class="post-header-tags" placeholder="<script>...</script>"><?php if ( isset( $header_value ) ) {echo $header_value;} ?></textarea>
			</label>
		</div>
		
		
		<div id="tabs-2">
			<label><?php echo lang( 'admin_uri' ); ?>:<span class="txt_require">*</span><input type="text" name="post_uri" value="<?php if ( isset( $post_uri ) ) {echo $post_uri;} ?>" maxlength="200" class="post_uri" /></label>
			<label><?php echo lang( 'admin_meta_title' ); ?>:<input type="text" name="meta_title" value="<?php if ( isset( $meta_title ) ) {echo $meta_title;} ?>" maxlength="255" /></label>
			<label><?php echo lang( 'admin_meta_description' ); ?>:<input type="text" name="meta_description" value="<?php if ( isset( $meta_description ) ) {echo $meta_description;} ?>" maxlength="255" /></label>
			<label><?php echo lang( 'admin_meta_keywords' ); ?>:<input type="text" name="meta_keywords" value="<?php if ( isset( $meta_keywords ) ) {echo $meta_keywords;} ?>" maxlength="255" /></label>
		</div>
		
		
		<div id="tabs-3">
			<div class="theme-select">
				<label>
					<img src="<?php echo $this->themes_model->show_theme_screenshot( '' ); ?>" alt="" /><br />
					<input type="radio" name="theme_system_name" value=""<?php if ( !isset( $theme_system_name ) || ( isset( $theme_system_name ) && $theme_system_name == null ) ) {echo ' checked="checked"';} ?> /><?php echo lang( 'post_no_theme' ); ?>
				</label>
			</div>
			<?php if ( isset( $list_theme['items'] ) ): ?>
			<?php foreach ( $list_theme['items'] as $row ): ?>
			<div class="theme-select">
				<label>
					<img src="<?php echo $this->themes_model->show_theme_screenshot( $row->theme_system_name ); ?>" alt="<?php echo $row->theme_name; ?>" /><br />
					<input type="radio" name="theme_system_name" value="<?php echo $row->theme_system_name; ?>"<?php if ( isset( $theme_system_name ) && $theme_system_name == $row->theme_system_name ) {echo ' checked="checked"';} ?> /><?php echo $row->theme_name; ?>
				</label>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
		</div>
		
		
		<div id="tabs-6">
			<label><?php echo lang( 'post_content_show_title' ); ?>:
				<select name="content_show_title">
					<option value=""><?php echo lang( 'post_use_default_setting' ); ?></option>
					<option value="1"<?php if ( isset( $content_show_title ) && $content_show_title == '1' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_yes' ); ?></option>
					<option value="0"<?php if ( isset( $content_show_title ) && $content_show_title == '0' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_no' ); ?></option>
				</select>
			</label>
			<label><?php echo lang( 'post_content_show_time' ); ?>:
				<select name="content_show_time">
					<option value=""><?php echo lang( 'post_use_default_setting' ); ?></option>
					<option value="1"<?php if ( isset( $content_show_time ) && $content_show_time == '1' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_yes' ); ?></option>
					<option value="0"<?php if ( isset( $content_show_time ) && $content_show_time == '0' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_no' ); ?></option>
				</select>
			</label>
			<label><?php echo lang( 'post_content_show_author' ); ?>:
				<select name="content_show_author">
					<option value=""><?php echo lang( 'post_use_default_setting' ); ?></option>
					<option value="1"<?php if ( isset( $content_show_author ) && $content_show_author == '1' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_yes' ); ?></option>
					<option value="0"<?php if ( isset( $content_show_author ) && $content_show_author == '0' ) {echo ' selected="selected"';} ?>><?php echo lang( 'post_no' ); ?></option>
				</select>
			</label>
			<?php echo $this->modules_plug->do_action( 'post_admin_bottomtab6' ); ?> 
		</div>
		
		
		<?php if ( $this->uri->segment(3) == 'edit' && $count_revision > 1 ): ?> 
		<div id="tabs-revision">
			<?php if ( isset( $list_revision ) ): ?> 
			<table class="list-items">
				<thead>
					<tr>
						<th><?php echo lang( 'post_author_name' ); ?></th>
						<th><?php echo lang( 'post_content' ); ?></th>
						<th><?php echo lang( 'post_revision_log_msg' ); ?></th>
						<th><?php echo lang( 'post_date' ); ?></th>
						<th></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th><?php echo lang( 'post_author_name' ); ?></th>
						<th><?php echo lang( 'post_content' ); ?></th>
						<th><?php echo lang( 'post_revision_log_msg' ); ?></th>
						<th><?php echo lang( 'post_date' ); ?></th>
						<th></th>
					</tr>
				</tfoot>
				<tbody>
			<?php foreach ( $list_revision as $rev ): ?> 
					<tr>
						<td><?php echo anchor( 'site-admin/account/edit/'.$rev->account_id, $rev->account_username ); ?></td>
						<td><?php echo anchor( 'post/revision/'.$post_id.'/'.$rev->revision_id, mb_strimwidth( strip_tags( $rev->body_value ), 0, 90, '...' ) ); ?></td>
						<td><?php echo $rev->log; ?></td>
						<td><?php echo gmt_date( 'Y-m-d H:i:s', $rev->revision_date_gmt ); ?></td>
						<td>
							<?php if ( $revision_id == $rev->revision_id ) {echo lang( 'post_current' );} else { ?> 
							<?php echo anchor( 'site-admin/page/revert/'.$post_id.'/'.$rev->revision_id, lang( 'post_revert' ) ); ?> 
							| <?php echo anchor( 'site-admin/page/del_rev/'.$post_id.'/'.$rev->revision_id, lang( 'admin_delete' ) ); ?> 
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
			<button type="submit" class="bb-button" name="button" value="save"><?php echo lang( 'admin_save' ); ?></button>&nbsp;
			<button type="button" class="bb-button" name="button" value="preview" id="preview_button" onclick="preview_post($(this));"><?php echo lang( 'post_preview' ); ?></button>
		</div>
	</div>
	
<?php echo form_close(); ?> 

<script type="text/javascript" src="<?php echo base_url(); ?>public/js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
	make_tabs();
	
	$(document).ready(function() {
		$('#accordion').accordion({ 
			autoHeight: false
		});// accordion
		
		<?php if ( $this->uri->segment(3) == 'add' ): ?> 
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
			script_url : base_url+'public/js/tiny_mce/tiny_mce.js',
			content_css : '<?php echo $this->theme_path; ?>front/style.css',
			// fix bug when open and tinymce not show in first time.
			height: '150px',
			width: '100%',
			theme : "advanced",
			plugins: "inlinepopups",
			theme_advanced_toolbar_align : "left",
			theme_advanced_toolbar_location : "top",
			theme_advanced_buttons1: "bold, italic , underline , strikethrough, forecolor, backcolor, link, unlink, image, removeformat, code",
			theme_advanced_buttons2: "",
			theme_advanced_buttons3: "",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false
		});// tinymce summary
		$('.post-body').tinymce({
			// Location of TinyMCE script
			script_url : base_url+'public/js/tiny_mce/tiny_mce.js',
			apply_source_formatting : true,
			content_css : '<?php echo $this->theme_path; ?>front/style.css',
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

			// Theme options
			theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,|,forecolor,backcolor",
			theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,ltr,rtl,|,fullscreen",
			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_toolbar_align : "left",
			theme_advanced_toolbar_location : "top",
			theme_advanced_resizing : true,
			theme_advanced_resize_horizontal : false
		});// tinymce post-body
	});// jquery
	
	
	function ajax_check_uri(inputval) {
		$.ajax({
			url: site_url+'site-admin/page/ajax_nameuri',
			type: 'POST',
			data: ({ <?php echo $this->security->get_csrf_token_name(); ?>:csrf_value, post_name:inputval<?php if ( $this->uri->segment(3) == 'edit' ): ?>, nodupedit:'true', id:'<?php echo $post_id; ?>'<?php endif; ?> }),
			dataType: 'json',
			success: function( data ) {
				$('.post_uri').val(data.post_uri);
			},
			error: function( data, status, e) {
				$('.post_uri').val('');
				alert( e );
			}
		});
	}
	
	
	function preview_post( thisobj ) {
		// modify target and action
		$(thisobj).parents('form').attr('target', '_preview').attr('action', site_url+'post/preview');
		$(thisobj).parents('form').submit();
		// restore target and action
		$(thisobj).parents('form').attr('target', '_self').attr('action', '<?php echo current_url(); ?>');
	}
	
	
	function update_feature_image( num ) {
		$.ajax({
			url: site_url+'site-admin/media/get_img/'+num,
			type: 'GET',
			success: function(data) {
				$('.feature-image-img').html(data+'<div><a href="#" onclick="return remove_feature_image()"><?php echo lang( 'post_remove' ); ?></a></div>');
			}
		});
	}
	
	// modules plug script
	<?php echo $this->modules_plug->do_action( 'post_admin_script' ); ?> 
</script>