<div id="detailed">
<?php while($item = loop_items()):?>
<div class="item">
	<h2><?php echo link_to_item(); ?></h2>

	<div class="meta">
		<ul>
			<li><span class="fieldname">Creator:</span> <?php echo item('Creator', ', '); ?></li>
			<li><span class="fieldname">Added:</span> <?php echo item('Date Added'); ?></li>
			<?php if (item_belongs_to_collection()): ?>
			<li><span class="fieldname">Collection:</span> <?php echo item('Collection Name'); ?></li>
			<?php endif; ?>
			<li><span class="fieldname">Public</span> <?php echo checkbox(array('name'=>"items[$item->id][public]",'class'=>"make-public"), item('Public')); ?></li>
			<li><span class="fieldname">Featured</span> 
			<?php echo checkbox(array('name'=>"items[" . item('id') . "][featured]",'class'=>"make-featured"), item('Featured')); ?>
			<?php echo hidden(array('name'=>"items[" . item('id') . "][id]"), item('id')); ?>	
			</li>
		</ul>
		<p><?php echo link_to_item('Edit', array('class'=>'edit'), 'edit'); ?></p>
	</div>

	<div class="description">
	<?php if (!item_has_thumbnail()): ?>
		<?php echo item('Description', array('index'=>0, 'snippet'=>300)); ?>
		<?php else: ?>
		<?php echo link_to_item(item_thumbnail(), array('class'=>'thumbnail')); ?>
		<?php echo item('Description', array('index'=>0, 'snippet'=>300)); ?>
	<?php endif; ?>
	</div>

</div>
<?php endwhile; ?>
</div>