<li class="element">
    <div class="sortable-item drawer">
        <?php
        echo $this->formSelect(
            $element_id_name, $element_id_value,
            array('class' => 'existing-element-drop-down drawer-name'),
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
        <button type="button" class="delete-drawer" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"><span class="icon" aria-hidden="true"></span></button>
    </div>
    <div class="drawer-contents opened"></div>
</li>
