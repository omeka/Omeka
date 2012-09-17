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
            <?php echo $form->getElement(Omeka_Form_ItemTypes::SUBMIT_EDIT_ELEMENT_ID); ?>
            <?php if (has_permission('ItemTypes', 'delete')): ?>
                <?php echo link_to($itemtype, 'delete-confirm', __('Delete this Item Type'), array('class' => 'big red button delete-confirm')); ?>
            <?php endif; ?>
        </div>
    </form>

<?php foot(); ?>
