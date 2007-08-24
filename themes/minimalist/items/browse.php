<?php head(); ?>

	<div id="primary">
		<h2>Items</h2>
			<div id="pagination"><?php echo pagination_links(); ?></div>
				<?php display_item_list($items,false,false); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo thumbnail($item); ?></a>

<?php foot(); ?>