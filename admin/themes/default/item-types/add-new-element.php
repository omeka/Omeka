<tr class="new-element-row">
    <td class="element-name"><?php echo $this->formText($element_name_name, $element_name_value); ?></td>
    <td><?php echo $this->formTextarea($element_description_name, $element_description_value, array('rows'=>'10', 'cols'=>'30')); ?></td>
    <td class="element-order"><?php echo $this->formText( $element_order_name, $element_order_value, array('size'=>2)); ?></td>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <td><a href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
    <?php endif; ?>
</tr>
