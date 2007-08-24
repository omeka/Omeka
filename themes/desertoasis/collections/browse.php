<?php head(); ?>
	<div id="primary">
		<div id="collections" class="cbb">

		<h2>Collections</h2>
		
		<?php foreach ($collections as $collection ): ?>
		<div id="collection">
			<?php $collectors=$collection->Collectors; ?>
			
			<h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3>
			<h4>Collector(s):</h4>
				<ul id="collectors">
				<?php foreach($collectors as $collector):?>
					<li><?php echo $collector->name; ?></li>
				<?php endforeach; ?>
				</ul>
			<p><a href="<?php echo uri('items/browse/?collection='.$collection->id); ?>">View the items in <?php echo $collection->name; ?></a></p>
		</div><!--end collection-->
		<?php endforeach; ?>
		
		</div><!-- end collections -->
	</div><!--end primary-->
	<div id="secondary">
		<?php common('sidebar'); ?>	
		
