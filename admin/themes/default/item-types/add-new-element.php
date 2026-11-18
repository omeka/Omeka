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
        <div id="<?php echo $moveId; ?>" class="move-handle">
            <span class="move icon" aria-hidden="true"></span>
            <div class="tooltip" popover="auto"><?php echo __('Move'); ?></div>
        </div>
        <label class="drawer-name">
        <?php
        echo __('Element Name');
        echo $this->formText($elementNameName, $elementName, ['class' => 'drawer-name']);
        ?>
        </label>
        <button type="button" id="<?php echo $returnId; ?>" class="has-tooltip undo-delete" aria-labelledby="<?php echo $returnId; ?>-tooltip" data-action-selector="deleted" aria-labelledby="<?php echo $returnId; ?> unnamed-element-label">
            <span class="icon" aria-hidden="true"></span>
            <div id="<?php echo $returnId; ?>-tooltip" class="tooltip" popover="auto"><?php echo __('Undo'); ?></div>
        </button>
        <button type="button" id="<?php echo $removeId; ?>" class="delete-drawer" data-action-selector="deleted" aria-labelledby="remove-element-link-<?php echo $removeId; ?> unnamed-element-label">
            <span class="icon" aria-hidden="true"></span>
            <div class="tooltip" popover="auto"><?php echo __('Remove'); ?></div>
        </button>
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
