<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	<h1>Collections</h1>

		<?php foreach($collections as $collection): ?>
			<div class="collection">
				<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo h($collection->name); ?></a></h2>
				
				<?php echo nls2p($collection["description"]); ?>
				<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo h($collection->name); ?></a></p>
				
				<?php if($time = $collection->added):?>
						<p>Time Added: <?php echo $time; ?></p>
				<?php endif; ?>
				
				<p class="collector">Collectors:
				<?php foreach( $collection->Collectors as $k => $collector ): ?>
					<?php echo h($collector->name); ?>
				<?php endforeach; ?>
				</p>
			</div>
		<?php endforeach; ?>
</div>		
<?php foot(); ?>
