<?php 
	//Name: Document Text Right;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="document-text-right">
	<div class="primary">
	<div class="item-full">
		<?php $item = page_item(1); ?>
		<h3><a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php echo $item->title; ?></a></h3>
		<div class="text"><?php echo nls2p($item->Metatext('Text'));?></div>
		
	</div>
	</div>
	
	<div class="secondary">
	<div class="commentary"><?php echo page_text(1); ?></div>
	</div>
</div>