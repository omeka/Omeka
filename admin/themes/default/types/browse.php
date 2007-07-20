<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php 
		if(has_permission('Types','add')) {
			nav(array('Browse Types' => uri('types/browse'), 'Add Type' => uri('types/add')));
		}
	?>
</ul>
<h1>Item Types</h1>
<?php foreach( $types as $type ): ?>
<div class="itemtype">
	 <h2><a href="<?php echo uri('types/show/'.$type->id); ?>"><?php echo $type->name; ?></a></h2>
	<p><?php echo $type->description; ?></p>
</div>
<?php endforeach; ?>
</div>
<?php foot(); ?>