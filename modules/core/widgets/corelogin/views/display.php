<?php 
if (isset($values['block_title']) && $values['block_title'] != null) {
	echo '<h3>'.$values['block_title'].'</h3>';
}
?>
<?php if ($this->account_model->isMemberLogin()): ?> 
	<ul>
		<?php
		if (isset($values['show_admin_link']) && $values['show_admin_link'] == '1') {
			// get account_id for current user.
			$cm_account = $this->account_model->getAccountCookie('member');
			$cur_user_id = 0;
			if (isset($cm_account['id'])) {
				$cur_user_id = $cm_account['id'];
			}
			unset($cm_account);
			// check permission login page for this user.
			if ($this->account_model->checkAdminPermission('account_admin_login', 'account_admin_login', $cur_user_id) == true) {
				echo '<li>'.anchor('site-admin', lang('coremd_login_administrator')).'</li>';
			}
		}
		?> 
		<li><?php echo anchor('account/edit-profile', lang('account_edit_profile')); ?></li>
		<li><?php echo anchor('account/logout', lang('account_logout')); ?></li>
	</ul>
<?php else: ?> 
	<?php echo form_open('account/login'); ?> 
	<label class="form-label"><?php echo lang('account_username'); ?>: <input type="text" name="account_username" value="" /></label>
	<label class="form-label"><?php echo lang('account_password'); ?>: <input type="password" name="account_password" value="" /></label>
	<label class="form-label"><input type="checkbox" name="remember" value="yes" /> <?php echo lang('account_remember_my_login'); ?></label>
	<button type="submit" class="btn btn-small"><?php echo lang('account_login'); ?></button>
	<?php echo anchor('account/register', lang('account_register')); ?> 
	<?php echo form_close(); ?> 
<?php endif; ?> 