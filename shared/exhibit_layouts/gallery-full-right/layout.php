<?php 
	//Name: Gallery Full Right;
	//Description: An image gallery, with a wider right column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-right">
	<div class="primary">
	<div class="item-full">
		<?php $item = page_item(1); ?>
		<?php exhibit_fullsize($item); ?>
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
			<?php $item = page_item(5); ?>
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
	</div><!--end secondary-->

	<div id="item-full-text"><?php echo page_text(1); ?></div>

</div><!--end gallery-->