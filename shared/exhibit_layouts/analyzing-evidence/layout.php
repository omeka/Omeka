<?php 
	//Name: Analyzing Evidence;
	//Description: A page to show video and accompanying text/transcript;
	//Author: Laura Veprek; 
?>

<div class="">
	
		<div class="exhibit-item">
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php fullsize($item->Files[0]); ?></a>
		<div class="item-text"><?php echo page_text(1); ?></div>
		</div>
		
</div>