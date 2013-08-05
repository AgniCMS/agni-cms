<?php
if (isset($values['block_title']) && $values['block_title'] != null) {
	echo '<h3>'.$values['block_title'].'</h3>';
}
?>
<?php if (isset($result) && is_array($result)): ?> 
<ul>
	<?php foreach ($result as $row): ?> 
	<li><?php echo anchor('post/'.$row->post_uri_encoded, $row->post_name); ?></li>
	<?php endforeach; ?> 
</ul>
<?php endif; ?> 