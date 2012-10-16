<?php
    $type_name = strip_formatting($item_type->name);
    if ($type_name != '') {
        $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
    } else {
        $type_name = '';
    }
    $title = __('Edit Item Type #%s', $item_type->id) . $type_name;
?>
<?php echo head(array('title'=> $title,'bodyclass'=>'item-types')); ?>

    <form id="edit-item-type-form" method="post" action="">
        <?php include 'form.php';?>
        <div id="save" class="three columns omega panel">
            <?php echo $form->getElement(Omeka_Form_ItemTypes::SUBMIT_EDIT_ELEMENT_ID); ?>
            <?php if (is_allowed('ItemTypes', 'delete')): ?>
                <?php echo link_to($item_type, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
            <?php endif; ?>
            <?php fire_plugin_hook("admin_append_to_item_types_panel_buttons", array('item_type'=> get_current_record('item_type'))); ?>
            <?php fire_plugin_hook("admin_append_to_item_types_panel_fields", array('item_type'=> get_current_record('item_type'))); ?>
        </div>
    </form>

<?php echo foot(); ?>
