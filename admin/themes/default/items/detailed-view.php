<div id="detailed">
<?php while($item = loop_items()):?>
<div class="item">
	<h2><?php echo link_to_item(); ?></h2>
	<p class="edit-item"><?php echo link_to_item('Edit', array('class'=>'edit'), 'edit'); ?>
	
	<ul class="public-featured-checkboxes">
	<li><span class="fieldname">Public</span> <?php echo checkbox(array('name'=>"items[" . item('id') . "][public]",'class'=>"make-public"), item('Public')); ?></li>
	<li><span class="fieldname">Featured</span> 
	<?php echo checkbox(array('name'=>"items[" . item('id') . "][featured]",'class'=>"make-featured"), item('Featured')); ?>
	<?php echo hidden(array('name'=>"items[" . item('id') . "][id]"), item('id')); ?>	
	</li>
	</ul>
	<div class="item-description">
	<?php if (!item_has_thumbnail()): ?>
		<?php echo item('Dublin Core', 'Description', array('snippet'=>300)); ?>
		<?php else: ?>
		<?php echo link_to_item(item_thumbnail(), array('class'=>'thumbnail')); ?>
		<?php echo item('Dublin Core', 'Description', array('snippet'=>300)); ?>
	<?php endif; ?>
	</div>
	<div class="item-meta">
		<ul>
			<li><span class="fieldname">Creator:</span> <?php echo item('Dublin Core', 'Creator', array('delimiter'=>', ', 'all'=>true)); ?></li>
			<li><span class="fieldname">Added:</span> <?php echo item('Date Added'); ?></li>
			<li><span class="fieldname">Collection:</span> <?php if (item_belongs_to_collection()) echo item('Collection Name'); else echo 'No Collection'; ?></li>
		</ul>
	</div>
	<div class="append-to-item-detail">
    <?php fire_plugin_hook('admin_append_to_items_browse_detailed_each'); ?>
	</div>
</div>
<?php endwhile; ?>
</div>