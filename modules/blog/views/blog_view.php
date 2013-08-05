<div class="blog-list-posts list-posts">
	<h1><?php echo lang('blog_blog'); ?></h1>

	<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
	<?php foreach ($list_item['items'] as $row): ?> 
	<article class="each-post">
		<header>
			<h2><?php echo $row->blog_title; ?></h2>
			<small>
				<time><?php echo date('Y-m-d H:i:s', $row->blog_date); ?></time>
				<?php echo lang('blog_by').' '.$row->account_username; ?>
			</small>
		</header>
		<div class="content">
			<?php echo $row->blog_content; ?>
		</div>
	</article>
	<?php endforeach; ?> 
	<?php endif; ?> 
	
	<?php if (isset($pagination)) {echo $pagination;} ?> 
</div>