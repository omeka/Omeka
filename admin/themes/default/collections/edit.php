<?php
    $collectionTitle = strip_formatting(collection('Name'));
    if ($collectionTitle != '') {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = 'Edit Collection #' . collection('id') . $collectionTitle;
?>

<?php head(array('title'=> $collectionTitle, 'bodyclass'=>'collections')); ?>
<h1><?php echo $collectionTitle; ?></h1>

<div id="primary">
	
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" class="submit submit-medium" id="save-changes" value="Save Changes" />
<p id="delete_link"><?php echo link_to($collection, 'delete', 'Delete this Collection', array('class'=>'delete')); ?></p>
</form>

</div>
<?php foot(); ?>