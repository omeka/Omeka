<?php head(array('title'=>'Browse Types','body_class'=>'types')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1 class="floater">Item Types</h1>
<a id="add-type" class="add" href="<?php echo uri('types/add'); ?>">Add an Item Type</a>
<?php foreach( $types as $type ): ?>
<div class="itemtype">
	 <h2><a href="<?php echo uri('types/show/'.$type->id); ?>"><?php echo h($type->name); ?></a></h2>
	<p><?php echo h($type->description); ?></p>
</div>
<?php endforeach; ?>
</div>
<?php foot(); ?>