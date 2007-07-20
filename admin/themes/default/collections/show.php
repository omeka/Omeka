<?php head(); ?>
<?php common('archive-nav');?>
<div id="primary">
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
<div id="collection-info">
<h1>Collections &rarr; <?php echo $collection->name;?> <a class="edit" href="<?php echo uri('collections/edit/').$collection->id; ?>">(Edit)</a>  <a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">(Delete)</a></h1>
<p><?php echo $collection->description; ?></p>

<ul id="collector-list">
<?php foreach( $collection->Collectors as $k => $collector ): ?><li><?php echo $collector->name; ?></li>
<?php endforeach; ?>
</ul>

</div>
<div id="collection-items">
	<h2>Recently Added to <?php echo $collection->name; ?></h2>
	<?php
		$items = items(array('collection'=>$collection->name, 'recent'=>true));
	?>
	
	<ul>
	<?php foreach ($items as $key => $item): ?>
		<?php if ($key < 10): ?>
		<li><span class="title"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo $item->title; ?></a></span> <span class="date"><?php echo date('m.d.Y', strtotime($item->added)); ?></span></li>
		<?php endif; ?> 
	<?php endforeach;?>
	</ul>
	
</div>
</div>
<?php foot(); ?>