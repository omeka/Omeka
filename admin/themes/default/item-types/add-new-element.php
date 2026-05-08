<li class="element new">
    <div class="sortable-item drawer">
        <span id="move-<?php echo $element_id_temp; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-label="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $element_id_temp; ?> element-<?php echo $element_id_temp; ?>-name"></span>
        <?php
        echo $this->formText(
            $element_name_name, $element_name_value,
            [
                'placeholder' => __('Element Name'),
                'class' => 'drawer-name'
            ]
        );
        ?>
        <button type="button" id="return-element-link-<?php echo $element_id_temp; ?>" class="undo-delete has-tooltip" data-action-selector="deleted" aria-labelledby="return-element-link-<?php echo $element_id_temp; ?> elements-to-add-<?php echo $element_id_temp; ?>-id">
            <span class="icon" aria-hidden="true"></span>
            <div class="tooltip" popover="auto"><?php echo __('Undo'); ?></div>
        </button>
        <button type="button" id="remove-element-link-<?php echo $element_id_temp; ?>" class="delete-drawer has-tooltip" data-action-selector="deleted" aria-labelledby="remove-element-link-<?php echo $element_id_temp; ?> elements-to-add-<?php echo $element_id_temp; ?>-id">
            <span class="icon" aria-hidden="true"></span>
            <div class="tooltip" popover="auto"><?php echo __('Remove'); ?></div>
        </button>
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
