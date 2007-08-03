<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	<h1>Collections</h1>
	<a href="<?php echo uri('collections/add'); ?>" id="add-collection" class="add-collection">Add a Collection</a>
		<?php foreach($collections as $collection): ?>
			<div class="collection">
				<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h2>
				<div class="meta">

				<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo $collection->name; ?></a></p>
				
				<?php if($time = $collection->added):?>
						<p>Time Added: <?php echo $time; ?></p>
				<?php endif; ?>
				
				<p class="collectors">Collectors:
				<?php foreach( $collection->Collectors as $k => $collector ): ?>
					<?php echo $collector->name; ?>
				<?php endforeach; ?>
				</p>
				</div>
				<div class="description">
					<?php echo nls2p($collection["description"]); ?>
				</div>
			</div>
		<?php endforeach; ?>
</div>		
<?php foot(); ?>
