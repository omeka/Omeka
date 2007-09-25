<?php head(); ?>	

	<div id="primary">
		
		<div id="featured-item">
		<h2>Featured Item</h2>
		
			<?php $randomitem = random_featured_item();  ?>
			<div class="feat-metadata">
			<a class="feat-img" href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php fullsize ($randomitem, null, 467); ?></a>
			<h3 class="item-title"><a href="<?php echo uri('items/show/'.$randomitem->id); ?>"><?php echo $randomitem->title; ?></a></h3>
			<p class="desc"><?php echo ($randomitem->description); ?></p>
			<?php if ($randomitem->source) {
			echo "<p class=\"source\">Source: <a href=\"".$randomitem->source."\">".$randomitem->source."</a></p>"; } ?>
			</div>
			
		</div>
			
		<div id="recent-items">
		<h2>Recently Added</h2>
			<?php $recent = recent_items(8); ?>
			<ul>
				<?php foreach( $recent as $item ): ?>
				<li><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo $item->title; ?></a><?php if ($item->description) 
				 echo "<span class=\"item-description\">".$item->description."</span>"; ?></li>
				<?php endforeach; ?>
			</ul>
		</div><!--end recent-items -->
		
	</div><!-- end primary -->
	
	<div id="secondary">
			<h2>Tags</h2>
			<?php tag_cloud(recent_tags(), uri('items')); ?>
	</div><!-- end secondary -->
	
<?php foot(); ?>