<?php head(array('title'=>'Browse Types','body_class'=>'item-types')); ?>

<div id="primary">
<h1 class="floater">Item Types</h1>
<a id="add-item-type" class="add" href="<?php echo uri('item-types/add'); ?>">Add an Item Type</a>
<?php foreach( $itemtypes as $itemtype ): ?>
<div class="itemtype">
	 <h2><a href="<?php echo url_for_record($itemtype, 'show', 'item-types'); ?>"><?php echo htmlentities($itemtype->name); ?></a></h2>
	<p><?php echo htmlentities($itemtype->description); ?></p>
</div>
<?php endforeach; ?>
</div>
<?php foot(); ?>