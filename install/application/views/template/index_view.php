<h1><?php echo lang( 'agni_install_requirement' ); ?></h1>

<table class="table table-verify-requirement">
	<tbody>
		<?php foreach ( $list_verify as $key => $item ): ?> 
		<tr class="<?php echo $item['result']; ?>">
			<td class="table-label"><?php echo lang( $key ); ?></td>
			<td class="table-data"><?php echo $item['value']; ?><?php if ( isset( $item['result_text'] ) ): ?><div><?php echo $item['result_text']; ?></div><?php endif; ?></td>
		</tr>
		<?php if ( $item['result'] == 'fail' ) {$critical_error = true;} ?> 
		<?php endforeach; ?> 
		<tr>
			<td colspan="2"><strong><?php echo lang( 'agni_check_writable_folders_and_files' ); ?></strong></td>
		</tr>
		<?php foreach ( $list_verify_writable as $key => $item ): ?> 
		<tr class="<?php echo $item['result']; ?>">
			<td class="table-label"><?php echo str_replace( '../', '', $key ); ?></td>
			<td class="table-data"><?php echo $item['value']; ?><?php if ( isset( $item['result_text'] ) ): ?><div><?php echo $item['result_text']; ?></div><?php endif; ?></td>
		</tr>
		<?php 
		if ( $item['result'] == 'fail' ) {
			$critical_error = true;
			set_cookie( 'agni_install_verify', 'fail', 86400 );
		} else {
			set_cookie( 'agni_install_verify', 'pass', 86400 );
		}
		?> 
		<?php endforeach; ?> 
	</tbody>
</table>

<div class="button-cmd">
	<?php if ( isset( $critical_error ) && $critical_error === true ): ?> 
	<p><?php echo lang( 'agni_please_check_error_message_and_fix' ); ?></p>
	<?php else: ?> 
	<div class="next-btn">
		<button type="button" onclick="window.location='<?php echo site_url( 'index/step2' ); ?>';" class="btn btn-primary"><?php echo lang( 'agni_next_step' ); ?></button>
	</div>
	<?php endif; ?> 
	<div class="clear"></div>
</div>