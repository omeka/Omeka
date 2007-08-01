<?php head(); ?>	
	
	<div id="primary">
		
			<div id="featured-item" class="cbb">
				<h2>Featured</h2>

				<?php $randomitem = random_featured_item();  ?>

				<div class="feat-metadata">
				<h3><a href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php echo $randomitem->title; ?></a></h3>
				<span class="desc"><?php echo ($randomitem->description); ?></span><span class="source">Source: <a href="<?php echo ($randomitem->source); ?>"><?php echo ($randomitem->source); ?></a></span>
				</div><!--end feat-metadata-->

				<a class="feat-img"href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php fullsize ($randomitem, null, 600); ?></a>
									
			</div><!--end featured-item-->
			
			<div id="recent-items" class="cbb">
				<h2>Recent</h2>
					<?php $recent = recent_items(8); ?>
					<ul>
						<?php foreach( $recent as $item ): ?>
						<li><span class="thumb"><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo thumbnail($item); ?><?php echo ($item->title); ?></a></span></li>
						<?php endforeach; ?>
					</ul>
					<p class="clear"><a href="<?php echo uri('items/browse/'); ?>">View All &#187;</a></p>
			</div><!--end recent-items -->
	
	</div><!--end primary-->
	
	<div id="secondary">
		<?php common('sidebar'); ?>