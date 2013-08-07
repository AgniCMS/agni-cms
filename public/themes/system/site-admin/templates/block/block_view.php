<h1><?php echo lang('block_blocks'); ?></h1>

<div class="cmds">
	<div class="cmd-left">
		<?php echo lang('block_please_select_theme'); ?>:
		<select name="theme_system_name" onchange="change_redirect($(this))">
			<option value=""></option>
			<?php if (isset($list_themes['items']) && is_array($list_themes['items'])): ?> 
			<?php foreach ($list_themes['items'] as $theme): ?> 
			<option value="<?php echo current_url().'?theme_system_name='.$theme->theme_system_name; ?>"<?php if ($current_selected_theme == $theme->theme_system_name) {echo ' selected="selected"';} ?>><?php echo $theme->theme_name; ?></option>
			<?php endforeach; ?> 
			<?php endif; ?> 
		</select>
		| <?php echo anchor('area/demo/'.$current_selected_theme, lang('block_view_area_demo')); ?>
	</div>
	<div class="clearfix"></div>
</div>

<div class="debug"></div>
<div class="form-result">
	<?php if (isset($form_status) && isset($form_status_message)) { ?> 
	<div class="alert alert-<?php echo $form_status; ?>"><button type="button" class="close" data-dismiss="alert">&times;</button><?php echo $form_status_message; ?></div>
	<?php } ?> 
</div>

<?php echo form_open('', array('class' => 'blocks-management')); ?> 
	
	<div class="row-fluid">
		<div class="span8 available-blocks">
			<h2><?php echo lang('block_available_blocks'); ?></h2>
			<div class="block-space">
				<?php if (isset($list_available_blocks) && is_array($list_available_blocks)): ?> 
				<ul class="available-blocks-grid">
					<?php foreach ($list_available_blocks as $key => $item): ?> 
					<li id="<?php echo $item['block_name']; ?>[::]<?php echo $item['block_file']; ?>" class="each-block">
						<h4><?php echo $item['block_title']; ?></h4>
						<p><?php echo $item['block_description']; ?></p>
					</li>
					<?php endforeach; ?> 
				</ul>
				<?php else: ?>&nbsp;<?php endif; ?> 
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="span4 areas">
			<div>
				<h2><?php echo lang('block_areas'); ?></h2>
				<?php if (isset($list_areas) && is_array($list_areas)): ?> 
				<?php foreach ($list_areas as $area): ?> 
				<div class="each-area block-space">
					<h3><?php echo $area['area_name']; ?></h3>
					<ol id="<?php echo $area['area_system_name']; ?>">
						<?php if (isset($list_block_in_area[$area['area_system_name']])): ?> 
						<?php foreach($list_block_in_area[$area['area_system_name']] as $block): ?> 
						<?php 
						$data['block'] = $block;
						$this->load->view('site-admin/templates/block/block_each', $data); 
						?> 
						<?php endforeach; ?> 
						<?php endif; ?> 
					</ol>
				</div>
				<?php endforeach; ?> 
				<?php else: ?> 
				<p><?php echo lang('block_please_select_theme'); ?></p>
				<?php endif; ?> 
			</div>
		</div>
	</div>
	
<?php echo form_close(); ?> 

<script type="text/javascript">
	$(document).ready(function() {
		$('.available-blocks li').draggable({
			//appendTo: "body",
			helper: "clone"
		});// available block draggable
		
		$('.available-blocks .block-space').droppable({
			accept: "ol li",
			activeClass: "ui-state-default",
			drop: function(event, ui) {
				remove_block(ui.draggable);
			}
		});// remove block from dragged area.
		
		$('.each-area ol')<?php if ($this->account_model->checkAdminPermission('block_perm', 'block_add_perm')): ?>.droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function(event, ui) {
				area_name = $(this).attr('id');
				$.ajax({
					url: site_url+'site-admin/block/ajax_add/<?php if (isset($current_selected_theme)) {echo $current_selected_theme;} ?>',
					type: 'POST',
					data: csrf_name+'='+csrf_value+'&area_name='+area_name+'&block_name='+ui.draggable.attr('id'),
					dataType: 'json',
					success: function(data) {
						// done, reload
						if (data.form_status != '') {
							$('.form-result').html('<div class="alert alert-'+data.form_status+'">'+data.form_status_message+'</div>');
						}
						if (data.result == true) {
							// reload blocks in area using ajax.
							ajax_load_area(area_name);
						}
					}
				});
			}
		})<?php endif; ?><?php if ($this->account_model->checkAdminPermission('block_perm', 'block_sort_perm')): ?>.sortable({
			handle: 'h4',
			items: "li:not(.placeholder, .action-item)",
			sort: function() {
				// gets added unintentionally by droppable interacting with sortable
				// using connectWithSortable fixes this, but doesn't allow you to customize active/hoverClass options
				$(this).removeClass("ui-state-default");
				$('.areas').find('.ui-state-default').removeClass('ui-state-default');
			},
			update: function() {
				var itemorder = $(this).sortable('serialize', {attribute: 'itemid'});
				$.ajax({
					url: site_url+'site-admin/block/ajax_sort',
					type: "GET",
					data: itemorder,
					success: function(data) {
						$('.form-result').html(data);
						setTimeout("$('.form-result').html('')", 2000);
					}
				});
			}
		})<?php endif; ?>;// each area droppable
		
		toggle_area();
	});// jquery start
	
	
	function ajax_change_status($item, status_to, area_name) {
		if (status_to != '1' && status_to != '0') {status_to = '0';}
		$.ajax({
			url: site_url+'site-admin/block/ajax_change_status/'+$item.attr('id'),
			type: 'POST',
			data: csrf_name+'='+csrf_value+'&block_status='+status_to,
			dataType: 'json',
			success: function(data) {
				if (data.result == true) {
					ajax_load_area(area_name);
				}
			}
		});
		return false;
	}// ajax_change_status
	
	
	function ajax_load_area(area_name) {
		$.ajax({
			url: site_url+'site-admin/block/ajax_load_area/<?php if (isset($current_selected_theme)) {echo $current_selected_theme;} ?>/'+area_name,
			type: 'GET',
			success: function(data) {
				$('#'+area_name).html(data);
			}
		});
	}// ajax_load_area
	
	
	function ajax_remove_block($item) {
		var confirms = confirm('<?php echo lang('block_are_you_sure_delete'); ?>');
		if (confirms == true) {
			return remove_block($item);
		}
		return false;
	}// ajax_remove_block
	
	
	function remove_block($item) {
		$.ajax({
			url: site_url+'site-admin/block/ajax_delete/'+$item.attr('id'),
			type: 'POST',
			data: csrf_name+'='+csrf_value,
			success:function(data) {
				$item.fadeOut();
			}
		});
		return false;
	}// remove_block
	
	
	function toggle_area() {
		$('.areas .each-area h3').click(function() {
			$(this).siblings('ol').fadeToggle('fast');
		});
	}// toggle_area
</script>