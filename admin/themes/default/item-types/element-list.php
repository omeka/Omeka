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
        <?php $elementInfos = $this->form->getElementInfos(); ?>
        
        <?php foreach ($elementInfos as $elementInfo): ?>
            <?php 
                $element = $elementInfo['element'];
                $elementTempId = $elementInfo['temp_id'];
                $elementOrder = $elementInfo['order'];  
            ?>
            
            <?php if ($element && $elementTempId === null): ?>
            
            <tr class="element">
                <td class="element-name"><strong><?php echo html_escape($element->name); ?></strong></td>
                <td><?php echo html_escape($element->description); ?></td>
                <td class="element-order"><?php echo __v()->formText('element-order-'. $element->id, $elementOrder, array('size'=>2)); ?></td>

                <?php if (has_permission('ItemTypes', 'delete-element')): ?>
                <td><a id="remove-element-link-<?php echo html_escape($element->id); ?>" href="" class="delete-element"><?php echo __('Remove'); ?></a></td>
                <?php endif; ?>
            </tr>

            <?php else: ?>

                <?php if (!$element->exists()):  ?>
                <?php echo $this->action('add-new-element',
                                         'item-types',
                                          null,
                                          array('from_post' => true,
                                                'elementTempId' => $elementTempId,
                                                'elementName' => $element->name,
                                                'elementDescription' => $element->description,
                                                'elementOrder' => $elementOrder));
                 ?>
                 <?php else: ?>
                 <?php echo $this->action('add-existing-element',
                                         'item-types',
                                          null,
                                          array('from_post' => true,
                                                'elementTempId' => $elementTempId,
                                                'elementId' => $element->id,
                                                'elementOrder' => $elementOrder));
                 ?>
                 <?php endif; ?>

            <?php endif; ?>
        <?php endforeach; // end for each $elementInfos ?> 
         
        <?php echo $this->form->getElement(Omeka_Form_ItemTypes::REMOVE_HIDDEN_ELEMENT_ID); ?>
    </tbody>
</table>
<p style="float:right;">
<input type="radio" name="add-element-type" value="existing" checked="checked" /><?php echo __('Existing'); ?>
<input type="radio" name="add-element-type" value="new" /><?php echo __('New'); ?>
<button style="float:none; margin-left:10px;" id="add-element" name="add-element"><?php echo __('Add Element'); ?></button>
</p>
