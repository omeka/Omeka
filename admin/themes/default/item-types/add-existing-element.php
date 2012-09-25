<tr class="existing-element-row">
    <td class="element-name"><?php echo $this->formSelect($elementNameId, $elementNameValue, array('class'=>'existing-element-drop-down'), get_table_options('Element', null, array('element_set_name'=>ELEMENT_SET_ITEM_TYPE,'sort'=>'alpha'))); ?></td>
    <td><?php echo html_escape($elementDescription); ?></td>
    <td class="element-order"><?php echo $this->formText($elementOrderId, $elementOrderValue, array('size'=>2)); ?></td>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <td><a href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
    <?php endif; ?>
</tr>