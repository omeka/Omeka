<?php head(); ?>

<items>
<?php foreach ($items as $item): ?>
	<?php 
	//Use the 'item' partial to display the info for the item
	common('_item', compact('item'), 'items'); ?>
<?php 
endforeach; 
?>

</items>
