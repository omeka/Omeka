<?php head(); ?>
<ul id="secondary-nav">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
	<h2>Collections</h2>

		<?php foreach( $collections as $collection ): ?>
			<div class="collection">
				<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
				<p><?php echo $collection->collector; ?></p>
				<p><a href="<?php echo uri('items/browse/'); ?>">View Items in <?php echo $collection->name; ?></a></p>
			</div>
		<?php endforeach; ?>

<?php foot(); ?>
