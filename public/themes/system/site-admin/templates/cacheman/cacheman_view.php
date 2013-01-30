<h1><?php echo lang( 'cache_manager' ); ?></h1>

<div class="page-add-edit page-cacheman">
	<?php echo form_open( 'site-admin/cacheman/do_action' ); ?> 
		<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 
		
		<div class="form-label">
			<?php echo lang( 'cache_please_select_action' ); ?>:
			<div>
				<select name="cache_act" class="select-inline">
					<option value=""></option>
					<option value="clear"><?php echo lang( 'cache_clear_all' ); ?></option>
				</select>
				<button type="submit" class="bb-button btn btn-warning"><?php echo lang( 'admin_submit' ); ?></button>
			</div>
		</div>
		
	<?php echo form_close(); ?> 
</div>