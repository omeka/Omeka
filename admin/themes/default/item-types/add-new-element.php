<li class="element">
    <div class="sortable-item">
        <?php echo $this->formText($element_name_name, $element_name_value); ?>
        <?php echo $this->formHidden( $element_order_name, $element_order_value, array('size'=>2, 'class' => 'element-order')); ?>
    </div>
    <div class="drawer-contents">
    <?php echo $this->formTextarea($element_description_name, $element_description_value, array('rows'=>'3', 'cols'=>'30')); ?>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <a href="" class="delete-element red button"><?php echo __('Remove'); ?></a>
    <?php endif; ?>
    </div>
</li>