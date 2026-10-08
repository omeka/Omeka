<?php
$elementTempId = $this->elementTempId ?? time();
$elementId = $this->elementId ?? '';
$elementOrder = $this->elementOrder ?? '';

$stem = Omeka_Form_ItemTypes::ELEMENTS_TO_ADD_INPUT_NAME . "[$elementTempId]";
$elementIdName = $stem .'[id]';
$elementOrderName = $stem .'[order]';

$moveId = html_escape("move-$elementTempId");
$returnId = html_escape("return-element-link-$elementTempId");
$removeId = html_escape("remove-element-link-$elementTempId");
?>
<li class="element">
    <div class="sortable-item drawer">
        <span id="<?php echo $moveId; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-labelledby="<?php echo $moveId; ?> unnamed-element-label"></span>
        <label class="drawer-name">
        <?php
        echo __('Element Name');
        echo $this->formSelect(
            $elementIdName,
            $elementId,
            ['class' => 'existing-element-drop-down drawer-name'],
            get_table_options('Element', null, [
                'element_set_name' => ElementSet::ITEM_TYPE_NAME,
                'sort' => 'alpha'
            ])
        );
        ?>
        </label>
        <?php echo $this->formHidden($elementOrderName, $elementOrder, ['class' => 'element-order']); ?>
        <button type="button" id="<?php echo $returnId; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="<?php echo $returnId; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="<?php echo $removeId; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="<?php echo $removeId; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
    </div>
    <div class="drawer-contents opened"></div>
</li>
