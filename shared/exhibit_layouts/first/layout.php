<?php 
	//Name: Super dooper layout;
	//Description: Here is the description for the layout;
	//Author: Kris Kelly; 
?>

<div class="first">
	The current section's title is: <?php echo $section->title;?>
	
	<?php if($item = page_item(1)): ?>
	<div class="item">
		<?php
		echo $item->title; 
		?>
	</div>
	<?php endif; ?>
	
	<div class="text">
		The value of the first piece of text is: <?php echo page_text(1); ?>
	</div>


	<div class="text">
		The value of the second piece of text is: <?php echo page_text(2); ?>
	</div>
</div>