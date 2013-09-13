<?php include(dirname(__FILE__).'/functions.php'); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower(config_item('charset')); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width" />
		<?php if (isset($page_meta)) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->theme_path; ?>share-js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap-responsive.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/style.css" />
		<?php if (in_array($this->uri->uri_string(), array('/account/edit-profile'))) { ?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-js/jquery-ui/css/smoothness/jquery-ui.css" />
		<?php } // endif; ?> 
		<?php if (isset($page_link)) {echo $page_link;} ?> 
		
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/jquery.min.js"></script>
		<?php if (in_array($this->uri->uri_string(), array('/account/edit-profile'))) { ?> 
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/jquery-ui/jquery-ui.min.js"></script>
		<?php } // endif; ?> 
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-css/bootstrap/js/bootstrap.min.js"></script>
		<?php if (isset($page_script)) {echo $page_script;} ?> 
		
		<script type="text/javascript">
			// declare variable for use in .js file
			var base_url = '<?php echo $this->base_url; ?>';
			var site_url = '<?php echo site_url('/'); ?>';
			var csrf_name = '<?php echo config_item('csrf_token_name'); ?>';
			var csrf_value = '<?php echo $this->security->get_csrf_hash(); ?>';
		</script>
		
		<?php if (isset($in_head_elements)) {echo $in_head_elements;} ?> 
		<?php echo $this->modules_plug->do_filter('front_html_head'); ?> 
	</head>
	<body class="body-class<?php echo $this->html_model->gen_front_body_class('theme-'.$this->theme_system_name); ?>">
		
		
		<div class="container page-container">
			<header class="row-fluid page-header-row clearfix">
				<div class="span12">
					<h1 class="pull-left brand"><?php echo anchor(base_url(), $this->config_model->loadSingle('site_name'), array('rel' => 'home', 'class' => 'site-name site-title')); ?></h1>
					<div class="pull-right account-header-area">
						<?php
						if ($this->account_model->isMemberLogin()) {
							echo anchor('account/edit-profile', lang('account_edit_profile'));
							echo ' '.anchor('account/logout', lang('account_logout'));
						} else {
							echo anchor('account/register', lang('account_register'));
							echo ' '.anchor('account/login', lang('account_login'));
						}
						?> 
					</div>
					<div class="clearfix"></div>
					<nav role="navigation" class="navigation site-navigation">
						<?php echo $area_navigation; ?> 
						<div class="clearfix"></div>
					</nav>
				</div>
			</header>
			
			<div class="row-fluid page-content-row">
				<div class="span9 primary-column">
					<?php if ($area_breadcrumb != null): ?><div class="breadcrumb-row"><?php echo $area_breadcrumb; ?></div><?php endif; ?> 
					
					<?php echo $page_content; ?> 
					
				</div>
				<div class="span3 sidebar-column">
					<?php echo $area_sidebar; ?> 
				</div>
				<div class="clearfix"></div>
			</div>
		</div>
		<footer class="page-footer-row">
			<div class="container">
				<div class="row">
					<div class="span12">
						<?php if ($area_footer != null) { ?> 
						<div class="pull-right area-footer">
							<?php echo $area_footer; ?> 
						</div>
						<?php } // endif; ?> 
						<small>Powered by <a href="http://www.agnicms.org">Agni CMS</a></small>
					</div>
				</div>
			</div>
		</footer>
		
		
	</body>
</html>
