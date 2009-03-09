<?php head(array('title'=>'Edit Collection #'.$collection->id, 'bodyclass'=>'collections')); ?>
<h1>Edit <?php echo h($collection->name); ?></h1>

<div id="primary">
	

<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" class="submit submit-medium" id="save-changes" value="Save Changes" />
<p id="delete_link"><a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">Delete This Collection</a></p>
</form>

</div>
<?php foot(); ?>