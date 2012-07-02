<?php include( dirname(__FILE__).'/functions.php' ); ?><!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower( config_item( 'charset' ) ); ?>" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( isset( $page_meta ) ) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->base_url; ?>public/js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960/reset.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960/text.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960/960.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/form.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/style.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/beauty-buttons/beauty-buttons.css" media="all" />
		<?php if ( isset( $page_link ) ) {echo $page_link;} ?> 
		<script src="<?php echo $this->base_url; ?>public/js/jquery.min.js" type="text/javascript"></script>
		<?php if ( isset( $page_script ) ) {echo $page_script;} ?> 
		<script type="text/javascript">
			// declare variable for use in .js file
			var base_url = '<?php echo $this->base_url; ?>';
			var site_url = '<?php echo site_url(); ?>/';
			<?php if ( config_item( 'csrf_protection' ) == true ): ?>
			var csrf_name = '<?php echo config_item( 'csrf_token_name' ); ?>';
			var csrf_value = '<?php echo $this->security->get_csrf_hash(); ?>';
			<?php endif; ?> 
		</script>
		<?php if ( isset( $in_head_elements ) ) {echo $in_head_elements;} ?> 
		<?php echo $this->modules_plug->do_action( 'front_html_head' ); ?> 
	</head>
	<body class="body-class<?php echo $this->html_model->gen_front_body_class( 'theme-'.$this->theme_system_name ); ?>">
		
		
		<div class="container_16 page-header">
			<header class="inner-page-header">
				<div class="page-header-top">
					<?php $header_tag = (current_url() == site_url() || current_url() == site_url( '/' ) ? 'h1' : 'div' );?><<?php echo $header_tag; ?> class="grid_6 site-name"><a href="<?php echo site_url(); ?>"><?php echo $this->config_model->load_single( 'site_name' ); ?></a></<?php echo $header_tag; ?>>
					<div class="grid_10 account-header-area">
						<?php
						if ( $this->account_model->is_member_login() ) {
							echo anchor( 'account/edit-profile', lang( 'account_edit_profile' ) );
							echo ' '.anchor( 'account/logout', lang( 'account_logout' ) );
						} else {
							echo anchor( 'account/register', lang( 'account_register' ) );
							echo ' '.anchor( 'account/login', lang( 'account_login' ) );
						}
						?> 
					</div>
					<div class="clear"></div>
				</div>
				<nav class="grid_16 navbar">
					<?php echo $area_navigation; ?> 
					<div class="clear"></div>
				</nav>
			</header>
		</div>
		
		<div class="container_16 body-wraper">
			<div class="grid_12 content-wraper">
				<div class="content-inner-wraper">
					<?php if ( $area_breadcrumb != null ): ?><div class="breadcrumb"><?php echo $area_breadcrumb; ?></div><?php endif; ?> 
					
					<?php echo $page_content; ?> 
					
				</div>
			</div>
			<?php if ( $area_sidebar != null ): ?> 
			<div class="grid_4 sidebar rightbar">
				<!--sidebar prototype-->
				<?php echo $area_sidebar; ?> 
				<!--end sidebar prototype-->
			</div>
			<?php endif; ?> 
			<div class="clear"></div>
			
			<div class="grid_16 page-footer">
				<footer class="inner-page-footer">
					<?php if ( $area_footer != null ): ?> 
					<nav class="footer-nav">
						<?php echo $area_footer; ?> 
					</nav>
					<?php endif; ?> 
					<small>Powered by <a href="http://www.agnicms.org">Agni CMS</a></small>
				</footer>
			</div>
		</div>
		
		
	</body>
</html>
