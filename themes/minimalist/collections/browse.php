<?php head(); ?>
	<h2>Collections</h2>

		<?php foreach ($collections as $collection ): ?>
			<div class="collection">
				<?php $collectors=$collection->Collectors; ?>
				<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
				<p><?php foreach($collectors as $collector):?>Collector: <?php echo $collector->name; ?><?php endforeach; ?></p>
				<p><a href="<?php echo uri('items/browse/'); ?>">View the items in <?php echo $collection->name; ?></a></p>
			</div>
		<?php endforeach; ?>
			

	<div id="secondary">
		<?php common('sidebar'); ?>	