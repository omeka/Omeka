<div id="detailed">
<?php foreach($items as $key => $item):?>
<div class="item">
	<h2><?php link_to_item($item); ?></h2>

	<div class="meta">
		<ul>
			<li><span class="fieldname">Creator:</span> <?php echo h($item->creator); ?></li>
			<li><span class="fieldname">Added:</span> <?php echo h($item->added); ?></li>
				<?php if(has_collection($item)): ?>
			<li><span class="fieldname">Collection:</span> <?php echo h($item->Collection->name); ?></li>
			<?php endif; ?>
			<li><span class="fieldname">Public</span> <?php checkbox(array('name'=>"items[$item->id][public]",'class'=>"make-public"), $item->public); ?></li>
			<li><span class="fieldname">Featured</span> 
			<?php checkbox(array('name'=>"items[$item->id][featured]",'class'=>"make-featured"), $item->featured); ?>
			<?php hidden(array('name'=>"items[$item->id][id]"), $item->id); ?>	
			</li>
		</ul>
		<p><?php link_to_item($item, 'edit', 'Edit', array('class'=>'edit')); ?></p>
	</div>

	<div class="description">
	<?php if (has_thumbnail($item) == null): ?>
		<?php echo nls2p(snippet($item->description, 0, 300)); ?>
		<?php else: ?>
		<?php link_to_thumbnail($item, array('class'=>'thumbnail')); ?>
		<?php echo nls2p(snippet($item->description, 0, 100)); ?>
	<?php endif; ?>
	</div>

</div>
<?php endforeach; ?>
</div>