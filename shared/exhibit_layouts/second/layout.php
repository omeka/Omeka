<?php 
	//Name: Super dooper layout;
	//Description: Here is the description for the layout;
	//Author: Kris Kelly; 
?>

<div class="second">

	<ul class="items" id="item-gallery-second">
	<li class="item">
		<?php 
			$item = page_item(1);
		?>
	<?php echo $item->title; ?>
	</li>
	<li class="item">
		<?php 
			$item = page_item(2);
		?>
		<?php
		echo $item->title; 
		?>
	</li>
	<li class="item">
		<?php 
			$item = page_item(3);
		?>
		<?php
		echo $item->title; 
		?>
	</li>
	<li class="item">
		<?php 
			$item = page_item(4);
		?>
		<?php
		echo $item->title; 
		?>
	</li>
	</ul>
	<div id="text-second">
	<div class="text">
		<?php echo page_text(1); ?>
	</div>
	<div class="text">
		<?php echo page_text(1); ?>
	</div>
	</div>
</div>