<h1><?php echo lang('post_articles'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<button type="button" class="bb-button btn" onclick="window.location=site_url+'site-admin/article/add';"><?php echo lang('admin_add'); ?></button>
		| <?php echo sprintf(lang('admin_total'), $list_item['total']); ?> 
		| <select name="tid" onchange="change_redirect(this)"><option value="<?php echo current_url(); ?>">&lt;root&gt;</option><?php 
			echo show_category_filter($list_category, $orders, $sort, $q);
			function show_category_filter($list_category, $orders, $sort, $q) {
				if (!is_array($list_category)) {return ;}
				$ci =& get_instance();
				$output = '';
				foreach ($list_category as $item) {
					$output .= '<option value="'.current_url().'?orders='.$orders.'&amp;sort='.strip_tags(trim($ci->input->get('sort'))).'&amp;q='.$q.'&amp;tid='.$item->tid.'"';
					if ($ci->input->get('tid') == $item->tid) {
						$output .= ' selected="selected"';
					}
					$output .= '>'.str_repeat('-', $item->nlevel-1).$item->t_name.'</option>';
					if (property_exists($item, 'childs')) {
						$output .= show_category_filter($item->childs, $orders, $sort, $q);
					}
				}
				return $output;
			} ?></select>
	</div>
	<div class="cmd-right">
		<form method="get" class="form-search">
			<input type="text" name="q" value="<?php echo htmlspecialchars(trim($this->input->get('q'))); ?>" maxlength="255" />
			<button type="submit" class="bb-button standard btn"><?php echo lang('post_search'); ?></button>
		</form>
	</div>
	<div class="clearfix"></div>
</div>

<?php echo form_open('site-admin/article/process_bulk'); ?> 
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 

	<table class="table table-striped table-hover">
		<thead>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=post_name&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('post_article_name')); ?></th>
				<th><?php echo lang('post_author_name'); ?></th>
				<th><?php echo anchor(current_url().'?orders=post_status&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('post_status')); ?></th>
				<th><?php echo lang('post_categories'); ?></th>
				<th><?php echo lang('post_tags'); ?></th>
				<th><?php echo anchor(current_url().'?orders=theme_system_name&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('admin_theme')); ?></th>
				<th><?php echo lang('post_date'); ?></th>
				<th><?php echo anchor(current_url().'?orders=view_count&amp;sort=' . $sort . '&amp;q=' . $q . '&amp;tid=' . $tid, lang('post_view_count')); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="check-column"><input type="checkbox" name="id_all" value="" onclick="checkAll(this.form,'id[]',this.checked)" /></th>
				<th><?php echo anchor(current_url().'?orders=post_name&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('post_article_name')); ?></th>
				<th><?php echo lang('post_author_name'); ?></th>
				<th><?php echo anchor(current_url().'?orders=post_status&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('post_status')); ?></th>
				<th><?php echo lang('post_categories'); ?></th>
				<th><?php echo lang('post_tags'); ?></th>
				<th><?php echo anchor(current_url().'?orders=theme_system_name&amp;sort='.$sort.'&amp;q='.$q.'&amp;tid='.$tid, lang('admin_theme')); ?></th>
				<th><?php echo lang('post_date'); ?></th>
				<th><?php echo anchor(current_url().'?orders=view_count&amp;sort=' . $sort . '&amp;q=' . $q . '&amp;tid=' . $tid, lang('post_view_count')); ?></th>
				<th></th>
			</tr>
		</tfoot>
		<tbody>
		<?php $cur_sort = $this->input->get('sort', true);// current sort ?> 
		<?php if (isset($list_item['items']) && is_array($list_item['items'])): ?> 
		<?php foreach ($list_item['items'] as $row): ?> 
			<tr>
				<td class="check-column"><?php echo form_checkbox('id[]', $row->post_id); ?></td>
				<td>
					<?php if ($this->input->get('tid') != null && $this->account_model->checkAdminPermission('post_article_perm', 'post_article_sort_perm') && (($orders == 'position' && ($cur_sort == 'desc' || $cur_sort == null)) || $orders == null)): ?>
					<span class="reorder" title="<?php echo $row->position; ?>">
						<?php echo anchor('site-admin/article/reorder/'.$row->post_id.'/'.$this->input->get('tid').'/up', '<span class="icon-arrow-up"></span>', array('class' => 'up')); ?> 
						<?php echo anchor('site-admin/article/reorder/'.$row->post_id.'/'.$this->input->get('tid').'/dn', '<span class="icon-arrow-down"></span>', array('class' => 'down')); ?> 
					</span><?php endif; ?> 
					<?php echo anchor(current_url().'/edit/'.$row->post_id, $row->post_name); ?>
				</td>
				<td><?php echo anchor('site-admin/account/edit/'.$row->account_id, $row->account_username); ?></td>
				<td><?php echo ($row->post_status == '1' ? lang('post_published') : lang('post_draft')); ?></td>
				<td><?php
					$this->taxonomy_model->tax_type = 'category';
					$list_taxterm_index = $this->taxonomy_model->listTaxTermIndex($row->post_id);
					if (!empty($list_taxterm_index)) {
						$count = 1;
						foreach ($list_taxterm_index as $tax) {
							echo anchor('site-admin/category/edit/'.$tax->tid, $tax->t_name);
							$count++;
							if ($count <= count($list_taxterm_index)) {
								echo ', ';
							}
						}
					}
				?></td>
				<td><?php
					$this->taxonomy_model->tax_type = 'tag';
					$list_taxterm_index = $this->taxonomy_model->listTaxTermIndex($row->post_id);
					if (!empty($list_taxterm_index)) {
						$count = 1;
						foreach ($list_taxterm_index as $tax) {
							echo anchor('site-admin/tag/edit/'.$tax->tid, $tax->t_name);
							$count++;
							if ($count < 11 && $count <= count($list_taxterm_index)) {
								echo ', ';
							}
						}
					}
				?></td>
				<td><?php
				// read theme name
				if ($row->theme_system_name != null) {
					$theme_data = $this->themes_model->readThemeMetadata($row->theme_system_name.'/'.$row->theme_system_name.'.info');
					echo anchor('area/demo/'.$row->theme_system_name, $theme_data['name']);
					unset($theme_data);
				}
				?></td>
				<td>
					<?php echo lang('post_add_since'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_add_gmt); ?><br />
					<?php echo lang('post_update_since'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_update_gmt); ?><br />
					<?php echo lang('post_publish'); ?>: <?php echo gmt_date('Y-m-d H:i:s', $row->post_publish_date_gmt); ?>
				</td>
				<td><?php echo $row->view_count; ?></td>
				<td>
					<?php if (($this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_own_perm') && $row->account_id == $my_account_id) || ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_edit_other_perm') && $row->account_id != $my_account_id)): ?>
					<?php echo anchor(current_url().'/edit/'.$row->post_id, lang('admin_edit')); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php endforeach; ?> 
		<?php else: ?> 
			<tr>
				<td colspan="10"><?php echo lang('admin_nodata'); ?></td>
			</tr>
		<?php endif; ?> 
		</tbody>
	</table>

	<div class="cmds">
		<div class="cmd-left">
			<select name="act">
				<option value="" selected="selected"></option>
				<?php if ($this->account_model->checkAdminPermission('post_article_perm', 'post_article_publish_unpublish_perm')): ?> 
				<option value="publish"><?php echo lang('post_publish'); ?></option>
				<option value="unpublish"><?php echo lang('post_unpublished'); ?></option>
				<option value="">---------</option>
				<?php endif; ?> 
				<option value="del"><?php echo lang('admin_delete'); ?></option>
			</select>
			<button type="submit" class="bb-button btn btn-warning"><?php echo lang('admin_submit'); ?></button>
		</div>
		<div class="cmd-right">
			<?php if (isset($pagination)) {echo $pagination;} ?>
		</div>
		<div class="clearfix"></div>
	</div>
<?php echo form_close(); ?> 