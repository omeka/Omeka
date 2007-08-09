<div id="pagination">
<?php 
	 echo pagination_links(5, null, null, null, null, uri('exhibits/items/'), 'page'); 
?>

</div>

<?php foreach( $items as $k => $item ): ?>
	<div class="item-drop">
		<div class="item-drag">
			<div class="item_id"><?php echo h($item->id); ?></div>
			<?php 
				if(has_thumbnail($item)){
					thumbnail($item);
				} else {
					echo h($item->title);
				}
			?>
		</div>
		<div class="item_id"><?php echo h($item->id); ?></div>
	</div>
<?php endforeach; ?>
<h2 id="search-header" class="close">Search Items</h2>

<?php items_filter_form(array('id'=>'search'), $url); ?>		