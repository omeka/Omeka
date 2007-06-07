<?php 
	//Retrieve items with their pagination
	$retVal = _make_omeka_request('Items','browse',array(),array('items','pagination'));
	extract($retVal);
	
	?>
	
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
<?php 
	 echo $pagination; 
?>
	
	
