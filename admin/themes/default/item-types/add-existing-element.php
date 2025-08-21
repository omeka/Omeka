<li class="element">
    <div class="sortable-item drawer">
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
        <button type="button" id="return-element-link-<?php echo $element_id_temp; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="return-element-link-<?php echo $element_id_temp; ?> elements-to-add-<?php echo $element_id_temp; ?>-id"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="remove-element-link-<?php echo $element_id_temp; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"  aria-labelledby="remove-element-link-<?php echo $element_id_temp; ?> elements-to-add-<?php echo $element_id_temp; ?>-id"><span class="icon" aria-hidden="true"></span></button>
    </div>
    <div class="drawer-contents opened"></div>
</li>
