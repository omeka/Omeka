<?php
queue_js_file('element-sets');
echo head(
    array(
        'title' => __('Edit Element Set'),
        'bodyclass' => 'element-sets'
    )
);
echo common('settings-nav');
echo flash();
?>
<form method='post'>
    <section class="seven columns alpha">
        <h2><?php echo __($element_set->name); ?></h2>

    <?php if (ElementSet::ITEM_TYPE_NAME == $element_set->name): ?>
        <p class="explanation">You can customize the element descriptions below. You 
        can order the elements by editing the specific item type.</p>
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
        
    <?php else: ?>
        <p class="explanation">You can click and drag the elements into your preferred 
        display order. Click the right arrows to add customized comments to elements.</p>
        <p><?php echo __($element_set->description); ?></p>
        <ul class="sortable">
        <?php foreach ($element_set->getElements() as $element): ?>
            <li class="element">
            <div class="sortable-item">
                <?php echo __($element->name); ?>
                <?php echo $this->formHidden("elements[{$element->id}][order]", $element->order, array('class' => 'element-order')); ?>
            </div>
            <div class="drawer-contents">
                <?php echo __($element->description); ?>
                <label for="<?php echo "elements[{$element->id}][comment]"; ?>"><?php echo __('Comment'); ?></label>
                <?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment, array('rows' => '3')); ?>
            </div>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    </section>

    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_edit_elements', 'Save Changes', array('class' => 'big green button')); ?>
        </div>
    </section>
</form>
<script type="text/javascript">
//<![CDATA[
Omeka.addReadyCallback(Omeka.ElementSets.enableSorting);
Omeka.addReadyCallback(Omeka.ElementSets.addHideButtons);
Omeka.addReadyCallback(Omeka.ElementSets.enableElementRemoval);
//]]>
</script>
<?php echo foot(); ?>
