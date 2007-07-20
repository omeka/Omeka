<div id="detailed">
<?php foreach($items as $key => $item):?>
<div class="item">
	<h2><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo $item->title; ?></a></h2>

	<div class="meta">
		<ul>
			<li><span class="fieldname">Creator:</span> <?php echo $item->creator; ?></li>
			<li><span class="fieldname">Added:</span> <?php echo $item->added; ?></li>
			<?php if ( $item->Collection->exists() ): ?>
			<li><span class="fieldname">Collection:</span> <?php echo $item->Collection->name; ?></li>
			<li><span class="fieldname">Public</span> <?php checkbox('public'); ?></li>
			<li><span class="fieldname">Featured</span> <?php checkbox('featured'); ?></li>
			<?php endif; ?>	
		</ul>
	</div>
	<div class="description">

	<?php if (has_thumbnail($item) == null): ?>
		<?php echo nls2p(snippet($item->description, 0, 300)); ?>
		<?php else: ?>
		<a class="thumbnail" href="<?php echo uri('items/show/'.$item->id); ?>"><?php thumbnail($item);?></a> 
		<?php echo nls2p(snippet($item->description, 0, 100)); ?>
	<?php endif; ?>
	</div>

</div>
<?php endforeach; ?>
</div>