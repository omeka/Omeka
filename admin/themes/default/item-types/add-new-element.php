<li class="element new">
    <div class="sortable-item drawer">
        <span id="move-<?php echo $element_id_temp; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $element_id_temp; ?> <?php echo $element_id_temp; ?>"></span>
        <label class="drawer-name">
        <?php
        echo __('Element Name');
        echo $this->formText(
            $element_name_name, $element_name_value,
            [
                'class' => 'drawer-name'
            ]
        );
        ?>
        </label>
        <button type="button" id="return-element-link-<?php echo $element_id_temp; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="return-element-link-<?php echo $element_id_temp; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
        <button type="button" id="remove-element-link-<?php echo $element_id_temp; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>"  aria-labelledby="remove-element-link-<?php echo $element_id_temp; ?> unnamed-element-label"><span class="icon" aria-hidden="true"></span></button>
        <?php
        echo $this->formHidden(
            $element_order_name, $element_order_value,
            ['class' => 'element-order']
        );
        ?>
    </div>
    <div class="drawer-contents opened">
        <label>
        <?php
        echo __('Element Description');
        echo $this->formTextarea(
            $element_description_name, $element_description_value,
            [
                'rows' => '3',
                'cols'=>'30'
            ]
        );
        ?>
        </label>
    </div>
</li>
