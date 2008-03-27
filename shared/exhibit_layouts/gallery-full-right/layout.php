<?php 
	//Name: Gallery Full Right;
	//Description: An image gallery, with a wider right column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-right">
	<div class="primary">
	<div class="item-full">
		<?php $item = page_item(1); ?>
		<?php echo exhibit_fullsize($item); ?>
		<?php echo h($item->title); ?>
		<?php echo $item->description; ?>
	</div>
	</div><!--end primary-->
	
	<div class="secondary gallery">
		
		<?php echo display_exhibit_thumbnail_gallery(2, 5, array('class'=>'permalink')); ?>
	</div><!--end secondary-->

	<div id="item-full-text"><?php echo page_text(1); ?></div>

</div><!--end gallery-->