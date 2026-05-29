<?php
$type_name = $item_type->name;
if ($type_name != '') {
    $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
} else {
    $type_name = '';
}
$title = __('Edit Item Type #%s', $item_type->id) . $type_name;

$successAlertTemplate = __("<span class='nav-item-title'></span> reordered. ");
$failAlertTemplate = __("Cannot reorder further.");
$upActionAlertTemplate = __("Moved above <span class='positional-nav-item-title'></span>.");
$downActionAlertTemplate = __("Moved below <span class='positional-nav-item-title'></span>.");

echo head(['title'=> $title,'bodyclass'=>'item-types']);
echo flash();
?>

<form method="post" action="">
    <div class="sr-only" role="alert" id="reorder-alerts" 
        data-success-alert-template="<?php echo $successAlertTemplate; ?>" 
        data-fail-alert-template="<?php echo $failAlertTemplate; ?>" 
        data-up-action-alert-template="<?php echo $upActionAlertTemplate; ?>"
        data-down-action-alert-template="<?php echo $downActionAlertTemplate; ?>">
    </div>
    <?php include 'form.php';?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" class="full-width green button" name="submit" value="<?php echo __('Save Changes'); ?>">
            <?php if (is_allowed('ItemTypes', 'delete')): ?>
                <?php echo link_to($item_type, 'delete-confirm', __('Delete'), ['class' => 'full-width red button delete-confirm']); ?>
            <?php endif; ?>
            <?php fire_plugin_hook("admin_item_types_panel_buttons", ['view'=>$this, 'record'=>$item_type]); ?>
            <?php fire_plugin_hook("admin_item_types_panel_fields", ['view'=>$this, 'record'=>$item_type]); ?>
        </div>
    </section>
</form>

<script type="text/javascript">
Omeka.enableKeyboardNavigation('li.element', '.element-order');
Omeka.enableSorting('li.element', '.element-order')
Omeka.manageDrawers('#type-elements');
</script>
<?php echo foot(); ?>
