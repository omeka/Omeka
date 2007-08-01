<?php head(); ?>	

	<div id="primary">
		
		<div id="featured-item">
		<h2>Featured Design</h2>
			<?php $randomitem = random_featured_item();  ?>
			<span class="feat-img"><a href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php fullsize ($randomitem, null, 467); ?></a></span>
			<div class="feat-metadata">
			<span class="item-title"><h3><a href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php echo $randomitem->title; ?></a></h3></span>
			<span class="desc"><p><?php echo ($randomitem->description); ?></p></span>
			<span class="source"><p>Source: <a href="<?php echo ($randomitem->source); ?>"><?php echo ($randomitem->source); ?></a></p></span>
			</div>
		</div>
			
		<div id="recent-items">
		<h2>New Designs</h2>
			<?php $recent = recent_items(8); ?>
			<ul>
				<?php foreach( $recent as $item ): ?>
				<li><span class="thumb"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo thumbnail($item); ?></a></span></li>
				<?php endforeach; ?>
			</ul>
		</div><!--end recent-items -->
		
	</div><!-- end primary -->
	
	<div id="secondary">
		<?php common('tagcloud'); ?>
	</div><!-- end secondary -->
	
<?php foot(); ?>