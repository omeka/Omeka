<li class="element">
    <div class="sortable-item drawer">
        <?php
        echo $this->formSelect(
            $element_id_name, $element_id_value,
            array(
                'class' => 'existing-element-drop-down drawer-name',
                'id' => 'new-element-[elementId]'
            ),
            get_table_options(
                'Element', null,
                array(
                    'element_set_name' => ElementSet::ITEM_TYPE_NAME,
                    'sort' => 'alpha'
                )
            )
        );
        ?>
        <?php
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            array('class' => 'element-order')
        );
        ?>
        <button type="button" id="return-element-link-[elementId]" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?>"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="remove-element-link-[elementId]" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"><span class="icon" aria-hidden="true"></span></button>
    </div>
    <div class="drawer-contents opened"></div>
</li>
