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

<?php if (has_permission('ItemTypes', 'delete')): ?>
    <?php echo delete_button(null, 'delete-item-type', 'Delete this Item Type', array(), 'delete-record-form'); ?>
<?php endif; ?>

<div id="primary">
    <form id="edit-item-type-form" method="post" action="">
        <?php include 'form.php';?>
        <input type="submit" name="submit" value="Save Changes" class="submit" />
    </form>
</div>
<?php foot(); ?>
