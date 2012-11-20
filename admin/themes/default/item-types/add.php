<?php 
$pageTitle = __('Add Item Type');
echo head(array('title'=>$pageTitle,'bodyclass'=>'item-types'));
echo flash();
?>

<form method="post" action="">
    <?php include 'form.php'; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $form->getElement(Omeka_Form_ItemTypes::SUBMIT_ADD_ELEMENT_ID); ?>
            <?php fire_plugin_hook("admin_item_types_panel_buttons", array('view'=>$this, 'record'=>$item_type)); ?>
            <?php fire_plugin_hook("admin_item_types_panel_fields", array('view'=>$this, 'record'=>$item_type)); ?>            
        </div>
    </section>
</form>
<?php echo foot(); ?>
