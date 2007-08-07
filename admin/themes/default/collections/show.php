<?php head(); ?>
<?php common('archive-nav');?>
<div id="primary">
<div id="collection-info">

<h2>Collection: <?php echo h($collection->name);?></h2>

<p> <a class="edit" href="<?php echo uri('collections/edit/').$collection->id; ?>">Edit</a>  <a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">Delete</a></p>

<h3>Description:</h3> <p><?php echo h($collection->description); ?></p>

<?php foreach( $collection->Collectors as $k => $collector ): ?>
	<h3>Collectors:</h3>
	<ul id="collector-list">
		<li><?php echo h($collector->name); ?></li>
	</ul>
<?php endforeach; ?>

</div>
<div id="collection-items">
	<h3>Recently Added to <?php echo h($collection->name); ?></h3>
	<?php
		$items = items(array('collection'=>$collection->name, 'recent'=>true));
	?>
	<ul>
	<?php foreach ($items as $key => $item): ?>
		<?php if ($key < 10): ?>
		<li><span class="title"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo h($item->title); ?></a></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>
		<?php endif; ?> 
	<?php endforeach;?>
	</ul>
	<h4>Total Number of Items in Collection: <?php echo total_items($items);?></h4>
	
</div>
</div>
<?php foot(); ?>