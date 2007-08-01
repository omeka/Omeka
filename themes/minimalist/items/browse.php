<?php head(); ?>

	<h2>Inspiration</h2>
		
			<div id="pagination"><?php echo pagination_links(); ?></div>
		
		<div id="primary">
			<?php display_item_list($items,false,false); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo thumbnail($item); ?></a>
		</div>

<?php foot(); ?>