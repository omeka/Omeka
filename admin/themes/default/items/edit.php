<?php head(array('title'=>'Edit Item', 'body_class'=>'items'));?>
<?php common('archive-nav'); ?>
<div id="primary">
	
	<h1>Edit &#8220;<?php echo item('Title', ', '); ?>&#8221;</h1>

	<form method="post" enctype="multipart/form-data" id="item-form">
		<?php include 'form.php'; ?>
		</div>
		<p id="item-form-submits">
		    <button type="submit" name="submit">Save Changes</button> or 
		    <?php echo link_to_item('show', 'Cancel', 
		        array('id'=>'cancel_changes', 'class'=>'cancel')); ?>
		</p>
		<p id="delete_item_link">
		    <?php echo link_to_item('delete', 'Delete This Item', 
		        array('class'=>'delete')); ?>
		</p>
	</form>



</div>
<?php foot();?>
