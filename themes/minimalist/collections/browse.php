<?php head(); ?>
	<h2>Collections</h2>

		<?php foreach ($collections as $collection ): ?>
			<div class="collection">
				<?php $collectors=$collection->Collectors; ?>
				<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
				<p><strong>Collector(s):</strong> <ul><?php foreach($collectors as $collector):?><li><?php echo $collector->name; ?></li><?php endforeach; ?></ul></p>
				<p><a href="<?php echo uri('items/browse/'); ?>">View the items in <?php echo $collection->name; ?></a></p>
			</div>
		<?php endforeach; ?>
			
<?php foot(); ?>	