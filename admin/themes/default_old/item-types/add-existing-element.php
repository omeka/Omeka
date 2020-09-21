<li class="element">
    <div class="sortable-item">
        <?php
        echo $this->formSelect(
            $element_id_name, $element_id_value,
            array('class' => 'existing-element-drop-down'),
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
        <a href="" class="delete-element"><?php echo __('Remove'); ?></a>
    </div>
    <div class="drawer-contents"></div>
</li>
