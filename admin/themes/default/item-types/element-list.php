<?php 
$elementsToAdd = $this->form->getElementsToAdd(); 
$elementsToAddIsNew = $this->form->getElementsToAddIsNew();
$elementsToAddTempIds = $this->form->getElementsToAddTempIds();
$elementsOrder = $this->form->getElementsOrder();

?>

<table width="100%">
    <thead>
        <tr>
            <th><?php echo __('Element Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
            <th><?php echo __('Order'); ?></th>
            <?php if (has_permission('ItemTypes', 'delete-element')): ?>
                <th><?php echo __('Remove'); ?></th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody id="element-list-tbody">
        <?php $orderIndex = 0; ?>
        <?php foreach ($elements as $key => $element): ?>
            <tr class="element">
                <td class="element-name"><strong><?php echo html_escape($element->name); ?></strong></td>
                <td><?php echo html_escape($element->description); ?></td>
                <td class="element-order"><?php echo __v()->formText('element-order-'. $element->id, $elementsOrder[$orderIndex], array('size'=>2)); ?></td>

                <?php if (has_permission('ItemTypes', 'delete-element')): ?>
                <td><a id="remove-element-link-<?php echo html_escape($element->id); ?>" href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
                <?php endif; ?>
            </tr>
            <?php $orderIndex++; ?>
        <?php endforeach; ?>
        <?php $elementToAddIndex = 0; ?>
        <?php foreach ($elementsToAdd as $elementToAdd): ?>            
        <?php if ($elementsToAddIsNew[$elementToAddIndex] == true):  ?>
        <?php echo $this->action('add-new-element',
                                 'item-types',
                                  null,
                                  array('from_post' => true,
                                        'elementTempId' => $elementsToAddTempIds[$elementToAddIndex],
                                        'elementName' => $elementToAdd->name,
                                        'elementDescription' => $elementToAdd->description,
                                        'elementOrder' => $elementsOrder[$orderIndex]));
         ?>
         <?php else: ?>
         <?php echo $this->action('add-existing-element',
                                 'item-types',
                                  null,
                                  array('from_post' => true,
                                        'elementTempId' => $elementsToAddTempIds[$elementToAddIndex],
                                        'elementId' => $elementToAdd->id,
                                        'elementOrder' => $elementsOrder[$orderIndex]));
         ?>
         <?php endif; ?>
         <?php $elementToAddIndex++; ?>
         <?php $orderIndex++; ?>
         <?php endforeach;?>
        <?php echo $this->form->getElement(Omeka_Form_ItemTypes::REMOVE_HIDDEN_ELEMENT_ID); ?>
    </tbody>
</table>
<p style="float:right;">
<input type="radio" name="add-element-type" value="existing" checked="checked" /><?php echo __('Existing'); ?>
<input type="radio" name="add-element-type" value="new" /><?php echo __('New'); ?>
<button style="float:none; margin-left:10px;" id="add-element" name="add-element"><?php echo __('Add Element'); ?></button>
</p>
