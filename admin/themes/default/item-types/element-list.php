<ul id="item-type-elements" class="sortable">
        <?php $elementInfos = $this->form->getElementInfos(); ?>
        
        <?php foreach ($elementInfos as $elementInfo): ?>
            <?php 
                $element = $elementInfo['element'];
                $elementTempId = $elementInfo['temp_id'];
                $elementOrder = $elementInfo['order'];  
            ?>
            
            <?php if ($element && $elementTempId === null): ?>
            
            <li class="element">
                <div class="sortable-item">
                <strong><?php echo html_escape($element->name); ?></strong>
                <?php echo $this->formHidden("elements[$element->id][order]", $elementOrder, array('size'=>2, 'class' => 'element-order')); ?>
                <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
                <a id="return-element-link-<?php echo html_escape($element->id); ?>" href="" class="undo-delete"><?php echo __('Undo'); ?></a>
                <a id="remove-element-link-<?php echo html_escape($element->id); ?>" href="" class="delete-element"><?php echo __('Remove'); ?></a>
                <?php endif; ?>
                </div>
                
                <div class="drawer-contents">
                    <div class="element-description"><?php echo html_escape($element->description); ?></div>
                </div>
            </li>

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
        <li>
            <div class="add-new">
            <?php echo __('Add Element'); ?>
            </div>
            <div class="drawer-contents">
                <p>
                    <input type="radio" name="add-element-type" value="existing" checked="checked" /><?php echo __('Existing'); ?>
                    <input type="radio" name="add-element-type" value="new" /><?php echo __('New'); ?>
                </p>
                <button id="add-element" name="add-element"><?php echo __('Add Element'); ?></button>
            </div>
        </li>
</ul>
<?php echo $this->form->getElement(Omeka_Form_ItemTypes::REMOVE_HIDDEN_ELEMENT_ID); ?>
