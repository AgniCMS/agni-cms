<h1><?php echo lang('config_global'); ?></h1>

<?php echo form_open(); ?>
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
	
	<div id="tabs" class="page-tabs config-tabs">
		<ul>
			<li><a href="#tabs-1"><?php echo lang('config_site'); ?></a></li>
			<li><a href="#tabs-2"><?php echo lang('config_member'); ?></a></li>
			<li><a href="#tabs-3"><?php echo lang('config_email'); ?></a></li>
			<li><a href="#tabs-4"><?php echo lang('config_content'); ?></a></li>
			<li><a href="#tabs-media"><?php echo lang('config_media'); ?></a></li>
			<li><a href="#tabs-comment"><?php echo lang('config_comment'); ?></a></li>
			<li><a href="#tabs-ftp"><?php echo lang('config_ftp'); ?></a></li>
		</ul>

		
		<div id="tabs-1">
			<div class="control-group">
				<label class="control-label" for="cfg-site_name"><?php echo lang('config_sitename'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="site_name" value="<?php if (isset($site_name)) {echo $site_name;} ?>" maxlength="255" id="cfg-site_name" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="page_title_separator"><?php echo lang('config_page_title_separator'); ?>: </label>
				<div class="controls">
					<input type="text" name="page_title_separator" value="<?php if (isset($page_title_separator)) {echo $page_title_separator;} ?>" maxlength="255" id="page_title_separator" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('config_timezone'); ?>: </label>
				<div class="controls">
					<?php echo timezone_menu((isset($site_timezone) ? $site_timezone : ''), 'input-block-level'); ?>
				</div>
			</div>
			
			<hr />
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('config_autoupdate'); ?>: </label>
				<div class="controls">
					<select name="angi_auto_update">
						<option value="0"<?php if (isset($angi_auto_update) && $angi_auto_update == '0') { ?> selected="selected"<?php } ?>><?php echo lang('config_disable'); ?></option>
						<option value="1"<?php if (isset($angi_auto_update) && $angi_auto_update == '1') { ?> selected="selected"<?php } ?>><?php echo lang('config_enable'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('config_current_version'); ?>: </label>
				<div class="controls">
					<?php echo lang('admin_agnicms'); ?> <?php if (isset($agni_version)) {echo $agni_version;} ?> 
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="cfg-agni_auto_update_url"><?php echo lang('config_autoupdate_url'); ?>: </label>
				<div class="controls">
					<input type="text" name="agni_auto_update_url" value="<?php if (isset($agni_auto_update_url)) {echo $agni_auto_update_url;} ?>" maxlength="255" id="cfg-agni_auto_update_url" class="input-block-level" />
					<div class="help-block"><?php printf(lang('config_autoupdate_url_default_is'), 'http://agnicms.org/th/modules/updateservice/update.xml'); ?></div>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('config_system_cron'); ?>: </label>
				<div class="controls">
					<select name="agni_system_cron">
						<option value="0"<?php if (isset($agni_system_cron) && $agni_system_cron == '0') { ?> selected="selected"<?php } ?>><?php echo lang('admin_no'); ?></option>
						<option value="1"<?php if (isset($agni_system_cron) && $agni_system_cron == '1') { ?> selected="selected"<?php } ?>><?php echo lang('admin_yes'); ?></option>
					</select>
					<div class="help-block"><?php printf(lang('config_system_cron_can_run_from_cronjob_if_no_use_system_cron'), site_url('cron')); ?></div>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-2">
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox inline">
						<input type="checkbox" name="member_allow_register" value="1"<?php if (isset($member_allow_register) && $member_allow_register == '1') {echo ' checked="checked"';} ?> /> <?php echo lang('config_member_allow_register'); ?>
					</label>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox inline">
						<input type="checkbox" name="member_register_notify_admin" value="1"<?php if (isset($member_register_notify_admin) && $member_register_notify_admin == '1') {echo 'checked="checked"';} ?> /> <?php echo lang('config_member_register_notify_admin'); ?>
					</label>
					<span class="help-inline">(<?php echo lang('config_member_force_notify_if_verify_admin'); ?>)</span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="member_verification"><?php echo lang('config_member_verification'); ?>: </label>
				<div class="controls">
					<select name="member_verification" id="member_verification" class="input-block-level">
						<option value="1"<?php if (isset($member_verification) && $member_verification == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_member_verify_email'); ?></option>
						<option value="2"<?php if (isset($member_verification) && $member_verification == '2') {echo ' selected="selected"';} ?>><?php echo lang('config_member_verify_admin'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="member_admin_verify_emails"><?php echo lang('config_member_admin_verify_emails'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="member_admin_verify_emails" value="<?php if (isset($member_admin_verify_emails)) {echo $member_admin_verify_emails;} ?>" maxlength="255" id="member_admin_verify_emails" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox inline">
						<input type="checkbox" name="duplicate_login" value="1"<?php if (isset($duplicate_login) && $duplicate_login == '1') {echo ' checked="checked"';} ?> /> <?php echo lang('config_duplicate_login'); ?>
					</label>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"></label>
				<div class="controls">
					<label class="checkbox inline">
						<input type="checkbox" name="allow_avatar" value="1"<?php if (isset($allow_avatar) && $allow_avatar == '1') {echo ' checked="checked"';} ?> /> <?php echo lang('config_allow_avatar'); ?>
					</label>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="avatar_size"><?php echo lang('config_avatar_size'); ?>: </label>
				<div class="controls">
					<input type="text" name="avatar_size" value="<?php if (isset($avatar_size)) {echo $avatar_size;} ?>" maxlength="255" id="avatar_size" />
					<span class="help-inline"><?php echo lang('config_avatar_size_comment'); ?></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="avatar_allowed_types"><?php echo lang('config_avatar_allowed_types'); ?>: </label>
				<div class="controls">
					<input type="text" name="avatar_allowed_types" value="<?php if (isset($avatar_allowed_types)) {echo $avatar_allowed_types;} ?>" maxlength="255" id="avatar_allowed_types" class="input-block-level" />
					<span class="help-block">gif|jpg|png</span>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-3">
			<div class="control-group">
				<label class="control-label" for="mail_protocol"><?php echo lang('config_mail_protocol'); ?>: </label>
				<div class="controls">
					<select name="mail_protocol" id="mail_protocol" class="input-block-level">
						<option value="mail"<?php if (isset($mail_protocol) && $mail_protocol == 'mail') {echo ' selected="selected"';} ?>>Mail function</option>
						<option value="sendmail"<?php if (isset($mail_protocol) && $mail_protocol == 'sendmail') {echo ' selected="selected"';} ?>>Sendmail function</option>
						<option value="smtp"<?php if (isset($mail_protocol) && $mail_protocol == 'smtp') {echo ' selected="selected"';} ?>>SMTP</option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_mailpath"><?php echo lang('config_mail_mailpath'); ?>: </label>
				<div class="controls">
					<input type="text" name="mail_mailpath" value="<?php if (isset($mail_mailpath)) {echo $mail_mailpath;} ?>" maxlength="255" id="mail_mailpath" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_smtp_host"><?php echo lang('config_mail_smtp_host'); ?>: </label>
				<div class="controls">
					<input type="text" name="mail_smtp_host" value="<?php if (isset($mail_smtp_host)) {echo $mail_smtp_host;} ?>" maxlength="255" id="mail_smtp_host" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_smtp_user"><?php echo lang('config_mail_smtp_user'); ?>: </label>
				<div class="controls">
					<input type="text" name="mail_smtp_user" value="<?php if (isset($mail_smtp_user)) {echo $mail_smtp_user;} ?>" maxlength="255" id="mail_smtp_user" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_smtp_pass"><?php echo lang('config_mail_smtp_pass'); ?>: </label>
				<div class="controls">
					<input type="password" name="mail_smtp_pass" value="<?php if (isset($mail_smtp_pass)) {echo $mail_smtp_pass;} ?>" maxlength="255" id="mail_smtp_pass" class="input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_smtp_port"><?php echo lang('config_mail_smtp_port'); ?>: </label>
				<div class="controls">
					<input type="text" name="mail_smtp_port" value="<?php if (isset($mail_smtp_port)) {echo $mail_smtp_port;} ?>" maxlength="255" id="mail_smtp_port" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="mail_sender_email"><?php echo lang('config_mail_sender_email'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="mail_sender_email" value="<?php if (isset($mail_sender_email)) {echo $mail_sender_email;} ?>" maxlength="255" id="mail_sender_email" class="input-block-level" />
					<span class="help-block"><?php echo lang('config_mail_sender_email_comment'); ?></span>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-4">
			<div class="control-group">
				<label class="control-label" for="content_show_title"><?php echo lang('config_content_show_title'); ?>: </label>
				<div class="controls">
					<select name="content_show_title" id="content_show_title" class="input-block-level">
						<option value="1"<?php if (isset($content_show_title) && $content_show_title == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_title) && $content_show_title == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="content_show_time"><?php echo lang('config_content_show_time'); ?>: </label>
				<div class="controls">
					<select name="content_show_time" id="content_show_time" class="input-block-level">
						<option value="1"<?php if (isset($content_show_time) && $content_show_time == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_time) && $content_show_time == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="content_show_author"><?php echo lang('config_content_show_author'); ?>: </label>
				<div class="controls">
					<select name="content_show_author" id="content_show_author" class="input-block-level">
						<option value="1"<?php if (isset($content_show_author) && $content_show_author == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_yes'); ?></option>
						<option value="0"<?php if (isset($content_show_author) && $content_show_author == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="content_items_perpage"><?php echo lang('config_content_items_perpage'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="content_items_perpage" value="<?php if (isset($content_items_perpage)) {echo $content_items_perpage;} ?>" maxlength="2" id="content_items_perpage" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="content_frontpage_category"><?php echo lang('config_content_frontpage_category'); ?> (<?php $langs = $this->config->item('lang_uri_abbr'); echo ucfirst($langs[$this->lang->get_current_lang()]); unset($langs); ?>): </label>
				<div class="controls">
					<select name="content_frontpage_category" id="content_frontpage_category" class="input-block-level">
						<option value=""<?php if ($content_frontpage_category == null) {echo ' selected="selected"';} ?>>&nbsp;</option>
						<?php $this->load->helper('category');
						$this->load->model('taxonomy_model');
						$this->taxonomy_model->tax_type = 'category';
						echo show_category_select($this->taxonomy_model->listTaxTerm(), $content_frontpage_category); ?> 
					</select>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-media">
			<div class="control-group">
				<label class="control-label" for="media_allowed_types"><?php echo lang('config_media_allowed_types'); ?>: </label>
				<div class="controls">
					<input type="text" name="media_allowed_types" value="<?php if (isset($media_allowed_types)) {echo $media_allowed_types;} ?>" maxlength="255" id="media_allowed_types" class="input-block-level" />
					<span class="help-block"><?php echo lang('config_media_please_check_mimes'); ?></span>
				</div>
			</div>
		</div>
		
		
		<div id="tabs-comment">
			<div class="control-group">
				<label class="control-label" for="comment_allow"><?php echo lang('config_comment_allow'); ?>: </label>
				<div class="controls">
					<select name="comment_allow" id="comment_allow" class="input-block-level">
						<option value=""<?php if (isset($comment_allow) && $comment_allow == null) {echo ' selected="selected"';} ?>><?php echo lang('config_comment_allow_uptopost'); ?></option>
						<option value="1"<?php if (isset($comment_allow) && $comment_allow == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_yes'); ?></option>
						<option value="0"<?php if (isset($comment_allow) && $comment_allow == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="comment_show_notallow"><?php echo lang('config_comment_show_notallow'); ?>: </label>
				<div class="controls">
					<select name="comment_show_notallow" id="comment_show_notallow" class="input-block-level">
						<option value="1"<?php if (isset($comment_show_notallow) && $comment_show_notallow == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_yes'); ?></option>
						<option value="0"<?php if (isset($comment_show_notallow) && $comment_show_notallow == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_no'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="comment_perpage"><?php echo lang('config_comment_perpage'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="comment_perpage" value="<?php if (isset($comment_perpage)) {echo $comment_perpage;} ?>" maxlength="2" id="comment_perpage" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="comment_new_notify_admin"><?php echo lang('config_comment_new_notify_admin'); ?>: </label>
				<div class="controls">
					<select name="comment_new_notify_admin" id="comment_new_notify_admin" class="input-block-level">
						<option value="0"<?php if (isset($comment_new_notify_admin) && $comment_new_notify_admin == '0') {echo ' selected="selected"';} ?>><?php echo lang('config_comment_new_notify_no'); ?></option>
						<option value="1"<?php if (isset($comment_new_notify_admin) && $comment_new_notify_admin == '1') {echo ' selected="selected"';} ?>><?php echo lang('config_comment_new_notify_yesmoderation'); ?></option>
						<option value="2"<?php if (isset($comment_new_notify_admin) && $comment_new_notify_admin == '2') {echo ' selected="selected"';} ?>><?php echo lang('config_comment_new_notify_yesall'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="comment_admin_notify_emails"><?php echo lang('config_comment_admin_notify_emails'); ?>: <span class="txt_require">*</span></label>
				<div class="controls">
					<input type="text" name="comment_admin_notify_emails" value="<?php if (isset($comment_admin_notify_emails)) {echo $comment_admin_notify_emails;} ?>" maxlength="255" id="comment_admin_notify_emails" class="input-block-level" />
				</div>
			</div>
		</div>
		
		
		<div id="tabs-ftp">
			<p><?php echo lang('config_ftp_usage_describe');?></p>
			
			<div class="control-group">
				<label class="control-label" for="config-ftp_host"><?php echo lang('config_ftp_host'); ?>:</label>
				<div class="controls">
					<input type="text" name="ftp_host" value="<?php if (isset($ftp_host)) {echo $ftp_host;} ?>" maxlength="255" id="config-ftp_host" class="config-ftp_host input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="config-ftp_username"><?php echo lang('config_ftp_username'); ?>:</label>
				<div class="controls">
					<input type="text" name="ftp_username" value="<?php if (isset($ftp_username)) {echo $ftp_username;} ?>" maxlength="255" id="config-ftp_username" class="config-ftp_username input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="config-ftp_password"><?php echo lang('config_ftp_password'); ?>:</label>
				<div class="controls">
					<input type="password" name="ftp_password" value="<?php if (isset($ftp_password)) {echo $this->encrypt->decode($ftp_password);} ?>" maxlength="255" id="config-ftp_password" class="config-ftp_password input-block-level" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="config-ftp_port"><?php echo lang('config_ftp_port'); ?>:</label>
				<div class="controls">
					<input type="text" name="ftp_port" value="<?php if (isset($ftp_port)) {echo $ftp_port;} ?>" maxlength="255" id="config-ftp_port" class="config-ftp_port" />
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label"><?php echo lang('config_ftp_passive'); ?>:</label>
				<div class="controls">
					<select name="ftp_passive" class="config-ftp_passive">
						<option value="false"<?php if (isset($ftp_passive) && $ftp_passive == 'false') { ?> selected="selected"<?php } ?>><?php echo lang('admin_no'); ?></option>
						<option value="true"<?php if (isset($ftp_passive) && $ftp_passive == 'true') { ?> selected="selected"<?php } ?>><?php echo lang('admin_yes'); ?></option>
					</select>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label" for="config-ftp_basepath"><?php echo lang('config_ftp_basepath'); ?>:</label>
				<div class="controls">
					<input type="text" name="ftp_basepath" value="<?php if (isset($ftp_basepath)) {echo $ftp_basepath;} ?>" maxlength="255" id="config-ftp_basepath" class="config-ftp_basepath input-block-level" />
					<div class="help-block"><?php echo lang('config_ftp_basepath_generally_is'); ?></div>
				</div>
			</div>
			
			<button type="button" class="btn" onclick="ajax_test_ftp();"><?php echo lang('config_ftp_test_connection'); ?></button>
			<div class="ftp-test-result"></div>
		</div>
		
		
		<div class="ui-tabs-panel button-panel"><button type="submit" class="bb-button btn btn-primary"><?php echo lang('admin_save'); ?></button></div>
	</div>
<?php echo form_close(); ?>

<script type="text/javascript">
	make_tabs();
	
	
	function ajax_test_ftp() {
		$('.ftp-test-result').fadeOut();
		
		var ftp_host = $('.config-ftp_host').val();
		var username = $('.config-ftp_username').val();
		var password = $('.config-ftp_password').val();
		var port = $('.config-ftp_port').val();
		var passive = $('.config-ftp_passive').val();
		var basepath = $('.config-ftp_basepath').val();
		
		$.ajax({
			url: site_url+'site-admin/config/ajax_test_ftp',
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&hostname='+ftp_host+'&username='+username+'&password='+password+'&port='+port+'&passive='+passive+'&basepath='+basepath,
			dataType: 'html',
			success: function(data) {
				$('.ftp-test-result').html(data);
				$('.ftp-test-result').show();
			}
		});
	}// ajax_test_ftp
</script>