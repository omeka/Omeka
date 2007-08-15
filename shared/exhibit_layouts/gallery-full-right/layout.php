<?php 
	//Name: Gallery Full Right;
	//Description: An image gallery, with a wider right column;
	//Author: Jeremy Boggs; 
?>

<div class="<?php echo $layout; ?>">
	<div class="primary">
	<div class="item-full">
		<?php $item = page_item(1); ?>
		<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		<?php echo $item->title; ?>
		<?php echo $item->description; ?>
	</div>
	
	<?php page_text(1); ?>
	</div>
	
	<div class="secondary gallery">
		
		<div class="item">
			<?php $item = page_item(2); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="item">
			<?php $item = page_item(3); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="item">
			<?php $item = page_item(4); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<div class="item">
			<?php $item = page_item(5); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php if($item = page_item(6)):?>
		<div class="item">
			<?php $item = page_item(6); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>	
		<?php if($item = page_item(7)):?>
		<div class="item">
			<?php $item = page_item(7); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>
		<?php if($item = page_item(8)):?>
		<div class="item">
			<?php $item = page_item(8); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>	
		<?php if($item = page_item(9)):?>
		<div class="item">
			<?php $item = page_item(9); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>
		<?php if($item = page_item(10)):?>
		<div class="item">
			<?php $item = page_item(10); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>	
		<?php if($item = page_item(11)):?>
		<div class="item">
			<?php $item = page_item(11); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>
		<?php if($item = page_item(12)):?>
		<div class="item">
			<?php $item = page_item(12); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>	
		<?php if($item = page_item(13)):?>
		<div class="item">
			<?php $item = page_item(13); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php thumbnail($item->Files[0]); ?></a>
		</div>
		<?php endif; ?>
		</div>

</div>