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
</form>

<?php echo delete_button(null, 'delete-collection', 'Delete this Collection', array('class' => 'delete-button delete-collection')); ?>

</div>
<?php foot(); ?>