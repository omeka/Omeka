<?php 
	//Name: Gallery Full Top Right;
	//Description: Gallery View, with main image and text at the top, and thumbnails at the bottom;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-top-right">

	<div class="primary">
		<?php if($item = page_item(1)):?>
		<div class="item">
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php fullsize($item->Files[0]); ?></a>

		</div>
		<?php endif; ?>
		<div class="text">
			<?php echo page_text(1); ?>
		</div>
	</div>
	<div class="secondary">
		<?php if($item = page_item(2)):?>
		<div class="item">
<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>				</div>
		<?php endif; ?>
		<?php if($item = page_item(3)):?>
		<div class="item">
<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>				</div>
		<?php endif; ?>
		<?php if($item = page_item(4)):?>
		<div class="item">
<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>				</div>
		<?php endif; ?>
		<?php if($item = page_item(5)):?>
		<div class="item">
<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>				</div>
		<?php endif; ?>
	</div>
</div>