<h1><?php echo ( $this->uri->segment(3) == 'add' ? lang( 'tag_add' ) : lang( 'tag_edit' ) ); ?></h1>

<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?>
	
	<div id="tabs" class="page-tabs tag-tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo lang( 'tag_info' ); ?></a></li>
			<li><a href="#tabs-2"><?php echo lang( 'admin_seo' ); ?></a></li>
			<li><a href="#tabs-3"><?php echo lang( 'admin_theme' ); ?></a></li>
		</ul>
		
		<div id="tabs-1">
			<input type="hidden" name="parent_id" value="0" />
			<label><?php echo lang( 'tag_name' ); ?>:<span class="txt_require">*</span><input type="text" name="t_name" value="<?php if ( isset( $t_name ) ) {echo $t_name;} ?>" maxlength="255" class="t_name" /></label>
			<label><?php echo lang( 'tag_description' ); ?>:<textarea name="t_description"><?php if ( isset( $t_description ) ) {echo $t_description;} ?></textarea><span class="txt_comment"><?php echo lang( 'admin_html_allowed' ); ?></span></label>
		</div>
		
		<div id="tabs-2">
			<label><?php echo lang( 'admin_uri' ); ?>:<span class="txt_require">*</span><input type="text" name="t_uri" value="<?php if ( isset( $t_uri ) ) {echo $t_uri;} ?>" maxlength="255" class="t_uri" /></label>
			<label><?php echo lang( 'admin_meta_title' ); ?>:<input type="text" name="meta_title" value="<?php if ( isset( $meta_title ) ) {echo $meta_title;} ?>" maxlength="255" /></label>
			<label><?php echo lang( 'admin_meta_description' ); ?>:<input type="text" name="meta_description" value="<?php if ( isset( $meta_description ) ) {echo $meta_description;} ?>" maxlength="255" /></label>
			<label><?php echo lang( 'admin_meta_keywords' ); ?>:<input type="text" name="meta_keywords" value="<?php if ( isset( $meta_keywords ) ) {echo $meta_keywords;} ?>" maxlength="255" /></label>
		</div>
		
		<div id="tabs-3">
			<div class="theme-select">
				<label>
					<img src="<?php echo $this->themes_model->show_theme_screenshot( '' ); ?>" alt="" /><br />
					<input type="radio" name="theme_system_name" value=""<?php if ( !isset( $theme_system_name ) || ( isset( $theme_system_name ) && $theme_system_name == null ) ) {echo ' checked="checked"';} ?> /><?php echo lang( 'tag_no_theme' ); ?>
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
		
		<div class="ui-tabs-panel button-panel"><button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button></div>
	</div>
	
<?php echo form_close(); ?> 

<?php echo $this->modules_plug->do_action( 'tag_admin_bottom' ); ?> 
	
<script type="text/javascript">
	make_tabs();
	
	<?php if ( $this->uri->segment(3) == 'add' ): ?> 
	// convert from name to uri (php+ajax)
	$(".t_name").keyup(function() {
		var tag_val = $(this).val();
		ajax_check_uri(tag_val);
	});// name to uri
	<?php endif; ?> 
	
	// check for no duplicate uri while entering
	$(".t_uri").keyup(function() {
		var uri_val = $(this).val();
		delay(function(){ajax_check_uri(uri_val);}, 2000);
	});// check uri
	
	function ajax_check_uri(inputval) {
		$.ajax({
			url: site_url+'site-admin/tag/ajax_nameuri',
			type: 'POST',
			data: ({ <?php echo $this->security->get_csrf_token_name(); ?>:csrf_value, t_name:inputval<?php if ( $this->uri->segment(3) == 'edit' ): ?>, nodupedit:'true', id:'<?php echo $tid; ?>'<?php endif; ?> }),
			dataType: 'json',
			success: function( data ) {
				$('.t_uri').val(data.t_uri);
			},
			error: function( data, status, e) {
				$('.t_uri').val('');
				alert( e );
			}
		});
	}
</script>