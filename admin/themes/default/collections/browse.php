<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
	<h1>Collections &rarr; Browse</h1>

		<?php foreach($collections as $collection): ?>
			<div class="collection">
				<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h2>
				
				<?php echo nls2p($collection["description"]); ?>
				<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo $collection->name; ?></a></p>
				
				<?php if($time = $collection->added):?>
						<p>Time Added: <?php echo $time; ?></p>
				<?php endif; ?>
				
				<p class="collector">Collectors:
				<?php foreach( $collection->Collectors as $k => $collector ): ?>
					<?php echo $collector->name; ?>
				<?php endforeach; ?>
				</p>
			</div>
		<?php endforeach; ?>
</div>		
<?php foot(); ?>
