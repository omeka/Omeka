<?php 
	//Name: Thumbnail Gallery;
	//Description: Displays a gallery of up to 12 items;
	//Author: Jeremy Boggs; 
?>

<div class="gallery-thumbnails-text-bottom">
<div class="primary">
	<?php if($item=page_item(1)):?>

	<div class="exhibit-item">
	<?php $item = page_item(1); ?>
	<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
</div>
<?php endif; ?>
<?php if($item=page_item(2)): ?>
<div class="exhibit-item">
	<?php $item = page_item(2); ?>
	<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
</div>
<?php endif; ?>
<?php if($item=page_item(3)):?>
<div class="exhibit-item">
	<?php $item = page_item(3); ?>
	<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
</div>
<?php endif; ?>
<?php if($item=page_item(4)):?>
<div class="exhibit-item">
	<?php $item = page_item(4); ?>
	<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
</div>
<?php endif; ?>
<?php if($item = page_item(5)):?>
<div class="exhibit-item">
	<?php $item = page_item(5); ?>
	<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
</div>
<?php endif; ?>
	<?php if($item = page_item(6)):?>
	<div class="exhibit-item">
		<?php $item = page_item(6); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(7)):?>
	<div class="exhibit-item">
		<?php $item = page_item(7); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(8)):?>
	<div class="exhibit-item">
		<?php $item = page_item(8); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(9)):?>
	<div class="exhibit-item">
		<?php $item = page_item(9); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(10)):?>
	<div class="exhibit-item">
		<?php $item = page_item(10); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(11)):?>
	<div class="exhibit-item">
		<?php $item = page_item(11); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	<?php if($item = page_item(12)):?>
	<div class="exhibit-item">
		<?php $item = page_item(12); ?>
		<?php link_to_thumbnail($item, array('class'=>'permalink')); ?>
	</div>
	<?php endif; ?>
	</div>
	<div class="secondary">
	<div class="exhibit-text">
	<?php echo page_text(1); ?>
	</div>
	</div>

</div>