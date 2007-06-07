<?php 
	//Name: Super dooper layout;
	//Description: Here is the description for the layout;
	//Author: Kris Kelly; 
?>

<div class="first">
	<?php 
		echo $section->title; 
	?>
	
	<div class="item">
		<?php 
			$item = page_item(1);
		?>
		<?php
		echo $item->title; 
		?>
	</div>
	
	<div class="text">
		<?php echo page_text(1); ?>
	</div>

</div>