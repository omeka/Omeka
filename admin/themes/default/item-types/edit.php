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

    <form id="edit-item-type-form" method="post" action="">
        <?php include 'form.php';?>
        <div id="save" class="three columns omega panel">
            <input type="submit" name="submit" value="<?php echo __('Save Changes'); ?>" class="submit big green button" />
            <?php if (has_permission('ItemTypes', 'delete')): ?>
                <?php echo delete_button(null, 'delete-item-type', __('Delete this Item Type'), array('class' => 'big red button'), 'delete-record-form'); ?>
            <?php endif; ?>
        </div>
    </form>

<?php foot(); ?>
