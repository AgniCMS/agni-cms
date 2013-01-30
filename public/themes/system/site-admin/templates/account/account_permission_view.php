<h1><?php echo lang( 'account_permission' ); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button red btn btn-danger" id="reset-permission"><?php echo lang( 'account_permission_reset' ); ?></button>
	</div>
	<div class="clear"></div>
</div>

<?php echo form_open( current_url().'/save' ); ?> 
	<?php if ( isset( $form_status ) ) {echo $form_status;} ?> 

	<table class="list-items-cells tableWithFloatingHeader">
		<thead>
			<tr>
				<th class="perm-page-cell-head"><?php echo lang( 'account_permission_page' ); ?></th>
				<th class="perm-action-cell-head"><?php echo lang( 'account_permission_action' ); ?></th>
				<?php $column = 2; foreach ( $list_level_group['items'] as $lv ): ?> 
				<th class="perm-lv-cell-head"><?php echo $lv->level_name; ?></th><?php $column++; ?> 
				<?php endforeach; ?> 
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="perm-page-cell-head"><?php echo lang( 'account_permission_page' ); ?></th>
				<th class="perm-action-cell-head"><?php echo lang( 'account_permission_action' ); ?></th>
				<?php foreach ( $list_level_group['items'] as $lv ): ?> 
				<th class="perm-lv-cell-head"><?php echo $lv->level_name; ?></th>
				<?php endforeach; ?> 
			</tr>
		</tfoot>
		<tbody>
			<?php $count_page = 1; $count_all = 1; ?> 
			<?php foreach ( $list_permissions as $perm_page => $perm_actions ): ?> 
			<?php $count_act = 1; ?> 
			<?php foreach ( $perm_actions as $perm_action ): ?> 
			<tr>
				<?php if ( $count_act == 1 ): ?><td rowspan="<?php echo count( $perm_actions ); ?>" class="perm-page-cell perm-page-row"><?php echo lang( $perm_page ); ?></td><?php endif; ?> 
				<td class="perm-action-cell<?php if ( $count_act == 1 ): ?> perm-page-row<?php endif; ?>"><?php echo lang( $perm_action ); ?></td>
				<?php foreach ( $list_level_group['items'] as $lv ): ?> 
				<td class="perm-lv-cell<?php if ( $count_act == 1 ): ?> perm-page-row<?php endif; ?>">
					<input type="hidden" name="permission_page[<?php echo $count_all; ?>]" value="<?php echo $perm_page; ?>" />
					<input type="hidden" name="permission_action[<?php echo $count_all; ?>]" value="<?php echo $perm_action; ?>" />
					<input type="checkbox" name="level_group_id[<?php echo $count_all; ?>][]" value="<?php echo $lv->level_group_id; ?>"<?php if ( in_array( array( $perm_page => array( $perm_action => $lv->level_group_id ) ), $list_permissions_check ) || $lv->level_group_id == '1' ): ?> checked="checked"<?php endif; ?> />
				</td>
				<?php endforeach; ?> 
			</tr>
			<?php $count_act++; $count_all++; ?> 
			<?php endforeach; ?> 
			<?php $count_page++; ?> 
			<?php endforeach; ?> 
			<?php unset( $list_permissions_check, $list_permissions ); ?>
		</tbody>
	</table>
	
	<button type="submit" class="bb-button btn btn-primary"><?php echo lang( 'admin_save' ); ?></button>
<?php echo form_close(); ?> 

<script type="text/javascript">
	$(document).ready(function() {
		$( '#reset-permission' ).click(function() {
			if ( window.confirm('<?php echo lang("account_permission_are_you_sure_to_reset"); ?>') ) {
				$.ajax({
					url: '<?php echo current_url(); ?>/reset',
					success: function() {
						alert( '<?php echo lang( 'account_permission_reset_done' ); ?>' );
						location.reload();
					}
				});
			}
			return false;
		});// reset button.
		
		// floating header
		$("table.tableWithFloatingHeader").each(function() {
		$(this).wrap("<div class=\"divTableWithFloatingHeader\" style=\"position:relative\"></div>");
		var originalHeaderRow = $("tr:first", this)
		originalHeaderRow.before(originalHeaderRow.clone());
		var clonedHeaderRow = $("tr:first", this)
		clonedHeaderRow.addClass("tableFloatingHeader");
		clonedHeaderRow.css("position", "absolute");
		clonedHeaderRow.css("top", "0px");
		clonedHeaderRow.css("left", $(this).css("margin-left"));
		clonedHeaderRow.css("visibility", "hidden");
		originalHeaderRow.addClass("tableFloatingHeaderOriginal");
		});
		UpdateTableHeaders();
		$(window).scroll(UpdateTableHeaders);
		$(window).resize(UpdateTableHeaders);
		// end floating header
	});// jquery
</script>