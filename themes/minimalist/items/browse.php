<?php head(); ?>

	<div id="primary">
		<h1>Items</h1>
			<div id="pagination"><?php echo pagination_links(); ?></div>
				<?php display_item_list($items,false,false); ?>
	</div>
<?php foot(); ?>