<?php head(); ?>
<?php common('archive-nav');?>
<ul id="tertiary-nav" class="navigation">
	<?php nav(array('Browse Collections' => uri('collections'), 'Add a Collection' => uri('collections/add/'))); ?>
</ul>
<div id="collection-info">
<h2>Collection: <?php echo $collection->name;?> <a class="edit" href="<?php echo uri('collections/edit/').$collection->id; ?>">(Edit)</a>  <a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">(Delete)</a></h2>
<p><?php echo $collection->description; ?></p>
</div>
<div id="collection-items">
	<h3>Recently Added to <?php echo $collection->name; ?></h3>
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

<?php foot(); ?>