<?php head(); ?>
<?php common('archive-nav'); ?>

<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
	<h2>Collections</h2>

		<?php foreach( $collections as $collection ): ?>
			<div class="collection">
				<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
				<p class="collector">Collector: <?php echo $collection->collector; ?></p>
				
				<?php echo nls2p($collection["description"]); ?>
				<p><a href="<?php echo uri('items/browse/'); ?>">View Items in <?php echo $collection->name; ?></a></p>
			</div>
		<?php endforeach; ?>

<?php foot(); ?>
