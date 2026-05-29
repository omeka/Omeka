<li class="element new">
    <div class="sortable-item drawer">
        <?php
        echo $this->formText(
            $element_name_name, $element_name_value,
            [
                'placeholder' => __('Element Name'),
                'class' => 'drawer-name'
            ]
        );
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
    </div>
    <div class="drawer-contents opened">
        <?php
        echo $this->formTextarea(
            $element_description_name, $element_description_value,
            [
                'placeholder' => __('Element Description'),
                'rows' => '3',
                'cols'=>'30'
            ]
        );
        ?>
    </div>
</li>
