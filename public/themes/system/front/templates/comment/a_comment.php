<article class="each-comment <?php echo $comment_class; ?>">
	<div class="comment-wrapper">
		<footer>
			<div class="comment-author">
				<?php
				if ($comment->account_avatar != null) {
					$comment_avatar = base_url($comment->account_avatar);
				} else {
					$comment_avatar = base_url('public/images/default-avatar.png');
				}
				?> 
				<img src="<?php echo $comment_avatar; ?>" alt="" class="comment-avatar" />
				<?php if ($comment->account_id != null || $comment->account_id != '0') {
					echo '<a href="#comment-id-'.$comment->comment_id/*$comment->c1_account_id*/.'">'.$comment->name.'</a>';
				} else {
					echo $comment->name;
				} ?> 
				<time pubdate="" datetime="<?php echo gmt_date('Y-m-d H:i:s', $comment->comment_add_gmt); ?>"><?php echo gmt_date('d F Y H:i', $comment->comment_add_gmt); ?></time>
			</div>
		</footer>
		<h3><?php echo $comment->subject; ?></h3>
		<p class="comment-content"><?php echo $comment_content; ?></p>
		<?php if ($comment_edit_permission === true || $comment_delete_permission === true || $comment_postreply_permission === true): ?> 
		<div class="comment-tools">
			<ul>
				<?php if ($comment_edit_permission === true): ?><li><?php echo anchor('comment/edit/'.$comment->comment_id, lang('comment_edit')); ?></li><?php endif; ?> 
				<?php if ($comment_delete_permission === true): ?><li><?php echo anchor('comment/delete/'.$comment->comment_id, lang('comment_delete')); ?></li><?php endif; ?> 
				<?php if ($comment_postreply_permission === true): ?><li><?php echo anchor(current_url().'?replyto='.$comment->comment_id.'&amp;per_page='.strip_tags(trim($this->input->get('per_page'))).'#addcomment', lang('comment_reply')); ?></li><?php endif; ?> 
			</ul>
		</div>
		<?php endif; ?> 
	</div>
</article>
