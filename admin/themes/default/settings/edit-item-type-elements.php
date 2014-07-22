<?php
queue_js_file('element-sets');
echo head(array('title' => __('Settings'), 'bodyclass'=>'settings edit-item-type-elements'));
echo common('settings-nav');
echo flash();
?>
<form method="post" id="edit-item-type-elements">
    <section class="seven columns alpha">
        <p class="explanation">
        <?php
        echo __('Customize the element descriptions below. To re-order the elements, %s.',
            '<a href="' . url('item-types') . '">' . __('edit a specific item type') . '</a>');
        ?>
        </p>
        <p><?php echo __($element_set->description); ?></p>
        <input type="hidden" name="elements-to-delete" id="elements-to-delete" value="" />
        <ul class="ui-sortable item-type-metadata">
        <?php foreach ($element_set->getElements() as $element): ?>
            <li class="element">
                <div class="sortable-item">
                    <?php echo __($element->name); ?>
                    <a href="#" class="undo-delete"><?php echo __('Undo'); ?></a>
                    <a href="#" class="delete-element"><?php echo __('Delete'); ?></a>
                    <?php echo $this->formHidden("elements[{$element->id}][delete]"); ?>
                </div>
                <div class="drawer-contents">
                    <label for="<?php echo "elements[{$element->id}][description]"; ?>"><?php echo __('Description'); ?></label>
                    <?php echo $this->formTextarea("elements[{$element->id}][description]", $element->description, array('rows' => '3')); ?>
                    <?php fire_plugin_hook('admin_settings_item_type_form_each', array('element_set' => $element_set, 'element' => $element, 'view' => $this)); ?>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php fire_plugin_hook('admin_settings_item_type_form', array('element_set' => $element_set, 'view' => $this)); ?>
     </section>
    <?php echo $csrf; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_edit_item_type', __('Save Changes'), array('class' => 'big green button')); ?>
        </div>
    </section>
</form>
<div id="confirm-delete-dialog" title="<?php echo __('Confirm Delete'); ?>">
<?php echo __('Are you sure you want to delete the selected item type elements from ' 
. 'the database? You will lose all text related to the selected elements. This cannot ' 
. 'be undone.'); ?>
</div>
<script type="text/javascript">
Omeka.addReadyCallback(Omeka.ElementSets.enableElementRemoval);
Omeka.addReadyCallback(Omeka.ElementSets.confirmDeleteElement,
    [<?php echo js_escape(__('Ok')); ?>, <?php echo js_escape(__('Cancel')); ?>]);
</script>
<?php echo foot(); ?>
