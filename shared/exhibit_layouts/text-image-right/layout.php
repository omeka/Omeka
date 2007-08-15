<?php 
	//Name: Text Image Right;
	//Description: A full page of text, with a full-size image floated right;
	//Author: Jeremy Boggs; 
?>

<div class="text-image-right">
	<div class="primary">
		<div class="exhibit-item">
			<?php $item = page_item(1); ?>
			<a href="<?php echo uri('items/show/'.$item->id); ?>" class="permalink"><?php fullsize($item->Files[0]); ?></a>
		</div>	
	</div>
	<div class="secondary">		
	<?php echo page_text(1); ?>
	</div>
</div>