<?php head(); ?>
	<div id="primary">
		<div id="collections" class="cbb">

		<h2>Collections</h2>
		
		<?php foreach ($collections as $collection ): ?>
		<div id="collection">
			<?php $collectors=$collection->Collectors; ?>
			<dl>
			<dt><h3><a href="<?php echo uri('collections/show/'.$collection->id); ?>"><?php echo $collection->name; ?></a></h3></dt>
			<dd>Collector(s): <ul id="collectors">
				<?php foreach($collectors as $collector):?>
					<li><?php echo $collector->name; ?></li>
				<?php endforeach; ?></dd>
				</ul>
			<dd><a href="<?php echo uri('items/browse/'); ?>">View the items in <?php echo $collection->name; ?></a></dd>
			</dl>
		</div><!--end collection-->
		<?php endforeach; ?>
		
		</div><!-- end collections -->
	</div><!--end primary-->
	<div id="secondary">
		<?php common('sidebar'); ?>	
		
