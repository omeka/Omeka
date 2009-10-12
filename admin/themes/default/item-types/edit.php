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
<form method="post" action="">
    <?php include 'form.php';?>
<input type="submit" name="submit" value="Save Changes" class="submit submit-medium" /></p>
    <?php if (has_permission('ItemTypes', 'delete')): ?>
        <p id="delete_link"><a class="delete" href="<?php echo html_escape(record_uri($itemtype, 'delete', 'item-types')); ?>">Delete This Item Type</a></p>     
    <?php endif; ?>
</form>

<div id="element-form">
<?php 
// Render the add-element action, which renders the element-form partial.
echo $this->action('add-element', 'item-types', null, array('item-type-id'=>$itemtype->id)); ?>
</div>

</div>
<?php foot(); ?>