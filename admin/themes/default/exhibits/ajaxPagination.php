	<?php 
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array('page'=>$_REQUEST['page'], 'pagination_url'=>null),array('items','pagination'));
	extract($retVal);
	
	?>
	<div id="pagination">
	<?php 
		 echo pagination(); 
	?>
		</div>
		
	<?php foreach( $items as $item ): ?>
		<div class="item">
			<div class="item_id">
				<?php echo $item->id; ?>
			</div>
			
			<?php 
				if(has_thumbnail($item)){
					thumbnail($item);
				} else {
					echo $item->title;
				}
			?>
		</div>
	<?php endforeach; ?>
	
		