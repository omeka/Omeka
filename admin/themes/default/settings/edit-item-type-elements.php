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
                <ul class="item-type-metadata drawers">
        <?php foreach ($element_set->getElements() as $element): ?>
            <?php $elementId = $element->id; ?>
            <li class="element">
                <div class="drawer">
                    <span id="element-<?php echo $elementId; ?>-name" class="drawer-name"><?php echo __($element->name); ?></span>
                    <?php $buttonToggleLabel = ' element-' . $elementId . '-name element-' . $elementId . '-toggle'; ?>
                    <button type="button" id="return-element-link-<?php echo html_escape($elementId); ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="return-element-link-<?php echo html_escape($elementId); ?> element-<?php echo $elementId; ?>-name"><span class="icon" aria-hidden="true"></span></button>
                    <button type="button" id="remove-element-link-<?php echo html_escape($elementId); ?>" class="delete-drawer"  data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="remove-element-link-<?php echo html_escape($elementId); ?> element-<?php echo $elementId; ?>-name"><span class="icon" aria-hidden="true"></span></button>
                    <?php echo $this->formHidden("elements[{$elementId}][delete]", "", array('class' => 'element-delete-hidden')); ?>
                </div>
                <div class="drawer-contents opened">
                    <label for="<?php echo "elements[{$elementId}][description]"; ?>"><?php echo __('Description'); ?></label>
                    <?php echo $this->formTextarea("elements[{$elementId}][description]", $element->description, array(
                            'rows' => '3', 
                            'id' => "elements[{$element->id}][description]",
                            'aria-labelledby' => "elements[{$elementId}][description]")
                        ); 
                    ?>
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
            <?php echo $this->formSubmit('submit_edit_item_type', __('Save Changes'), array('class' => 'full-width green button')); ?>
        </div>
    </section>
</form>
<div id="confirm-delete-dialog" title="<?php echo __('Confirm Delete'); ?>">
<?php echo __('Are you sure you want to delete the selected item type elements from '
. 'the database? You will lose all text related to the selected elements. This cannot '
. 'be undone.'); ?>
</div>
<script type="text/javascript">
Omeka.addReadyCallback(Omeka.manageDrawers);
Omeka.addReadyCallback(Omeka.ElementSets.enableElementRemoval);
Omeka.addReadyCallback(Omeka.ElementSets.confirmDeleteElement,
    [<?php echo js_escape(__('Ok')); ?>, <?php echo js_escape(__('Cancel')); ?>]);
</script>
<?php echo foot(); ?>
