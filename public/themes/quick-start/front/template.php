<?php include(dirname(__FILE__).'/functions.php'); ?><!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower(config_item('charset')); ?>" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if (isset($page_meta)) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->theme_path; ?>share-js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-css/bootstrap/css/bootstrap-responsive.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/style.css" media="all" />
		<?php if (in_array($this->uri->uri_string(), array('/account/edit-profile'))) { ?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-js/jquery-ui/css/smoothness/jquery-ui.css" />
		<?php } // endif; ?> 
		<?php if (isset($page_link)) {echo $page_link;} ?> 
		
		<script src="<?php echo $this->theme_path; ?>share-js/jquery.min.js" type="text/javascript"></script>
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
		
		
		<div class="container page-header-row">
			<header class="row">
				<h1 class="span12 site-name"><a href="<?php echo site_url(); ?>"><?php echo $this->config_model->loadSingle('site_name'); ?></a></h1>
				<nav class="span12 navbar">
					<?php echo $area_navigation; ?> 
					<div class="clear"></div>
				</nav>
			</header>
		</div>
		
		<div class="container body-wraper">
			<div class="row">
				<div class="span9 content-wraper">
					<div class="content-inner-wraper">

						<?php echo $page_content; ?> 

					</div>
				</div>
				<?php if ($area_sidebar != null): ?> 
				<div class="span3 sidebar rightbar">
					<!--sidebar prototype-->
					<?php echo $area_sidebar; ?> 
					<!--end sidebar prototype-->
				</div>
				<?php endif; ?> 
				<div class="clearfix"></div>
			</div>
			
			<div class="row footer-row">
				<div class="span12 page-footer">
					<footer class="inner-page-footer">
						<small>Powered by <a href="http://www.agnicms.org">Agni CMS</a></small>
					</footer>
				</div>
			</div>
		</div>
		
		
	</body>
</html>
