<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	<h1>Collections</h1>

		<?php foreach($collections as $collection): ?>
			<div class="collection">
				<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo h($collection->name); ?></a></h2>
			
				<h3>Description:</h3> <p><?php echo nls2p($collection["description"]); ?></p>

				<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo h($collection->name); ?></a></p>
				
				<?php if($time = $collection->added):?>
				<h3>Time Added:</h3> <p><?php echo $time; ?></p>
				<?php endif; ?>
				
				<h3>Collectors:</h3>
				<?php foreach( $collection->Collectors as $k => $collector ): ?>
				<p><?php echo h($collector->name); ?></p>
				<?php endforeach; ?>
				</p>

				<p class="viewitems"><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo $collection->name; ?></a></p>
				</div><!--end collection-->

		<?php endforeach; ?>
</div>		
<?php foot(); ?>
