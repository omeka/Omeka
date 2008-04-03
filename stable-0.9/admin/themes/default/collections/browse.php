<?php head(array('title'=>'Browse Collections', 'body_class'=>'collections')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	<h1 class="floater">Collections</h1>
	<a href="<?php echo uri('collections/add'); ?>" id="add-collection" class="add-collection">Add a Collection</a>
		
		<?php foreach($collections as $collection): ?>
		
			<div class="collection">
			<h2><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo h($collection->name); ?></a></h2>

			<div class="description">
			<h3>Description</h3>
			<p><?php echo nls2p($collection->description); ?><p>
			</div>

			<div class="meta">
			<?php if($time = $collection->added):?>
				<h3>Date Created</h3> 
				<p><?php echo date('m.d.Y', strtotime($time)); ?></p>
				<?php endif; ?>
			</div>
			
			<div class="meta">	
			<h3>Collectors</h3>
			<ul>
			<?php if(has_collectors($collection)): ?> 
			<?php foreach( $collection->Collectors as $k => $collector ): ?>
			<li><?php echo h($collector->name); ?></li>
			<?php endforeach; ?>
			<?php else: ?>
			<li>No collectors</li>
			<?php endif; ?>
			</ul>
			</div>

			<div class="meta">	
			<p class="viewitems"><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View Items in <?php echo $collection->name; ?></a></p>
			</div>
					
			</div><!--end collection-->

		<?php endforeach; ?>
</div>		
<?php foot(); ?>
