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
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/bootstrap/css/bootstrap.min.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/bootstrap/css/bootstrap-responsive.min.css" media="all" />
		<!--link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/960/960.css" media="all" /-->
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/js/jquery-ui/css/smoothness/jquery-ui.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>site-admin/style.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>site-admin/superfish.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/beauty-buttons/beauty-buttons.css" media="all" />
		<?php if ( isset( $page_link ) ) {echo $page_link;} ?> 
		<script src="<?php echo $this->base_url; ?>public/js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo $this->base_url; ?>public/js/jquery.cookie.js" type="text/javascript"></script>
		<script src="<?php echo $this->base_url; ?>public/js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?php echo $this->base_url; ?>public/js/admin.js" type="text/javascript"></script>
		<script src="<?php echo $this->base_url; ?>public/js/superfish/hoverIntent.js"></script>
		<script src="<?php echo $this->base_url; ?>public/js/superfish/superfish.js"></script>
		<script src="<?php echo $this->base_url; ?>public/js/superfish/supersubs.js"></script>
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
		<?php echo $this->modules_plug->do_action( 'admin_html_head' ); ?> 
	</head>
	<body>
