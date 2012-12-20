<?php echo js_tag('item-types'); ?>
<script type="text/javascript">
jQuery(document).ready(function () {
    var addNewRequestUrl = '<?php echo admin_url('item-types/add-new-element'); ?>';
    var addExistingRequestUrl = '<?php echo admin_url('item-types/add-existing-element'); ?>';
    var changeExistingElementUrl = '<?php echo admin_url('item-types/change-existing-element'); ?>';

    Omeka.ItemTypes.manageItemTypes(addNewRequestUrl, addExistingRequestUrl, changeExistingElementUrl);
});
</script>

<section class="seven columns alpha">
    <fieldset id="type-information">
        <h2><?php echo __('Item Type Information'); ?></h2>
        <p class='explanation'>* <?php echo __('required field'); ?></p>
            
        <div class="field">
            <?php echo $this->form->getElement(Omeka_Form_ItemTypes::NAME_ELEMENT_ID); ?>
        </div>
        <div class="field">
            <?php echo $this->form->getElement(Omeka_Form_ItemTypes::DESCRIPTION_ELEMENT_ID); ?>
        </div>
    </fieldset>
    <fieldset id="type-elements">
        <h2><?php echo __('Elements'); ?></h2>
        <div id="element-list">
            <ul id="item-type-elements" class="sortable">
            <?php
            $elementInfos = $this->form->getElementInfos();
            foreach ($elementInfos as $elementInfo):
                $element = $elementInfo['element'];
                $elementTempId = $elementInfo['temp_id'];
                $elementOrder = $elementInfo['order'];

                if ($element && $elementTempId === null):
            ?>
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
                    <?php echo $this->action(
                        'add-new-element', 'item-types', null,
                        array(
                            'from_post' => true,
                            'elementTempId' => $elementTempId,
                            'elementName' => $element->name,
                            'elementDescription' => $element->description,
                            'elementOrder' => $elementOrder
                        )
                    );
                    ?>
                    <?php else: ?>
                    <?php echo $this->action(
                        'add-existing-element', 'item-types', null,
                        array(
                            'from_post' => true,
                            'elementTempId' => $elementTempId,
                            'elementId' => $element->id,
                            'elementOrder' => $elementOrder
                        )
                    );
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
        </div>
    </fieldset>

    <?php fire_plugin_hook('admin_item_types_form', array('item_type' => $item_type, 'view' => $this)); ?>
</section>
