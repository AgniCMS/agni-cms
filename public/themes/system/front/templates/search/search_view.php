<section class="page-search">
	<form method="get" class="form-search">
		<input type="text" name="q" value="<?php echo$q; ?>" maxlength="255" class="span9 offset1" />
		<button type="submit" class="btn"><?php echo lang( 'search_search' ); ?></button>
	</form>
</section>


<div class="list-posts">
	<?php if ( isset( $list_item['items'] ) ): ?> 
	<?php foreach ( $list_item['items'] as $row ): ?> 
	<?php 
	$list_type = null;
	if ( $row->post_type != null ) {
		$list_type = $row->post_type;
	}
	// list url
	switch( $list_type ) {
		case 'article':
			$list_url = 'post/'.$row->post_uri_encoded;
			break;
		case 'page':
			$list_url = $row->post_uri_encoded;
			break;
		default:
			$list_url = null;
			break;
	}
	$post_url = (isset( $cat ) ? $cat->t_uris.'/'.$row->post_uri_encoded : 'post/'.$row->post_uri_encoded );
	?> 
	
	<article class="each-post <?php echo $list_type; ?>-id-<?php echo $row->post_id; ?>">
		
		<header>
			<h1><?php echo anchor( $list_url, $row->post_name ); ?></h1>
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
		
		<?php if ( !empty( $row->comment_count ) ): ?><footer><?php echo anchor( $list_url.'#list-comments', sprintf( lang( 'post_total_comment' ), $row->comment_count ) ); ?></footer><?php endif; ?> 
	</article>
	
	<?php endforeach; ?> 
	<?php else: ?> 
	
	<?php if ( $q != null ): ?> 
	<p><?php echo lang( 'search_not_found' ); ?></p>
	<?php endif; ?> 
	
	<?php endif; ?> 
	<?php
	// pagination
	if ( isset( $pagination ) ) {
		echo $pagination;
	}
	?> 
</div>

