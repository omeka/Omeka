<?php 
	//Name: Text Image Right;
	//Description: A full page of text, with a full-size image floated right;
	//Author: Jeremy Boggs; 
?>

<div class="text-image-right">
	<div class="primary">
		<div class="exhibit-item">
			<?php $item = page_item(1); ?>
			<?php exhibit_fullsize($item); ?>
		</div>	
	</div>
	<div class="secondary">		
	<?php echo page_text(1); ?>
	</div>
</div>