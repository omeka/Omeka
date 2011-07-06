  <?php
    $itemTypeTitle = strip_formatting($itemtype->name);
    if ($itemTypeTitle != '') {
        $itemTypeTitle = ': &quot;' . html_escape($itemTypeTitle) . '&quot; ';
    } else {
        $itemTypeTitle = '';
    }
    $itemTypeTitle = __('Edit Item Type #%s', $itemtype->id) . $itemTypeTitle;
?>
<?php head(array('title'=> $itemTypeTitle,'bodyclass'=>'item-types')); ?>
<h1><?php echo $itemTypeTitle; ?></h1>

<?php if (has_permission('ItemTypes', 'delete')): ?>
    <?php echo delete_button(null, 'delete-item-type', __('Delete this Item Type'), array(), 'delete-record-form'); ?>
<?php endif; ?>

<div id="primary">
    <form id="edit-item-type-form" method="post" action="">
        <?php include 'form.php';?>
        <input type="submit" name="submit" value="<?php echo __('Save Changes'); ?>" class="submit" />
    </form>
</div>
<?php foot(); ?>
