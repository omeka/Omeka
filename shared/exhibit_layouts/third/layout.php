<div class="third">
	
	<div id="items-gallery-third" class="items">
	<div class="item">
		<?php $item = page_item(1);?>
		<?php thumbnail($item->Files[0]); ?>
		<h2><?php echo $item->title; ?></h2>
		<p><em><?php echo $item->description; ?></em></p>
	</div>
	
	<div class="item">
		<?php $item = page_item(2);?>
		<?php thumbnail($item->Files[0]); ?>
		<h2><?php echo $item->title; ?></h2>
		<p><em><?php echo $item->description; ?></em></p>
	</div>
	</div>
	
	<div id="text-third">
	<div class="text">
		<?php echo page_text(1); ?>
	</div>
	</div>
</div>