<?php head(array('title'=>'Edit Collection #'.html_escape($collection->id), 'bodyclass'=>'collections')); ?>
<h1>Edit <?php echo html_escape($collection->name); ?></h1>

<div id="primary">
	

<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" class="submit submit-medium" id="save-changes" value="Save Changes" />
<p id="delete_link"><?php echo link_to($collection, 'delete', 'Delete this Collection', array('class'=>'delete')); ?></p>
</form>

</div>
<?php foot(); ?>