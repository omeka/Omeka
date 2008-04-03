<?php 
	//Name: Gallery Full Left;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-left">
	<div class="primary">
		<?php if($item = page_item(1)):?>
		<div class="item-full">
			<?php echo exhibit_fullsize($item, array('class'=>'permalink')); ?>
			<?php echo h($item->title); ?>
			<?php echo h($item->description); ?>
			<a href="<?php echo exhibit_item_uri($item); ?>">Item Link</a>
		</div>
		<?php endif; ?>
	</div>
	
	<div class="secondary gallery">
        <?php echo display_exhibit_thumbnail_gallery(2, 5, array('class'=>'permalink')); ?>
	</div>
	
	<?php if($text = page_text(1)):?>
	<div id="item-full-text">
		<?php echo $text; ?>
	</div>
	<?php endif; ?>
</div>