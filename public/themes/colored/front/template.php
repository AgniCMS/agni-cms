<?php include(dirname(__FILE__).'/functions.php'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo strtolower(config_item('charset')); ?>" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if (isset($page_meta)) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
			<script src="<?php echo $this->theme_path; ?>share-js/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>960/reset.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>960/text.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>960/960.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/form.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>front/style.css" media="all" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->base_url; ?>public/css-fw/beauty-buttons/beauty-buttons.css" media="all" />
		<?php if (in_array($this->uri->uri_string(), array('/account/edit-profile'))) { ?> 
		<link rel="stylesheet" type="text/css" href="<?php echo $this->theme_path; ?>share-js/jquery-ui/css/smoothness/jquery-ui.css" />
		<?php } // endif; ?> 
		<?php if (isset($page_link)) {echo $page_link;} ?> 
		
		<script src="<?php echo $this->theme_path; ?>share-js/jquery.min.js" type="text/javascript"></script>
		<?php if (in_array($this->uri->uri_string(), array('/account/edit-profile'))) { ?> 
		<script type="text/javascript" src="<?php echo $this->theme_path; ?>share-js/jquery-ui/jquery-ui.min.js"></script>
		<?php } // endif; ?> 
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
		
		
		<div class="container container_12 page-header">
			<div class="inner-page-header">
				<?php $header_tag = (current_url() == site_url() || current_url() == site_url('/') ? 'h1' : 'div');?><<?php echo $header_tag; ?> class="grid_12 site-name"><a href="<?php echo site_url(); ?>"><?php echo $this->config_model->loadSingle('site_name'); ?></a></<?php echo $header_tag; ?>>
				<div class="grid_12 page-banner<?php if ($area_banner == null) {echo ' default-banner';} ?>">
					<?php echo $area_banner; ?> 
				</div>
				<div class="grid_12 navbar">
					<?php echo $area_navigation; ?> 
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
		
		<div class="container container_12 body-wraper">
			<div class="grid_12 content-wraper">
				<div class="content-inner-wraper">
					
					<?php echo $page_content; ?> 
					
				</div>
			</div>
			<div class="clear"></div>
			
			<div class="grid_12 page-footer">
				<div class="inner-page-footer">
					<div class="footer-nav footer1 grid_4 alpha">
						<?php echo $area_footer1; ?> 
					</div>
					<div class="footer-nav footer2 grid_4">
						<?php echo $area_footer2; ?> 
					</div>
					<div class="footer-nav footer3 grid_4 omega">
						<?php echo $area_footer3; ?> 
					</div>
					<div class="clear"></div>
					<small>Powered by <a href="http://www.agnicms.org">Agni CMS</a></small>
				</div>
			</div>
		</div>
		
		
	</body>
</html>
