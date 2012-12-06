<?php
queue_js_file('element-sets');
echo head(array('title' => __('Settings'), 'bodyclass'=>'settings edit-item-type-elements'));
echo common('settings-nav');
echo flash();
?>
<form method="post" id="edit-item-type-elements">
    <section class="seven columns alpha">
        <p class="explanation">
            <?php echo __('You can customize the element descriptions below. You can order the elements by editing a specific item type.'); ?>
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
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
     </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_edit_item_type', __('Save Changes'), array('class' => 'big green button')); ?>
        </div>
    </section>
</form>
<script type="text/javascript">
//<![CDATA[
Omeka.addReadyCallback(Omeka.ElementSets.enableElementRemoval);
Omeka.addReadyCallback(Omeka.ElementSets.confirmDeleteElement);
//]]>
</script>
<?php echo foot(); ?>
