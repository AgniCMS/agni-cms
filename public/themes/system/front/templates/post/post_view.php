

<article class="post-page post-id-<?php echo $row->post_id; ?>">
	<?php if ( $this->posts_model->is_allow_edit_post( $row ) || $this->posts_model->is_allow_delete_post( $row ) ): ?> 
		<div class="article-tools">
			<div class="btn-group">
				<button type="button" class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
					<span class="icon-asterisk"></span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php if ( $this->posts_model->is_allow_edit_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/edit/'.$row->post_id, '<span class="icon-pencil"></span> '.lang( 'post_edit' ) ); ?></li><?php endif; ?> 
					<?php if ( $this->posts_model->is_allow_delete_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/delete/'.$row->post_id, '<span class="icon-trash"></span> '.lang( 'post_delete' ) ); ?></li><?php endif; ?> 
				</ul>
			</div>
		</div>
		<?php endif; ?> 
	<?php if ( $content_show_title == 1 || $content_show_time == 1 || $content_show_author == 1 ): ?> 
	<header class="post-page-header">
		<?php if ( $content_show_title == 1 ): ?><h1><?php echo $post_name; ?></h1><?php endif; ?> 
		
		<?php if ( $content_show_time == 1 || $content_show_author == 1 ): ?> 
		<small>
			<?php if ( $content_show_time == 1 ): ?><time pubdate="" datetime="<?php echo gmt_date( 'Y-m-d', $row->post_publish_date_gmt ); ?>"><?php echo $post_publish_date_gmt; ?></time><?php endif; ?> 
			<?php if ( $content_show_author == 1 ): ?><?php echo lang( 'post_by' ); ?> <?php echo $post_author; ?> <?php endif; ?> 
		</small>
		<?php endif; ?> 
		
	</header>
	<?php endif; ?> 
	
	<div class="entry">
		<?php echo $body_value; ?> 
	</div>
	
	<?php if ( ( isset( $list_category ) && $list_category != null ) || ( isset( $list_tag ) && $list_tag != null ) ): ?> 
	<aside class="taxonomy-term">
		<ul class="items">
			<?php if ( isset( $list_category ) && $list_category != null ): ?> 
			<li class="category">
				<h3><?php echo lang( 'category_category' ); ?></h3>
				<ul>
					<?php foreach ( $list_category as $term ): ?> 
					<li><?php echo anchor( $term->t_uris, $term->t_name ); ?></li>
					<?php endforeach; ?> 
				</ul>
			</li>
			<?php endif; ?> 
			<?php if ( isset( $list_tag ) && $list_tag != null ): ?> 
			<li class="tag">
				<h3><?php echo lang( 'tag_tag' ); ?></h3>
				<ul>
					<?php foreach ( $list_tag as $term ): ?> 
					<li><?php echo anchor( 'tag/'.$term->t_uris, $term->t_name ); ?></li>
					<?php endforeach; ?> 
				</ul>
			</li>
			<?php endif; ?> 
		</ul>
	</aside>
	<?php endif; ?> 
	
	<?php 
	// load comment
	$this->load->module( 'comment' );
	if ( method_exists( $this->comment, 'list_comments' ) ) {
		echo $this->comment->list_comments( $comment_allow, $row->post_id );
	}
	?> 
	
</article>

