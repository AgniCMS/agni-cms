<?php $this->load->view('site-admin/inc_html_head'); ?> 
		

		<div class="page-container">
			
			
			<div class="header">
				<div class="cms-logo"><img src="<?php echo $this->theme_path; ?>site-admin/images/logo.png" alt="Agni CMS" /></div>
				<div class="site-name"><?php echo $this->config_model->loadSingle('site_name'); ?></div>
				<div class="user">
					<?php if (!isset($cookie)) {
						$cookie = $this->account_model->getAccountCookie('admin');
					} ?> 
					<ul>
						<li><?php echo sprintf(lang('admin_hello'), $cookie['username']); ?></li>
						<li class="language"><?php echo language_switch_admin(); ?></li>
						<li><?php echo anchor('site-admin/logout', lang('admin_logout')); ?></li>
					</ul>
					<div class="clear"></div>
				</div>
				<div class="clearfix"></div>
				
				
				<div class="navigations">
					<?php // load helper
					$this->load->helper('account'); 
					?> 
					<ul class="primary sf-menu">
						<li><?php echo anchor('#', lang('admin_nav_website'), array('onclick' => 'return false;')); ?> 
							<ul>
								<li>
									<?php echo anchor('site-admin', lang('admin_home')); ?> 
									<?php if (isset($agni_list_sites['items']) && is_array($agni_list_sites['items']) && !empty($agni_list_sites['items'])) { ?> 
									<ul class="list_multi_sites">
										<?php foreach ($agni_list_sites['items'] as $agni_site) { ?> 
										<li>
											<?php echo anchor(current_protocol().$agni_site->site_domain.site_path('site-admin'), $agni_site->site_name); ?> 
										</li>
										<?php } // endforeach; ?> 
									</ul>
									<?php } // endif; ?> 
								</li>
								<li>
									<?php echo anchor(site_url(), lang('admin_nav_visit_site')); ?> 
									<?php if (isset($agni_list_sites['items']) && is_array($agni_list_sites['items']) && !empty($agni_list_sites['items'])) { ?> 
									<ul class="list_multi_sites">
										<?php foreach ($agni_list_sites['items'] as $agni_site) { ?> 
										<li>
											<?php echo anchor(current_protocol().$agni_site->site_domain.site_path(), $agni_site->site_name); ?> 
										</li>
										<?php } // endforeach; ?> 
									</ul>
									<?php } // endif; ?> 
								</li>
								<?php if (check_admin_permission('config_global', 'config_global')): ?><li><?php echo anchor('site-admin/config', lang('admin_nav_global_config')); ?></li><?php endif; ?> 
								<li><?php echo anchor('#', lang('admin_nav_tools'), array('onclick' => 'return false;')); ?>
									<?php if (check_admin_permission('urls_perm', 'urls_perm_view_all')): ?> 
									<ul>
										<?php if (check_admin_permission('urls_perm', 'urls_perm_view_all')): ?><li><?php echo anchor('site-admin/urls', lang('admin_nav_url_redirect')); ?></li><?php endif; ?> 
										<li><?php echo anchor('site-admin/cacheman', lang('admin_nav_cache_manager')); ?></li>
									</ul>
									<?php endif; ?> 
								</li>
							</ul>
						</li>
						<li><?php echo anchor('#', lang('admin_nav_users_roles_and_permssions'), array('onclick' => 'return false;')); ?> 
							<ul>
								<?php if (check_admin_permission('account_perm', 'account_manage_perm')): ?><li><?php echo anchor('site-admin/account', lang('admin_nav_users')); ?></li><?php endif; ?> 
								<?php if (check_admin_permission('account_perm', 'account_add_perm')): ?><li><?php echo anchor('site-admin/account/add', lang('admin_nav_add_user')); ?></li><?php endif; ?> 
								<li><?php echo anchor('site-admin/account/edit', lang('admin_nav_edit_profile')); ?></li>
								<?php if (check_admin_permission('account_lv_perm', 'account_lv_manage_perm') || check_admin_permission('account_permission_perm', 'account_permission_manage_perm')): ?><li><?php echo anchor('#', lang('admin_nav_roles_and_permissions'), array('onclick' => 'return false')); ?> 
									<ul>
										<?php if (check_admin_permission('account_lv_perm', 'account_lv_manage_perm')): ?><li><?php echo anchor('site-admin/account-level', lang('admin_nav_roles')); ?></li><?php endif; ?> 
										<?php if (check_admin_permission('account_permission_perm', 'account_permission_manage_perm')): ?><li><?php echo anchor('site-admin/account-permission', lang('admin_nav_permissions')); ?></li><?php endif; ?> 
									</ul>
								</li><?php endif; ?> 
							</ul>
						</li>
						<li><?php echo anchor('#', lang('admin_nav_content'), array('onclick' => 'return false;')); ?> 
							<ul>
								<?php if (check_admin_permission('category_perm', 'category_viewall_perm')): ?> 
								<li><?php echo anchor('site-admin/category', lang('admin_nav_categories')); ?> 
									<?php if (check_admin_permission('category_perm', 'category_add_perm')): ?> 
									<ul>
										<li><?php echo anchor('site-admin/category/add', lang('admin_nav_add_category')); ?></li>
									</ul>
									<?php endif; ?> 
								</li>
								<?php endif; ?> 
								<?php if (check_admin_permission('post_article_perm', 'post_article_viewall_perm')): ?> 
								<li><?php echo anchor('site-admin/article', lang('admin_nav_articles')); ?> 
									<?php if (check_admin_permission('post_article_perm', 'post_article_add_perm')): ?> 
									<ul>
										<li><?php echo anchor('site-admin/article/add', lang('admin_nav_add_article')); ?></li>
									</ul>
									<?php endif; ?> 
								</li>
								<?php endif; ?> 
								<?php if (check_admin_permission('post_page_perm', 'post_page_viewall_perm')): ?> 
								<li><?php echo anchor('site-admin/page', lang('admin_nav_pages')); ?> 
									<?php if (check_admin_permission('post_page_perm', 'post_page_add_perm')): ?> 
									<ul>
										<li><?php echo anchor('site-admin/page/add', lang('admin_nav_add_page')); ?></li>
									</ul>
									<?php endif; ?> 
								</li>
								<?php endif; ?> 
								<?php if (check_admin_permission('tag_perm', 'tag_viewall_perm')): ?> 
								<li><?php echo anchor('site-admin/tag', lang('admin_nav_tags')); ?> 
									<?php if (check_admin_permission('tag_perm', 'tag_add_perm')): ?> 
									<ul>
										<li><?php echo anchor('site-admin/tag/add', lang('admin_nav_add_tag')); ?></li>
									</ul>
									<?php endif; ?> 
								</li>
								<?php endif; ?> 
								<?php if (check_admin_permission('media_perm', 'media_viewall_perm')): ?> 
								<li><?php echo anchor('site-admin/media', lang('admin_nav_media_mgr')); ?></li>
								<?php endif; ?> 
								<?php if (check_admin_permission('comment_perm', 'comment_viewall_perm')): ?> 
								<li><?php 
									$count_comment = $this->db->where('language',$this->lang->get_current_lang())->where('comment_status', '0')->where('comment_spam_status', 'normal')->count_all_results('comments');
									echo anchor('site-admin/comment', lang('admin_nav_comment').($count_comment > 0 ? ' '.'<span class="badge badge-important">'.$count_comment.'</span>' : ''));
									?> 
								<?php endif; ?> 
							</ul>
						</li>
						<?php if (check_admin_permission('menu_perm', 'menu_viewall_group_perm') || check_admin_permission('block_perm', 'block_viewall_perm')): ?>
						<li><?php echo anchor('#', lang('admin_nav_menuandblock'), array('onclick' => 'return false;')); ?>
							<ul>
								<?php if (check_admin_permission('menu_perm', 'menu_viewall_group_perm')): ?><li><?php echo anchor('site-admin/menu', lang('admin_nav_menu')); ?></li><?php endif; ?> 
								<?php if (check_admin_permission('block_perm', 'block_viewall_perm')): ?><li><?php echo anchor('site-admin/block', lang('admin_nav_block')); ?></li><?php endif; ?> 
							</ul>
						</li>
						<?php endif; ?> 
						<li><?php echo anchor('#', lang('admin_nav_component'), array('onclick' => 'return false;')); ?> 
							<?php echo $this->modules_model->loadAdminNav(); ?> 
						</li>
						<li><?php echo anchor('#', lang('admin_nav_extensions'), array('onclick' => 'return false;')); ?> 
							<?php if (check_admin_permission('modules_manage_perm', 'modules_viewall_perm') || check_admin_permission('themes_manage_perm', 'themes_viewall_perm') || check_admin_permission('siteman_perm', 'siteman_manage_perm')): ?> 
							<ul>
								<?php if (check_admin_permission('modules_manage_perm', 'modules_viewall_perm')): ?><li><?php echo anchor('site-admin/module', lang('admin_nav_modules_manager')); ?></li><?php endif; ?> 
								<?php if (check_admin_permission('themes_manage_perm', 'themes_viewall_perm')): ?><li><?php echo anchor('site-admin/themes', lang('admin_nav_themes_manager')); ?></li><?php endif; ?> 
								<?php if (check_admin_permission('siteman_perm', 'siteman_manage_perm')): ?><li><?php echo anchor('site-admin/siteman', lang('admin_nav_multisite_manager')); ?></li><?php endif; ?> 
							</ul>
							<?php endif; ?> 
						</li>
					</ul>
					<div class="clearfix"></div>
				</div><!--.navigations-->
			</div><!--.header-->
			
			
			<div class="body-wrap">
				<?php if (isset($global_status['msg']) && isset($global_status['status']) && !empty($global_status)) { ?> 
				<div class="alert alert-<?php echo $global_status['status']; ?> fade in">
					<button class="close" data-dismiss="alert" type="button">&times;</button>
					<?php echo $global_status['msg']; ?> 
				</div>
				<?php } // endif; 
				unset($global_status);
				?> 
				
				<?php if (isset($page_content)) {echo $page_content;} ?> 
				
			</div><!--.body-wrap-->
			
			
		</div>



		<div class="footer">
			<?php echo lang('admin_credit'); ?> 
		</div><!--.footer-->
		
		
<?php $this->load->view('site-admin/inc_html_foot'); ?>