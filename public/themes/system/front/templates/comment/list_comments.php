<?php if ($comment_allow == 1 || ($comment_allow == 0 && $comment_show_notallow == 1)): ?> 
<section class="comments" id="list-comments">
	
	<h2><?php echo lang('comment_comment'); ?></h2>
	<?php
	echo $list_comments;
	if (isset($pagination)) {echo $pagination;}
	?>
	
	<?php if ($comment_allow == 1) { ?> 
	
	<h2 class="comment-add-title" id="addcomment"><?php echo $comment_add_title; ?></h2>
	
	<?php if (check_admin_permission('comment_perm', 'comment_allowpost_perm', $account_id)) { ?>
	<?php $this->load->view('front/templates/comment/comment_form'); ?> 
	<?php } else { ?>
	<div><?php echo lang('comment_need_member'); ?></div>
	<?php } //endif; ?> 
	
	<?php } //endif; ?> 
		
</section>
<?php endif; ?> 