<?php 
$pageTitle = __('Add Item Type');
echo head(['title'=>$pageTitle,'bodyclass'=>'item-types']);
echo flash();
?>

<form method="post" action="">
    <?php include 'form.php'; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" class="full-width green button" name="submit" value="<?php echo __('Add Item Type'); ?>">
            <?php fire_plugin_hook("admin_item_types_panel_buttons", ['view'=>$this, 'record'=>$item_type]); ?>
            <?php fire_plugin_hook("admin_item_types_panel_fields", ['view'=>$this, 'record'=>$item_type]); ?>
        </div>
    </section>
</form>
<?php echo foot(); ?>
