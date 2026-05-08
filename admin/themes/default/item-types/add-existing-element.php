<li class="element">
    <div class="sortable-item drawer">
        <span id="move-<?php echo $element_id_temp; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-label="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $element_id_temp; ?> element-<?php echo $element_id_temp; ?>-name"></span>
        <?php
        echo $this->formSelect(
            $element_id_name, $element_id_value,
            [
                'class' => 'existing-element-drop-down drawer-name',

            ],
            get_table_options(
                'Element', null,
                [
                    'element_set_name' => ElementSet::ITEM_TYPE_NAME,
                    'sort' => 'alpha'
                ]
            )
        );
        ?>
        <?php
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            ['class' => 'element-order']
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
    </div>
    <div class="drawer-contents opened"></div>
</li>
