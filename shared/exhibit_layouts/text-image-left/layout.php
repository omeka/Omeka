<?php 
	//Name: Text Image Left;
	//Description: A full page of text, with a full-size image floated left;
	//Author: Jeremy Boggs; 
?>

<div class="text-image-left">
	<div class="primary">
		<div class="item">
			<?php $item = page_item(1); ?>
			<?php exhibit_fullsize($item); ?>
			</div>	
		</div>
		<div class="secondary">		
	<?php echo page_text(1); ?>
	</div>
</div>