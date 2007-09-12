<?php 
	//Name: Gallery Full Left;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-left">
	<div class="primary">
		<div class="item-full">
			<?php $item = page_item(1); ?>
			<?php link_to_fullsize($item, array('class'=>'permalink')); ?>
			<?php echo $item->title; ?>
			<?php echo $item->description; ?>
		</div>
	</div><!--end primary-->
	
	<div class="secondary gallery">
		
		<div class="exhibit-item">
			<?php $item = page_item(2); ?>
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<div class="exhibit-item">
			<?php $item = page_item(3); ?>
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<div class="exhibit-item">
			<?php $item = page_item(4); ?>
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<div class="exhibit-item">
			<?php if($item = page_item(5)): ?>
			<?php 	exhibit_thumbnail($item, array('class'=>'permalink')); ?>
			<?php endif; ?>
		</div>

	</div><!--end secondary gallery-->
	
		<div id="item-full-text"><?php echo page_text(1); ?></div>

</div><!--end gallery full left-->