<?php 
	//Name: Image List Left;
	//Description: An image gallery, with a full-size image on the left;
	//Author: Jeremy Boggs; 
?>

<div class="image-list-left">
	
		<?php if($item = page_item(1)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(1); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(2)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(2); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(3)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(3); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(4)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(4); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(5)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(5); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(6)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(6); ?></div>
		</div>
		<?php endif; ?>	
		
		<?php if($item = page_item(7)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(7); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(8)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(8); ?></div>
		</div>
		<?php endif; ?>	
		
		<?php if($item = page_item(9)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(9); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(10)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(10); ?></div>
		</div>
		<?php endif; ?>	
		
		<?php if($item = page_item(11)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(11); ?></div>
		</div>
		<?php endif; ?>
		
		<?php if($item = page_item(12)):?>
		<div class="exhibit-item">
			<?php exhibit_fullsize($item); ?>
		<div class="item-text"><?php echo page_text(12); ?></div>
		</div>
		<?php endif; ?>	
</div>