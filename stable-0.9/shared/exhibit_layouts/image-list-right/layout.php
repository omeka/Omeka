<?php 
	//Name: Image List Right;
	//Description: An image gallery, with a wider right column;
	//Author: Jeremy Boggs; 
?>

<div class="image-list-right">
		<?php for ($i=1; $i <= 12; $i++): ?>
    		<?php if($item = page_item($i)):?>
    		    <div class="exhibit-item">
    			    <?php echo exhibit_fullsize($item); ?>
    		        <div class="item-text"><?php echo page_text($i); ?></div>
    		    </div>
    		<?php endif; ?>
		<?php endfor;?>
</div>