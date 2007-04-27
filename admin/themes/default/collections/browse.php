<?php head(); ?>
<?php common('archive-nav'); ?>

<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
	<h2>Collections</h2>

	<?php function showfield($field = null, $class = null, $tag = 'p') {
		if($field):
		echo '<'.$tag.(!empty($class)?' class="'.$class.'"':'').'>'.$field.'</'.$tag.'>';
		endif; 
	} ?>
		<?php foreach( $collections as $collection ): ?>
			<div class="collection">
				<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
				<?php showfield($collection->collector); ?>
				
				<?php showfield($collection["description"]); ?>
				<p><a href="<?php echo uri('items/browse/'); ?>">View Items in <?php echo $collection->name; ?></a></p>
			</div>
		<?php endforeach; ?>

<?php foot(); ?>
