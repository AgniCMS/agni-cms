<!DOCTYPE html>
<html>
	<head>
		<meta charset="<?php echo strtolower( config_item( 'charset' ) ); ?>" />
		<title><?php echo $page_title; ?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<?php if ( isset( $page_meta ) ) {echo $page_meta;} ?> 
		<!--[if lt IE 9]>
		  <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/bootstrap/css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/bootstrap/css/bootstrap-responsive.min.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>public/styles/style.css" />
		<?php if ( isset( $page_link ) ) {echo $page_link;} ?> 
		<script src="<?php echo base_url(); ?>../public/js/jquery.min.js" type="text/javascript"></script>
		<?php if ( isset( $page_script ) ) {echo $page_script;} ?> 
		<script type="text/javascript">
			// declare variable for use in .js file
			var base_url = '<?php echo base_url(); ?>';
			var site_url = '<?php echo site_url(); ?>/';
			var csrf_name = '<?php echo config_item( 'csrf_token_name' ); ?>';
			var csrf_value = '<?php echo $this->security->get_csrf_hash(); ?>';
		</script>
		
	</head>
	<body>
		<div class="container content-wrapper">
			<div class="row">
				<div class="span10">
					<div class="content-inner-wrapper">
						<div class="site-name"><?php echo lang( 'agni_agnicms' ); ?></div>
					</div>
				</div>
				<div class="span2">
					<div class="content-inner-wrapper">
						<?php echo language_switch_select(); ?>
					</div>
				</div>
				<div class="clear"></div>
				<div class="span12">
					<div class="content-inner-wrapper">
						
						<ul class="install-step">
							<li class="each-step<?php if ( current_url() == base_url() || current_url() == site_url() ) {echo ' active current';} ?>"><a href="<?php echo base_url(); ?>"><?php echo lang( 'agni_install_requirement' ); ?></a></li>
							<li class="each-step<?php if ( $this->uri->segment(2) == 'step2' ) {echo ' active current';} ?>"><a><?php echo lang( 'agni_install_db' ); ?></a></li>
							<li class="each-step<?php if ( $this->uri->segment(2) == 'step3' ) {echo ' active current';} ?>"><a><?php echo lang( 'agni_configure_website' ); ?></a></li>
							<li class="each-step<?php if ( $this->uri->segment(2) == 'finished' ) {echo ' active current';} ?>"><a><?php echo lang( 'agni_install_finished' ); ?></a></li>
						</ul>
					</div>
				</div>
				<div class="clear"></div>
				<div class="span12">
					<div class="content-inner-wrapper">
						<?php echo $page_content; ?> 
					</div>
				</div>
			</div>
		</div><!--.container-->
		
		
		<script src="<?php echo base_url(); ?>public/bootstrap/js/bootstrap-alert.js"></script>
	</body>
</html>