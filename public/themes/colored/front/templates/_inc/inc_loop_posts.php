<div class="list-posts">
	<?php
	if ( isset( $list_item['items'] ) ) {
		$column = 1;
		foreach ( $list_item['items'] as $row ) {
			$post_url = (isset( $cat ) ? $cat->t_uris.'/'.$row->post_uri_encoded : 'post/'.$row->post_uri_encoded );
			?> 
	<div class="each-post post-id-<?php echo $row->post_id; ?> grid_4<?php if ( $column == 1 ) { echo ' alpha';} elseif ( $column == 3 ) {echo ' omega';} ?>">
		<?php if ( $this->posts_model->is_allow_edit_post( $row ) || $this->posts_model->is_allow_delete_post( $row ) ): ?> 
		<div class="article-tools">
			<div class="tools-start">
				<div class="tools-container">
					<ul>
						<?php if ( $this->posts_model->is_allow_edit_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/edit/'.$row->post_id, '<span class="ico16-edit"></span>' ); ?></li><?php endif; ?> 
						<?php if ( $this->posts_model->is_allow_delete_post( $row ) ): ?><li><?php echo anchor( 'site-admin/'.($row->post_type == 'article' ? 'article' : 'page').'/delete/'.$row->post_id, '<span class="ico16-delete"></span>' ); ?></li><?php endif; ?> 
					</ul>
				</div>
			</div>
		</div>
		<?php endif; ?> 
		<div class="post-header">
			<h2 class="random-bg<?php echo rand(1, 5); ?>"><?php echo anchor( $post_url, $row->post_name ); ?></h2>
			<small>
				<span title="<?php echo gmt_date( 'Y-m-d', $row->post_publish_date_gmt ); ?>"><?php echo gmt_date( 'j F Y', $row->post_publish_date_gmt ); ?></span>
				<?php echo lang( 'post_by' ); ?> <?php echo anchor( 'author/'.$row->account_username, $row->account_username, array( 'rel' => 'author' ) ); ?> 
			</small>
		</div>
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
		<div class="entry">
			<?php if ( $row->body_summary != null ) {
				echo $row->body_summary;
			} else {
				echo mb_strimwidth( nl2br( strip_tags( $row->body_value ) ), 0, 255, '...' );
			} ?>
		</div>
		<div class="clear"></div>
		<?php if ( !empty( $row->comment_count ) ): ?><div><?php echo anchor( $post_url.'#list-comments', sprintf( lang( 'post_total_comment' ), $row->comment_count ) ); ?></div><?php endif; ?> 
	</div>
		<?php
			$column++;
			if ( $column >= 4 ) {
				$column = 1;
			}
		}
	}
	// pagination
	if ( isset( $pagination ) ) {
		echo $pagination;
	}
	?> 
</div>