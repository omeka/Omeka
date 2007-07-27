<?php head(); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Item Types</h1>
<div id="add-type" class="add"><a href="<?php echo uri('types/add'); ?>">Add an Item Type</a></div>
<?php foreach( $types as $type ): ?>
<div class="itemtype">
	 <h2><a href="<?php echo uri('types/show/'.$type->id); ?>"><?php echo $type->name; ?></a></h2>
	<p><?php echo $type->description; ?></p>
</div>
<?php endforeach; ?>
</div>
<?php foot(); ?>