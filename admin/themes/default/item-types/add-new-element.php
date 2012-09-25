<tr class="new-element-row">
    <td class="element-name"><?php echo $this->formText($elementNameId, $elementNameValue); ?></td>
    <td><?php echo $this->formTextarea($elementDescriptionId, $elementDescriptionValue, array('rows'=>'10', 'cols'=>'30')); ?></td>
    <td class="element-order"><?php echo $this->formText( $elementOrderId, $elementOrderValue, array('size'=>2)); ?></td>
    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
        <td><a href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
    <?php endif; ?>
</tr>
