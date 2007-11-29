<?php head(array('title'=>'Collection # '.$collection->id, 'body_class'=>'collections')); ?>
<?php common('archive-nav');?>
<div id="primary">
<div id="collection-info">

<h1>Collection: <?php echo h($collection->name);?></h1>

<p> <a class="edit" href="<?php echo uri('collections/edit/').$collection->id; ?>">Edit</a></p>

<h2>Description:</h2> <p><?php echo h($collection->description); ?></p>

	<h2>Collectors:</h2>
	<ul id="collector-list">
		<?php foreach( $collection->Collectors as $k => $collector ): ?>
		<li><?php echo h($collector->name); ?></li>
		<?php endforeach; ?>
	</ul>

</div>
<div id="collection-items">
	<h2>Recently Added to <?php echo h($collection->name); ?></h2>
	<?php
		$items = items(array('collection'=>$collection->name, 'recent'=>true));
	?>
	<ul>
	<?php foreach ($items as $key => $item): ?>
		<?php if ($key < 10): ?>
		<li><span class="title"><?php link_to_item($item); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>
		<?php endif; ?> 
	<?php endforeach;?>
	</ul>
	<h4>Total Number of Items in Collection: <?php echo total_items($collection);?></h4>
	
</div>
</div>
<?php foot(); ?>