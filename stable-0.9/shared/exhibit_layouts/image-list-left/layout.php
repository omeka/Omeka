<?php 
	//Name: Image List Left;
	//Description: An image gallery, with a full-size image on the left;
	//Author: Jeremy Boggs; 
?>

<div class="image-list-left">
    		
		<?php for ($i=1; $i <= 12; $i++): ?>
    		<?php if($item = page_item($i)):?>
    		    <div class="exhibit-item">
    			    <?php echo exhibit_fullsize($item); ?>
    		        <div class="item-text"><?php echo page_text($i); ?></div>
    		    </div>
    		<?php endif; ?>
		<?php endfor;?>
</div>