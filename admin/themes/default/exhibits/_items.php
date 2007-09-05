<div id="page-search-form">
<?php items_search_form(array('id'=>'search'), $url); ?>
</div>

<div id="pagination">
<?php 
	 echo pagination_links(5, null, null, null, null, uri('exhibits/items/'), 'page'); 
?>

</div>

<div id="item-list">
<?php foreach( $items as $k => $item ): ?>
	<div class="item-drop">
		<div class="item-drag">
			<div class="handle"><img src="<?php img('arrow_move.gif'); ?>"></div>
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
</div>
		