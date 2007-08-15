<?php 
	//Name: Text Image Left;
	//Description: A full page of text, with a full-size image floated left;
	//Author: Jeremy Boggs; 
?>

<div class="text-image-left">
	<div class="primary">
		<div class="item">
			<?php $item = page_item(1); ?>
			<?php img_link_to_exhibit_item($item, array('class'=>'permalink'), 'fullsize'); ?>
			</div>	
		</div>
		<div class="secondary">		
	<?php echo page_text(1); ?>
	</div>
</div>