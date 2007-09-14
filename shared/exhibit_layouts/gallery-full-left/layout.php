<?php 
	//Name: Gallery Full Left;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-left">
	<div class="primary">
		<?php if($item = page_item(1)):?>
		<div class="item-full">
			<?php exhibit_fullsize($item, array('class'=>'permalink')); ?>
			<?php echo $item->title; ?>
			<?php echo $item->description; ?>
			<a href="<?php echo exhibit_item_uri($item); ?>">Item Link</a>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="secondary gallery">
		<?php if($item = page_item(2)):?>
		<div class="exhibit-item">
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(3)):?>
		<div class="exhibit-item">
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(4)):?>
		<div class="exhibit-item">
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<?php endif; ?>
		<?php if($item = page_item(5)):?>
		<div class="exhibit-item">
			<?php exhibit_thumbnail($item, array('class'=>'permalink')); ?>
		</div>
		<?php endif; ?>
	</div>
	
	<?php if($text = page_text(1)):?>
	<div id="item-full-text">
		<?php echo $text; ?>
	</div>
	<?php endif; ?>
</div>