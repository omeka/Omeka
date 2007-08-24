<?php head(); ?>
<div id="primary">
	<div id="collections" class="cbb">
		<h2>Collection: <?php echo $collection->name;?></h2>
		
		<h3>Description</h3>
		<p><?php echo $collection->description; ?></p>

		<?php $collectors=$collection->Collectors; ?>
		<h3>Collector(s)</h3>
			<ul id="collectors">
				<?php foreach($collectors as $collector):?>
					<li><?php echo $collector->name; ?></li>
				<?php endforeach; ?>
			</ul>

		<div class="meta">
			<?php if($time = $collection->added):?>
			<span>Created on <?php echo date('m.d.Y', strtotime($time)); ?></span>
			<?php endif; ?>
		</div>

			<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View the items in <?php echo $collection->name; ?></a></p>

	</div><!-- end collections -->
</div><!--end primary-->
<div id="secondary">
<?php common('sidebar'); ?>	
		
