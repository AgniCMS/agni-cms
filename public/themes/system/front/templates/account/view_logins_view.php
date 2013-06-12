<article class="general-page-container">
	
	<h1><?php echo sprintf( lang( 'account_view_logins_of' ), $account->account_username ); ?></h1>

	<table class="table table-bordered table-striped list-logins">
		<thead>
			<tr>
				<th><?php echo lang( 'account_useragent' ); ?></th>
				<th><?php echo lang( 'account_OS' ); ?></th>
				<th><?php echo lang( 'account_ipaddress' ); ?></th>
				<th><?php echo lang( 'account_time' ); ?></th>
				<th><?php echo lang( 'account_login_result' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php if ( isset( $list_item['items'] ) && is_array( $list_item['items'] ) ): ?> 
		<?php foreach ( $list_item['items'] as $row ): ?> 
			<tr>
				<td class="user-agent"><?php echo $row->login_ua; ?></td>
				<td><?php echo $row->login_os; ?> <?php echo $row->login_browser; ?></td>
				<td><?php echo $row->login_ip; ?></td>
				<td><?php echo gmt_date( '', $row->login_time_gmt , $account->account_timezone ); ?></td>
				<td><span class="ico-<?php echo ( $row->login_attempt == '1' ? 'yes' : 'no' ); ?>"></span> <?php echo $row->login_attempt_text; ?></td>
			</tr>
		<?php endforeach; ?> 
		<?php endif; ?> 
		</tbody>
	</table>
	
</article>