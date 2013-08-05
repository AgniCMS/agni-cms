<table class="table table-striped table-hover media-list-items">
	<thead>
		<tr>
			<th><?php echo anchor(current_url().'?orders=file_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_name')); ?></th>
			<th><?php echo anchor(current_url().'?orders=file_size&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_size')); ?></th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php echo anchor(current_url().'?orders=file_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_name')); ?></th>
			<th><?php echo anchor(current_url().'?orders=file_size&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_size')); ?></th>
			<th></th>
		</tr>
	</tfoot>
	<tbody>
	<?php if (isset($list_item['items']) && is_array($list_item['items'])) { ?> 
	<?php foreach ($list_item['items'] as $row) { ?> 
		<tr>
			<td>
				<?php if (file_exists($row->file)) {
					list($width, $height) = getimagesize($row->file);
				} ?> 
				<?php if (isset($width) && isset($height) && is_numeric($width) && is_numeric($height) && (strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png')): ?> 
				<a href="<?php echo base_url().$row->file; ?>" target="fullview"><img src="<?php echo base_url().$row->file; ?>" alt="<?php echo $row->file_name; ?>" class="media-thumbnail" /></a>
				<?php echo $width; ?> x <?php echo $height; ?><br />
				<?php endif; ?> 
				<?php echo anchor(base_url().$row->file, $row->media_name, array('title' => $row->file_client_name, 'target' => '_fullview')); ?><br />
				<?php echo anchor(site_url('site-admin/media/edit/'.$row->file_id), lang('admin_edit'), array('target' => '_edit')); ?> 
				<div class="clearfix"></div>
			</td>
			<td><?php $size = get_file_info($row->file, 'size'); 
				echo easy_filesize($size['size']); 
			?></td>
			<td>
				<?php
				// check if is image
				$is_image = false;
				if ((strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png')) {
					$is_image = true;
				}
				
				// if has module plug filter insert tag
				if ($this->modules_plug->has_filter('media_insert_tag')) {
					$insert_tag = $this->modules_plug->do_filter('media_insert_tag', $row);
				} else {
					// if is image
					if ($is_image === true) {
						// insert image element.
						$insert_tag = '<img src="'.base_url($row->file).'" alt="'.$row->media_name.'" />';
					} else {
						// if has module plug filter insert tag in other type.
						if ($this->modules_plug->has_filter('media_insert_tag_other')) {
							$insert_tag = $this->modules_plug->do_filter('media_insert_tag_other');
						} else {
							// use default insert media tag
							$insert_tag = '<a href="'.base_url($row->file).'" title="' . $row->media_name . '">'.$row->file_name.'</a>';
						}
					}
				}
				?>
				<ul class="actions-inline">
					<?php 
					if ($this->modules_plug->has_filter('media_actions')) {
						echo $this->modules_plug->do_filter('media_actions', $row);
					} else {
					?> 
					<li><a href="#" onclick="return insert_media('<?php echo htmlspecialchars($insert_tag, ENT_QUOTES, config_item('charset')); ?>');"><?php echo lang('media_insert'); ?></a></li>
					<?php if (isset($is_image) && $is_image === true): ?><li><a href="#" onclick="return set_feature_image(<?php echo $row->file_id; ?>);"><?php echo lang('media_set_as_feature'); ?></a></li><?php endif; ?> 
					<?php
					}
					?> 
				</ul>
				<?php unset($insert_tag, $is_image, $action_module_plug); ?> 
			</td>
		</tr>
	<?php } //endforeach; ?> 
	<?php } else { ?> 
		<tr>
			<td colspan="3"><?php echo lang('admin_nodata'); ?></td>
		</tr>
	<?php } //endif; ?> 
	</tbody>
</table>