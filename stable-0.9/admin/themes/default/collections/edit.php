<?php head(array('title'=>'Edit Collection #'.$collection->id, 'body_class'=>'collections')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
	
<h2><?php echo h($collection->name); ?></h2>

<form method="post">
<?php include 'form.php';?>	
<p id="form_submits"><button type="submit" name="submit">Save Changes</button> or <a class="cancel" href="<?php echo uri('collections/show/').$collection->id; ?>">Cancel</a></p>
<p id="delete_link"><a class="delete" href="<?php echo uri('collections/delete/').$collection->id; ?>">Delete This Collection</a></p>
</form>

</div>
<?php foot(); ?>