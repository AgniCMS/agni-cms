<?php include( dirname(__FILE__).'/functions.php' ); ?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower( config_item( 'charset' ) ); ?>" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( isset( $page_meta ) ) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->base_url; ?>public/js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960adapt/css/master.css" />
		<noscript><link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960adapt/css/960.min.css" /></noscript>
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/form.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/style.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/beauty-buttons/beauty-buttons.css" />
		<?php if ( isset( $page_link ) ) {echo $page_link;} ?> 
		<script src="<?php echo $this->base_url; ?>public/js/jquery.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			var ADAPT_CONFIG = {
				// Where is your CSS?
				path: '<?php echo $this->base_url; ?>public/css-fw/960adapt/css/',
				
				// false = Only run once, when page first loads.
				// true = Change on window resize and page tilt.
				dynamic: true,
				
				// First range entry is the minimum.
				// Last range entry is the maximum.
				// Separate ranges by "to" keyword.
				range: [
					'0px    to 760px  = mobile.css',
					'760px  to 980px  = 720.min.css',
					'980px  to 1280px = 960.min.css',
					'1280px to 1600px = 1200.min.css',
					'1600px to 1920px = 1200.min.css',// replace 1560
					'1940px to 2540px = 1200.min.css',// replace 1920
					'2540px           = 1200.min.css'// replace 2520
				]
			};
		</script>
		<script src="<?php echo $this->base_url; ?>public/css-fw/960adapt/js/adapt.min.js" type="text/javascript"></script>
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
		
		
		<div class="container_12 page-header">
			<header class="inner-page-header">
				<div class="page-header-top">
					<?php $header_tag = (current_url() == site_url() || current_url() == site_url( '/' ) ? 'h1' : 'div' );?><<?php echo $header_tag; ?> class="grid_5 site-name"><a href="<?php echo site_url(); ?>"><?php echo $this->config_model->load_single( 'site_name' ); ?></a></<?php echo $header_tag; ?>>
					<div class="grid_7 account-header-area">
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
				<nav class="grid_12 navbar">
					<?php echo $area_navigation; ?> 
					<div class="clear"></div>
				</nav>
			</header>
		</div>
		
		<div class="container_12 body-wraper">
			<div class="grid_9 content-wraper">
				<div class="content-inner-wraper">
					<?php if ( $area_breadcrumb != null ): ?><div class="breadcrumb"><?php echo $area_breadcrumb; ?></div><?php endif; ?> 
					
					<?php echo $page_content; ?> 
					
				</div>
			</div>
			<?php if ( $area_sidebar != null ): ?> 
			<div class="grid_3 sidebar rightbar">
				<?php echo $area_sidebar; ?> 
			</div>
			<?php endif; ?> 
			<div class="clear"></div>
			
			<div class="grid_12 page-footer">
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
