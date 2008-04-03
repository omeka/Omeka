<?php head(array('title'=>'Edit Item', 'body_class'=>'items'));?>
<?php common('archive-nav'); ?>
<div id="primary">
	
	<h1>Edit &#8220;<?php echo h($item->title); ?>&#8221;</h1>

	<form method="post" enctype="multipart/form-data" id="item-form">
		<?php include 'form.php'; ?>
		</div>
		<p id="item-form-submits"><button type="submit" name="submit">Save Changes</button> or <a href="<?php echo uri("items/show/".$item->id); ?>" id="cancel_changes" class="cancel">Cancel</a></p>
		<p id="delete_item_link"><a href="<?php echo uri('items/delete/'.$item->id); ?>" class="delete">Delete This Item</a></p>
	</form>



</div>
<?php foot();?>
