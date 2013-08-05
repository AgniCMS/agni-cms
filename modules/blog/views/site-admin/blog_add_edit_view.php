<h1><?php if ($this->uri->segment(4) == 'add') {echo lang('blog_new_post');} else {echo lang('blog_edit_post');} ?></h1>

<?php echo form_open(); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<div class="page-add-edit">

		<label><?php echo lang('blog_title'); ?>: <span class="txt_require">*</span>
			<input type="text" name="blog_title" value="<?php if (isset($blog_title)) {echo $blog_title;} ?>" maxlength="255" />
		</label>
		<label><?php echo lang('blog_content'); ?>: <span class="txt_require">*</span>
			<!--insert media-->
			<span class="ico16-media-insert insert-media" title="<?php echo lang('blog_insert_media'); ?>" onclick="$('#media-popup').dialog('open');"><?php echo lang('blog_insert_media'); ?></span>
			<div id="media-popup" title="<?php echo lang('blog_insert_media'); ?>" class="dialog"><iframe name="media-browser" id="media-browser" src="<?php echo site_url('site-admin/media/popup'); ?>" class="media-browser-dialog iframe-in-dialog"></iframe></div>
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
			<textarea name="blog_content" cols="30" rows="20" class="blog-content"><?php if (isset($blog_content)) {echo $blog_content;} ?></textarea>
		</label>
		
		<button type="submit" class="bb-button blog-save-button"><?php echo lang('admin_save'); ?></button>
	</div>
	
<?php echo form_close(); ?> 

<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/tiny_mce/jquery.tinymce.js"></script>
<script type="text/javascript">
	$('.blog-content').tinymce({
			// Location of TinyMCE script
			script_url : '<?php echo $this->theme_path; ?>share-js/tiny_mce/tiny_mce.js',
			apply_source_formatting : true,
			content_css : '<?php echo $this->theme_path; ?>front/style.css',
			convert_urls : false,
			document_base_url : base_url,
			inline_styles : true,
			preformatted : false,
			relative_urls : false,
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
</script>