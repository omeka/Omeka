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
	</div><!--end primary-->
	
	<div class="secondary gallery">
		
		<div class="exhibit-item">
			<?php $item = page_item(2); ?>
			<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
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
			<?php if($item = page_item(5)): ?>
				<?php var_dump(get_class($item)); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
			<?php endif; ?>
		</div>

	</div><!--end secondary gallery-->
	
		<div id="item-full-text"><?php echo page_text(1); ?></div>

</div><!--end gallery full left-->