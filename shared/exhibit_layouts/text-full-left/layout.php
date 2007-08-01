<?php 
	//Name: Document Text Left;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="document-text-left">
	<div class="primary">
	<div class="item-full">
		<?php $item = page_item(1); ?>
		<h3><a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php echo $item->title; ?></a></h3>
		<?php echo display_item($item); ?>
		
	</div>
	</div>
	
	<div class="secondary">
	<div class="commentary"><?php echo page_text(1); ?></div>
	</div>
</div>