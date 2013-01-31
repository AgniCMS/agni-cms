<h1><?php echo lang( 'modules_modules' ); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button standard btn" onclick="window.location=site_url+'site-admin/module/add';"><?php echo lang( 'admin_add' ); ?></button>
		| <?php echo sprintf( lang( 'modules_all' ), $list_item['total'] ); ?>
		| <?php echo sprintf( lang( 'modules_inactive' ), ($list_item['total']-$this->db->count_all_results( 'modules' )) ); ?>
	</div>
	<div class="clear"></div>
</div>

<?php echo form_open( 'site-admin/module/process_bulk' ); ?>
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?>
	
	<table class="list-items">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang( 'modules_name' ); ?></th>
				<th><?php echo lang( 'modules_description' ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo lang( 'modules_name' ); ?></th>
				<th><?php echo lang( 'modules_description' ); ?></th>
			</tr>
		</tfoot>
		<tbody>
		<?php if ( isset( $list_item['items'] ) && is_array( $list_item['items'] ) ): ?>
		<?php foreach ( $list_item['items'] as $key ): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox( 'id[]', $key['module_system_name']); ?></td>
				<td>
					<strong><?php if ( !empty( $key['module_name'] ) ): ?><?php echo $key['module_name']; ?><?php else: ?><em title="<?php echo lang( 'modules_no_name' ); ?>"><?php echo $key['module_system_name']; ?></em><?php endif; ?></strong>
					<div>
					<?php if ( $key['module_activated'] == 'yes' ): ?> 
						<?php echo anchor( 'site-admin/module/deactivate?id='.$key['module_system_name'], lang( 'modules_deactivate' ) ); ?> 
						<?php $find_install = Modules::find($key['module_system_name'].'_uninstall', $key['module_system_name'], 'controllers/');
						if ( isset( $find_install[0] ) && $find_install[0] != null ): ?>
							| <?php echo anchor( $key['module_system_name'].'/'.$key['module_system_name'].'_uninstall', lang( 'modules_uninstall' ), array( 'onclick' => 'return confirm(\''.sprintf( lang( 'module_are_you_sure_uninstall' ), $key['module_name'] ).'\');' ) ); ?>
						<?php endif; ?>
					<?php else: ?> 
						<?php echo anchor( 'site-admin/module/activate?id='.$key['module_system_name'], lang( 'modules_activate' ) ); ?> 
					<?php endif; ?>
					</div>
				</td>
				<td>
					<p><?php echo $key['module_description']; ?></p>
					<p>
						<?php echo lang( 'modules_version' ); ?>: <?php echo (!empty( $key['module_version'] ) ? $key['module_version'] : '-' ); ?> 
						| <?php echo lang( 'modules_by' ); ?>: <?php if ( !empty( $key['module_author_name'] ) ) {if ( !empty( $key['module_author_url'] ) ) {echo anchor( $key['module_author_url'], $key['module_author_name'] );} else {echo $key['module_author_name'];}} else {echo '-';} ?> 
						<?php if ( !empty( $key['module_url'] ) ): ?>| <?php echo anchor( $key['module_url'], lang( 'modules_visit_site' ) ); ?><?php endif; ?>
					</p>
				</td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="3"><?php echo lang( 'admin_nodata' ); ?></td>
			</tr>
		<?php endif; ?> 
	</table>
	
	<div class="cmds">
		<div class="cmd-left">
			<select name="act">
				<option value="" selected="selected"></option>
				<option value="activate"><?php echo lang( 'modules_activate' ); ?></option>
				<option value="deactivate"><?php echo lang( 'modules_deactivate' ); ?></option>
				<option value="del"><?php echo lang( 'admin_delete' ); ?></option>
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo lang( 'admin_submit' ); ?></button>
		</div>
		<div class="cmd-right">
			<?php if ( isset( $pagination ) ) {echo $pagination;} ?>
		</div>
		<div class="clear"></div>
	</div>
<?php echo form_close(); ?> 