<?php head(); ?>
<?php common('archive-nav');?>
<div id="primary">
<div id="collection-info">
<h1>Collections &rarr; <?php echo h($collection->name);?> <a class="edit" href="<?php echo uri('collections/edit/').$collection->id; ?>">(Edit)</a>  <a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">(Delete)</a></h1>
<p><?php echo h($collection->description); ?></p>

<ul id="collector-list">
<?php foreach( $collection->Collectors as $k => $collector ): ?><li><?php echo h($collector->name); ?></li>
<?php endforeach; ?>
</ul>

</div>
<div id="collection-items">
	<h2>Recently Added to <?php echo h($collection->name); ?></h2>
	<?php
		$items = items(array('collection'=>$collection->name, 'recent'=>true));
	?>
	<?php echo total_items($items);?>
	<ul>
	<?php foreach ($items as $key => $item): ?>
		<?php if ($key < 10): ?>
		<li><span class="title"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo h($item->title); ?></a></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>
		<?php endif; ?> 
	<?php endforeach;?>
	</ul>
	
</div>
</div>
<?php foot(); ?>