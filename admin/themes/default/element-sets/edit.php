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

        <p class="explanation">
            <?php echo __('Click and drag the elements into the preferred display order. Click the right arrows to add customized comments to element descriptions.'); ?>
        </p>
        <p><?php echo __($element_set->description); ?></p>
        <ul class="sortable">
        <?php foreach ($element_set->getElements() as $element): ?>
            <?php $elementId = $element->id; ?>
            <li class="element">
            <div class="sortable-item drawer">
                <span id="move-<?php echo $elementId; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-label="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $elementId; ?> element-<?php echo $elementId; ?>"></span>
                <span id="element-<?php echo $elementId; ?>-name" class="drawer-name"><?php echo __($element->name); ?></span>
                <?php echo $this->formHidden("elements[{$element->id}][order]", $element->order, array('class' => 'element-order')); ?>
                <?php $buttonToggleLabel = 'element-' . $elementId . '-toggle element-' . $elementId . '-name element-' . $elementId . '-comment'; ?>
                <button type="button" id="element-<?php echo $elementId; ?>-toggle" aria-expanded="false" aria-label="<?php echo __('Show'); ?>" class="drawer-toggle" data-action-selector="opened" aria-labelledby="<?php echo $buttonToggleLabel; ?>" title="<?php echo __($element->name); ?> <?php echo __('Comment'); ?>"><span class="icon" aria-hidden="true"></span></button>
            </div>
            <div class="drawer-contents">
                <?php echo __($element->description); ?>
                <label id="element-<?php echo $elementId; ?>-comment"; ?><?php echo __('Comment'); ?></label>
                <?php echo $this->formTextarea("elements[{$element->id}][comment]", $element->comment, array('rows' => '3', 'aria-labelledby' => 'element-' . $elementId . '-name element-' . $elementId . '-comment')); ?>
                <?php fire_plugin_hook('admin_element_sets_form_each', array('element_set' => $element_set, 'element' => $element, 'view' => $this)); ?>
            </div>
            </li>
        <?php endforeach; ?>
        </ul>
        <?php fire_plugin_hook('admin_element_sets_form', array('element_set' => $element_set, 'view' => $this)); ?>
    </section>
    <?php echo $csrf; ?>
    <section class="three columns omega">
        <div id="save" class="panel">
            <?php echo $this->formSubmit('submit_edit_elements', __('Save Changes'), array('class' => 'big green button')); ?>
        </div>
    </section>
</form>
<script type="text/javascript">
//<![CDATA[
Omeka.addReadyCallback(Omeka.ElementSets.enableSorting);
Omeka.addReadyCallback(Omeka.ElementSets.enableElementRemoval);
Omeka.manageDrawers('.sortable');
//]]>
</script>
<?php echo foot(); ?>
