<?php 
	//Name: Text Image Left;
	//Description: A full page of text, with a full-size image floated left;
	//Author: Jeremy Boggs; 
?>

<div class="text-image-left">
	<div class="primary">
		<div class="item">
			<?php $item = page_item(1); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php fullsize($item->Files[0]); ?></a>
			</div>			
	<?php echo page_text(1); ?>
	</div>
</div>