<li class="element">
    <div class="sortable-item drawer">
        <?php
        echo $this->formText(
            $element_name_name, $element_name_value,
            array(
                'placeholder' => __('Element Name'),
                'class' => 'drawer-name'
            )
        );
        ?>
        <button type="button" class="delete-element" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"><span class="icon" aria-hidden="true"></span></button>
        <?php
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            array('class' => 'element-order')
        );
        ?>
    </div>
    <div class="drawer-contents opened">
        <?php
        echo $this->formTextarea(
            $element_description_name, $element_description_value,
            array(
                'placeholder' => __('Element Description'),
                'rows' => '3',
                'cols'=>'30'
            )
        );
        ?>
    </div>
</li>
