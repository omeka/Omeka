<?php if($item = item($_REQUEST['id'])): ?>
<div class="item">
	<div class="item_id">
		<?php echo $item->id; ?>
	</div>
	
	<?php 
		if(has_thumbnail($item)){
			thumbnail($item);
		} else {
			echo $item->title;
		}
	?>
</div>
<?php endif; ?>