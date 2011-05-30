<?php
    $collectionTitle = strip_formatting(collection('Name'));
    if ($collectionTitle != '') {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = __('Edit Collection #%s', collection('id')) . $collectionTitle;
?>

<?php head(array('title'=> $collectionTitle, 'bodyclass'=>'collections')); ?>
<h1><?php echo $collectionTitle; ?></h1>

<div id="primary">
	
<form method="post">
<?php include 'form.php';?>	
<input type="submit" name="submit" class="submit" id="save-changes" value="<?php echo __('Save Changes'); ?>" />
</form>

<?php echo delete_button(null, 'delete-collection', __('Delete this Collection')); ?>

</div>
<?php foot(); ?>
