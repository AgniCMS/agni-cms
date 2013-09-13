<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower(config_item('charset')); ?>">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width">
		<?php if (isset($page_meta)) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->theme_path; ?>share-js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap-responsive.min.css" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-js/jquery-ui/css/smoothness/jquery-ui.css" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>site-admin/style.css" media="all">
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>site-admin/superfish.css" media="all">
		<?php if ($this->uri->uri_string() == '/site-admin/login') { ?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>site-admin/style-login.css" media="all">
		<?php } // endif; ?> 
		<?php if (isset($page_link)) {echo $page_link;} ?> 
		
		<script src="<?php echo $this->theme_path; ?>share-js/jquery.min.js" type="text/javascript"></script>
		<script src="<?php echo $this->theme_path; ?>share-css/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/jquery.cookie.js" type="text/javascript"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/admin.js" type="text/javascript"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/superfish/hoverIntent.js"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/superfish/superfish.js"></script>
		<script src="<?php echo $this->theme_path; ?>share-js/superfish/supersubs.js"></script>
		<?php if (isset($page_script)) {echo $page_script;} ?> 
		
		<!--.js up to page.-->
		<?php if ($this->uri->segment(2) == 'article' || $this->uri->segment(2) == 'page') { ?> 
		<script src="<?php echo $this->theme_path; ?>share-js/jquery.textarea.js"></script>
		<?php } ?> 
		<?php if ($this->uri->segment(2) == 'category' || $this->uri->segment(2) == 'menu') { ?> 
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/jquery.mjs.nestedSortable.js"></script>
		<?php } ?> 
		<?php if ($this->uri->segment(2) == 'media' && $this->uri->segment(3) == 'edit') { ?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-js/jcrop/css/jquery.Jcrop.min.css">
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/jcrop/js/jquery.Jcrop.min.js"></script>
		<?php } ?> 
		
		<script type="text/javascript">
			// declare variable for use in .js file
			var base_url = '<?php echo $this->base_url; ?>';
			var site_url = '<?php echo site_url('/'); ?>';
			var csrf_name = '<?php echo config_item('csrf_token_name'); ?>';
			var csrf_value = '<?php echo $this->security->get_csrf_hash(); ?>';
		</script>
		<?php echo $this->modules_plug->do_filter('admin_html_head'); ?> 
	</head>
	
	<body>
