<?php head(); ?>

<items>
<?php foreach ($items as $item): ?>
	<item id="<?php echo htmlspecialchars($item->id); ?>">
		<?php 
		//Use the 'item' partial to display the info for the item
		common('_item', compact('item'), 'items'); ?>
	</item>
<?php 
endforeach; 
?>

</items>
