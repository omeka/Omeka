<?php 
$pageTitle = __('Add Item Type');
echo head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>

<div id="primary">
    <form method="post" action="">
        <?php include 'form.php';?>
        <div id="save" class="three columns omega panel">
            <?php echo $form->getElement(Omeka_Form_ItemTypes::SUBMIT_ADD_ELEMENT_ID); ?>
            <?php fire_plugin_hook("admin_append_to_item_types_panel_buttons", array('item_type'=> get_current_record('item_type'))); ?>
            <?php fire_plugin_hook("admin_append_to_item_types_panel_fields", array('item_type'=> get_current_record('item_type'))); ?>            
        </div>
    </form>
</div>
<?php echo foot(); ?>
