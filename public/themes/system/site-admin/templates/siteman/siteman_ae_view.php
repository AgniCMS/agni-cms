<h1><?php echo ( $this->uri->segment(3) == 'add' ? lang( 'siteman_add' ) : lang( 'siteman_edit' ) ); ?></h1>

<?php echo form_open(); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

	<div class=" page-add-edit page-siteman-ae">
		<label><?php echo lang( 'siteman_site_name' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="site_name" value="<?php if ( isset( $site_name ) ) {echo $site_name;} ?>" maxlength="255" />
		</label>
		
		<label><?php echo lang( 'siteman_site_domain' ); ?>: <span class="txt_require">*</span>
			<input type="text" name="site_domain" value="<?php if ( isset( $site_domain ) ) {echo $site_domain;} ?>" maxlength="255" placeholder="domain.tld" />
			<span class="txt_comment"><?php echo lang( 'siteman_domain_comment' ); ?></span>
		</label>
		
		<label><?php echo lang( 'siteman_site_status' ); ?>: 
			<select name="site_status">
				<option value="0"<?php if ( isset( $site_status ) && $site_status == '0' ) { ?> selected="selected"<?php } ?>><?php echo lang( 'siteman_disable' ); ?></option>
				<option value="1"<?php if ( isset( $site_status ) && $site_status == '1' ) { ?> selected="selected"<?php } ?>><?php echo lang( 'siteman_enable' ); ?></option>
			</select>
		</label>
		
		<button type="submit" class="bb-button standard btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
	</div>

<?php echo form_close(); ?> 