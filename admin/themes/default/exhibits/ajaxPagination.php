	<?php 
	items_filter_form(array(), null);
	
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array('page'=>$_REQUEST['page'], 'pagination_url'=>null, 'public'=>true),array('items','pagination'));
	extract($retVal);
	
	?>
	<div id="pagination">
	<?php 
		 echo pagination_links(); 
	?>
		</div>
		
	<?php foreach( $items as $item ): ?>
		<div class="item">
			<div class="item_id">
				<?php echo h($item->id); ?>
			</div>
			
			<?php 
				if(has_thumbnail($item)){
					thumbnail($item);
				} else {
					echo h($item->title);
				}
			?>
		</div>
	<?php endforeach; ?>
	
		