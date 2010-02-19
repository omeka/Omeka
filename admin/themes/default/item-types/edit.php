  <?php
    $itemTypeTitle = strip_formatting($itemtype->name);
    if ($itemTypeTitle != '') {
        $itemTypeTitle = ': &quot;' . html_escape($itemTypeTitle) . '&quot; ';
    } else {
        $itemTypeTitle = '';
    }
    $itemTypeTitle = 'Edit Item Type #' . $itemtype->id . $itemTypeTitle;
?>
<?php head(array('title'=> $itemTypeTitle,'bodyclass'=>'item-types')); ?>
<h1><?php echo $itemTypeTitle; ?></h1>

<div id="primary">
	<form id="edit-item-type-form" method="post" action="">
	    <?php include 'form.php';?>
		<input type="submit" name="submit" value="Save Changes" class="submit submit-medium" /></p>
	    <?php if (has_permission('ItemTypes', 'delete')): ?>
	        <p id="delete_link"><a class="delete-item-type delete" href="<?php echo html_escape(record_uri($itemtype, 'delete', 'item-types')); ?>">Delete This Item Type</a></p>     
	    <?php endif; ?>
	</form>
</div>
<?php foot(); ?>