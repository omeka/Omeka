<?php 
	//Name: Image List Right Thumbs;
	//Description: An image gallery, with the thumbnail image on the right;
	//Author: Jeremy Boggs; 
?>

<div class="image-list-right">
	
		<?php for ($i=1; $i <= 12; $i++): ?>
    		<?php if($item = page_item($i)):?>
    		    <div class="exhibit-item">
    			    <?php echo exhibit_thumbnail($item, array('class'=>'permalink')); ?>
    		        <div class="item-text"><?php echo page_text($i); ?></div>
    		    </div>
    		<?php endif; ?>
		<?php endfor;?>
</div>