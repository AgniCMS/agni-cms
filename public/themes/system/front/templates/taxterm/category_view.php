

<section class="page-category">
	<h1 class="page-category-name"><?php echo $cat->t_name; ?></h1>
	<?php if ($cat->t_description != null): ?>
	<div class="page-category-description category-id-<?php echo $cat->tid; ?>"><?php echo $cat->t_description; ?></div>
	<?php endif; ?> 
</section>

<?php $this->load->view('front/templates/_inc/inc_loop_posts'); ?> 

