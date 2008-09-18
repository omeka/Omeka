<?php head(array('title'=>'Browse Types','body_class'=>'item-types')); ?>
<h1>Item Types</h1>
<p id="add-item-type" class="add-button"><a class="add" href="<?php echo uri('item-types/add'); ?>">Add an Item Type</a></p>

<div id="primary">
<?php foreach( $itemtypes as $itemtype ): ?>
<div class="itemtype">
	 <h2><a href="<?php echo record_uri($itemtype, 'show', 'item-types'); ?>"><?php echo htmlentities($itemtype->name); ?></a></h2>
	<p><?php echo htmlentities($itemtype->description); ?></p>
</div>
<?php endforeach; ?>
</div>
<?php foot(); ?>