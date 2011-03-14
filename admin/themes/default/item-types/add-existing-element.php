<tr class="existing-element-row">
    <td class="element-name"><?php echo select_item_type_elements(array('name'=> ($addExistingElementIdPrefix . $elementTempId), 'class'=>'existing-element-drop-down'), $elementId, ''); ?></td>
    <td><?php echo html_escape($elementDescription); ?></td>
    <td><?php echo html_escape($elementDataTypeName); ?></td>
    <td class="element-order"><?php echo $this->formText(($addExistingElementOrderPrefix . $elementTempId), $elementOrder, array('size'=>2)); ?></td>
    <?php if (has_permission('ItemTypes', 'delete-element')): ?>
        <td><a href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
    <?php endif; ?>
</tr>