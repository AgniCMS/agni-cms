<div class="list-posts">
	<?php
	if ( isset( $list_item['items'] ) ) {
		foreach ( $list_item['items'] as $row ) {
			$post_url = (isset( $cat ) ? $cat->t_uris.'/'.$row->post_uri_encoded : 'post/'.$row->post_uri_encoded );
			?> 
	<article class="each-post post-id-<?php echo $row->post_id; ?>">
		
		<?php if ( $this->posts_model->is_allow_edit_post( $row ) || $this->posts_model->is_allow_delete_post( $row ) ): ?> 
		<div class="article-tools">
			<div class="btn-group">
				<button type="button" class="btn btn-mini dropdown-toggle" data-toggle="dropdown">
					<span class="icon-asterisk"></span>
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu">
					<?php if ( $this->posts_model->is_allow_edit_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/edit/'.$row->post_id, '<span class="icon-pencil"></span> '.lang( 'post_edit_article' ) ); ?></li><?php endif; ?> 
					<?php if ( $this->posts_model->is_allow_delete_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/delete/'.$row->post_id, '<span class="icon-trash"></span> '.lang( 'post_delete_article' ) ); ?></li><?php endif; ?> 
				</ul>
			</div>
		</div>
		<?php endif; ?> 
		
		<header>
			<h1><?php echo anchor( $post_url, $row->post_name ); ?></h1>
			<small>
				<time pubdate="" datetime="<?php echo gmt_date( 'Y-m-d', $row->post_publish_date_gmt ); ?>"><?php echo gmt_date( 'j F Y', $row->post_publish_date_gmt ); ?></time>
				<?php echo lang( 'post_by' ); ?> <?php echo anchor( 'author/'.$row->account_username, $row->account_username, array( 'rel' => 'author' ) ); ?> 
			</small>
		</header>
		
		<div class="row-fluid">
			<div class="span3">
				<a href="<?php echo site_url( $post_url ); ?>">
					<?php 
					if ( $row->post_feature_image != null ) {
						$this->load->model( 'media_model' );
					?> 
					<img src="<?php echo $this->media_model->get_img( $row->post_feature_image, '' ); ?>" alt="" class="post-feature-image" />
					<?php 
					} else {
					?> 
					<img src="<?php echo $this->theme_path; ?>front/images/no-feature-image.png" alt="" class="post-feature-image" />
					<?php 
					}
					?> 
				</a>
			</div>
			<div class="span9 entry">
				<?php if ( $row->body_summary != null ) {
					echo $row->body_summary;
				} else {
					echo mb_strimwidth( nl2br( strip_tags( $row->body_value ) ), 0, 255, '...' );
				} ?>
			</div>
			<div class="clearfix"></div>
		</div>
		
		<?php if ( !empty( $row->comment_count ) ): ?><footer><?php echo anchor( $post_url.'#list-comments', sprintf( lang( 'post_total_comment' ), $row->comment_count ) ); ?></footer><?php endif; ?> 
		
	</article>
		<?php
		}// endforeach;
	}// endif isset( $list_item )
	// pagination
	if ( isset( $pagination ) ) {
		echo $pagination;
	}
	?> 
</div>