<?php 
	//Name: Gallery Full Left;
	//Description: An image gallery, with a wider left column;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-full-left">
	<div class="primary">
		<div class="item-full">
			<?php $item = page_item(1); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php fullsize($item->Files[0]); ?></a>
			<?php echo $item->title; ?>
			<?php echo $item->description; ?>
		</div>
		<div class="exhibit-text">
		<?php page_text(1); ?>
		</div>
	</div>
	
	<div class="secondary gallery">
		
		<div class="exhibit-item">
			<?php $item = page_item(2); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="exhibit-item">
			<?php $item = page_item(3); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="exhibit-item">
			<?php $item = page_item(4); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="exhibit-item">
			<?php $item = page_item(5); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
</div>