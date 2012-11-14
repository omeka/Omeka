<li class="element">
    <div class="sortable-item">
        <?php echo $this->formSelect($element_id_name, $element_id_value, array('class'=>'existing-element-drop-down'), get_table_options('Element', null, array('element_set_name'=>ElementSet::ITEM_TYPE_NAME,'sort'=>'alpha'))); ?>
    <?php echo $this->formHidden($element_order_name, $element_order_value, array('size'=>2, 'class' => 'element-order')); ?>
    </div>
    <div class="drawer-contents">
    <?php echo html_escape($element_description); ?>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <a href="" class="delete-element red button"><?php echo __('Remove'); ?></a>
    <?php endif; ?>
    </div>
</li>
