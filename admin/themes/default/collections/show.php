<?php head(array('title'=>'Collection # '.$collection->id, 'body_class'=>'collections')); ?>
<h1>Collection: <?php echo h($collection->name);?></h1>

<div id="primary">
<div id="collection-info">


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
		$items = items(array('collection'=>$collection->name, 'recent'=>true, 'per_page'=>10));
		set_items_for_loop($items);
	?>
	<ul>
	<?php while (loop_items()): ?>
		<li><span class="title"><?php echo link_to_item(); ?></span> <span class="date"><?php echo date('m.d.Y', strtotime(item('Date Added'))); ?></span></li>
	<?php endwhile;?>
	</ul>
	<h4>Total Number of Items in Collection: <?php echo total_items($collection);?></h4>
	
</div>
</div>
<?php foot(); ?>