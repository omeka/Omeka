<tr class="existing-element-row">
    <td class="element-name"><?php echo $this->formSelect($element_id_name, $element_id_value, array('class'=>'existing-element-drop-down'), get_table_options('Element', null, array('element_set_name'=>ElementSet::ITEM_TYPE_NAME,'sort'=>'alpha'))); ?></td>
    <td><?php echo html_escape($element_description); ?></td>
    <td class="element-order"><?php echo $this->formText($element_order_name, $element_order_value, array('size'=>2)); ?></td>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <td><a href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
    <?php endif; ?>
</tr>
