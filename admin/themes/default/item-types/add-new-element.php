<?php
$elementTempId = $this->elementTempId ?? time();
$elementName = $this->elementName ?? '';
$elementDescription = $this->elementDescription ?? '';
$elementOrder = $this->elementOrder ?? '';

$stem = Omeka_Form_ItemTypes::NEW_ELEMENTS_INPUT_NAME . "[$elementTempId]";
$elementNameName = $stem . '[name]';
$elementDescriptionName = $stem . '[description]';
$elementOrderName = $stem . '[order]';

$moveId = html_escape("move-$elementTempId");
$returnId = html_escape("return-element-link-$elementTempId");
$removeId = html_escape("remove-element-link-$elementTempId");
?>
<li class="element new">
    <div class="sortable-item drawer">
        <span id="<?php echo $moveId; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-labelledby="<?php echo $moveId; ?> unnamed-element-label"></span>
        <label class="drawer-name">
        <?php
        echo __('Element Name');
        echo $this->formText($elementNameName, $elementName, ['class' => 'drawer-name']);
        ?>
        <div class="keyboard-reorder-group">
            <button type="button" class="keyboard-reorder" aria-label="<?php echo __('Reorder with keyboard'); ?>" title="<?php echo __('Reorder with keyboard'); ?>" aria-expanded="false" aria-controls="keyboard-reorder-<?php echo $element_id_temp; ?>"></button>
            <div class="keyboard-reorder-panel" id="keyboard-reorder-<?php echo $element_id_temp; ?>" role="group" aria-label="<?php echo __('Reorder actions'); ?>">
                <button type="button" class="keyboard-reorder-up" aria-label="<?php echo __('Move up'); ?>" title="<?php echo __('Move up'); ?>"></button>
                <button type="button" class="keyboard-reorder-down" aria-label="<?php echo __('Move down'); ?>" title="<?php echo __('Move down'); ?>"></button>
            </div>
        </div>
        <button type="button" id="return-element-link-<?php echo $element_id_temp; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="return-element-link-<?php echo $element_id_temp; ?> new-elements-<?php echo $element_id_temp; ?>-name"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="remove-element-link-<?php echo $element_id_temp; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"  aria-labelledby="remove-element-link-<?php echo $element_id_temp; ?> new-elements-<?php echo $element_id_temp; ?>-name"><span class="icon" aria-hidden="true"></span></button>
        <?php
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            ['class' => 'element-order']
        );
        ?>
        </label>
        <button type="button" id="<?php echo $returnId; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="<?php echo $returnId; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="<?php echo $removeId; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"  aria-labelledby="remove-element-link-<?php echo $removeId; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
        <?php echo $this->formHidden($elementOrderName, $elementOrder, ['class' => 'element-order']); ?>
    </div>
    <div class="drawer-contents opened">
        <label>
        <?php
        echo __('Element Description');
        echo $this->formTextarea($elementDescriptionName, $elementDescription, ['rows' => '3', 'cols' => '30']);
        ?>
        </label>
    </div>
</li>
