<li class="element">
    <div class="sortable-item drawer">
        <?php
        echo $this->formText(
            $element_name_name, $element_name_value,
            array(
                'placeholder' => __('Element Name'),
                'class' => 'drawer-name',
                'id' => 'new-element-[elementId]'
            )
        );
        ?>
        <button type="button" id="return-element-link-[elementId]" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?>"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="remove-element-link-[elementId]" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"><span class="icon" aria-hidden="true"></span></button>
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
