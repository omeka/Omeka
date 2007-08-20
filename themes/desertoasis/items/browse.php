<?php head(); ?>	
	<div id="primary">
		<div id="items" class="cbb">
			
		<h2>Items</h2>
			<div id="pagination"><?php echo pagination_links(); ?></div>
			<div id="item-list"><?php display_item_list($items,false,false); ?><a href="<?php echo uri('items/show/'.$item->id); ?>"><?php echo thumbnail($item); ?></a></div>
							
		</div>
	</div>

	<div id="secondary">
		<?php common('sidebar'); ?>	
		
		
