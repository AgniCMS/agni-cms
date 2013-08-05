<table class="table table-striped table-hover media-list-items">
	<thead>
		<tr>
			<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_name')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=media_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_name')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_mime_type&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_mime_type')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_size&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_size')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_add&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_add_date')); ?></th>
			<th></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_name')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=media_name&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_name')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_mime_type&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_mime_type')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_size&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_file_size')); ?></th>
			<th><?php echo anchor(current_url().'?current_path='.urlencode($current_path).'&amp;orders=file_add&amp;sort='.$sort.'&amp;filter='.$filter.'&amp;filter_val='.$filter_val.($q != null ?'&amp;q='.$q : ''), lang('media_add_date')); ?></th>
			<th></th>
		</tr>
	</tfoot>
	
	<tbody>
	<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
	<?php foreach ($list_item['items'] as $row): ?> 
		<tr>
			<td class="check-column"><?php echo form_checkbox('id[]', $row->file_id); ?></td>
			<td>
				<?php if (file_exists($row->file)) {
					list($width, $height) = getimagesize($row->file);
				} ?> 
				<?php if (isset($width) && isset($height) && is_numeric($width) && is_numeric($height) && (strtolower($row->file_ext) == '.jpg' || strtolower($row->file_ext) == '.jpeg' || strtolower($row->file_ext) == '.gif' || strtolower($row->file_ext) == '.png')): ?> 
				<a href="<?php echo base_url().$row->file; ?>"><img src="<?php echo base_url().$row->file; ?>" alt="<?php echo $row->file_name; ?>" class="media-thumbnail" /></a>
				<?php echo $width; ?> x <?php echo $height; ?><br />
				<?php endif; ?> 
				<?php echo anchor(base_url().$row->file, $row->file_name, array('title' => $row->file_client_name)); ?>
				<div class="clearfix"></div>
			</td>
			<td><?php echo $row->media_name; ?></td>
			<td><?php echo $row->file_mime_type; ?></td>
			<td><?php $size = get_file_info($row->file, 'size'); 
				echo easy_filesize($size['size']); 
			?></td>
			<td><?php echo gmt_date('Y-m-d H:i:s', $row->file_add_gmt); ?></td>
			<td>
				<ul class="actions-inline">
					<li><?php echo anchor('site-admin/media/edit/'.$row->file_id, lang('admin_edit')); ?></li>
					<li><?php echo anchor('site-admin/media/copy/'.$row->file_id, lang('media_copy')); ?></li>
				</ul>
			</td>
		</tr>
	<?php endforeach; ?> 
	<?php else: ?> 
		<tr>
			<td colspan="7"><?php echo lang('admin_nodata'); ?></td>
		</tr>
	<?php endif; ?> 
	</tbody>
</table>
