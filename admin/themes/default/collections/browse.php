<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	<h1>Collections</h1>
	<a href="<?php echo uri('collections/add'); ?>" id="add-collection" class="add-collection">Add a Collection</a>
		<?php foreach($collections as $collection): ?>
			<div class="collection">
				<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo h($collection->name); ?></a></h2>
			
				<div class="meta">
				<?php if($time = $collection->added):?>
				<h3>Time Added</h3> 
				<p><?php echo $time; ?></p>
				<?php endif; ?>
				
				<h3>Collectors</h3>
				<ul>
				<?php if(has_collectors($collection)): ?> 
				<?php foreach( $collection->Collectors as $k => $collector ): ?>
				<li><?php echo h($collector->name); ?></li>
				<?php endforeach; ?>
				<?php else: ?>
				<li>No collectors</li>
				<?php endif; ?>
				</ul>

				
				<p class="viewitems"><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo $collection->name; ?></a></p>
				</div>
				<div class="description">
					<?php echo nls2p($collection["description"]); ?>
					</div>
				</div><!--end collection-->

		<?php endforeach; ?>
</div>		
<?php foot(); ?>
