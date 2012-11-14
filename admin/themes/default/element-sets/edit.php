<?php
queue_js_file('element-sets');
echo head(array(
    'title'=> __('Edit Element Set'),
    'content_class' => 'vertical-nav',
    'bodyclass'=>'element-sets primary'
));
?>
<?php echo common('settings-nav'); ?>
<?php echo flash(); ?>
<form method='post'>

<section class="seven columns alpha">
    <h2><?php echo __($element_set->name); ?></h2>
    <?php if (ElementSet::ITEM_TYPE_NAME == $element_set->name): ?>
    <p class="explanation">You can customize the element descriptions below. You 
    can order the elements by editing the specific item type.</p>
    <p><?php echo __($element_set->description); ?></p>
    <ul>
    <?php foreach ($element_set->getElements() as $element): ?>
        <li><p><label for="<?php echo "elements[{$element->id}][name]"; ?>"><?php echo __('Name'); ?></label>
        <?php echo $this->formText("elements[{$element->id}][name]", $element->name); ?></p>
        <p><label for="<?php echo "elements[{$element->id}][description]"; ?>"><?php echo __('Description'); ?></label>
        <?php echo $this->formTextarea("elements[{$element->id}][description]", $element->description); ?></p>
        <p><label for="<?php echo "elements[{$element->id}][delete]"; ?>"><?php echo __('Delete?'); ?></label>
        <?php echo $this->formCheckbox("elements[{$element->id}][delete]"); ?></p>
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
        </div>
        <div class="drawer-contents">
            <p><?php echo __($element->description); ?></p>
            <label for="<?php echo "elements[{$element->id}][comment]"; ?>"><?php echo __('Comment'); ?></label>
            <?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment); ?>
            <?php echo $this->formHidden("elements[{$element->id}][order]", $element->order, array('class' => 'element-order')); ?>
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
<?php if (ElementSet::ITEM_TYPE_NAME != $element_set->name): ?>
<script type="text/javascript">
//<![CDATA[
Omeka.addReadyCallback(Omeka.ElementSets.enableSorting);
Omeka.addReadyCallback(Omeka.ElementSets.addHideButtons);
//]]>
</script>
<?php endif; ?>
<?php echo foot(); ?>
