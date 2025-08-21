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
        <legend><?php echo __('Item Type Information'); ?></legend>
            
        <div class="field">
            <?php echo $this->form->getElement(Omeka_Form_ItemTypes::NAME_ELEMENT_ID); ?>
        </div>
        <div class="field">
            <?php echo $this->form->getElement(Omeka_Form_ItemTypes::DESCRIPTION_ELEMENT_ID); ?>
        </div>
    </fieldset>
    <fieldset id="type-elements">
        <legend><?php echo __('Elements'); ?></legend>
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
                    <div class="sortable-item drawer">
                    <?php $elementId = html_escape($element->id); ?>
                    <span id="move-<?php echo $elementId; ?>" class="move icon" title="<?php echo __('Move'); ?>" aria-label="<?php echo __('Move'); ?>" aria-labelledby="move-<?php echo $elementId; ?> element-<?php echo $elementId; ?>-name"></span>
                    <span id="element-<?php echo $elementId; ?>-name" class="drawer-name"><?php echo html_escape($element->name); ?></span>
                    <?php $buttonToggleLabel = 'element-' . $elementId . '-name element-' . $elementId . '-toggle'; ?>
                    <button type="button" id="element-<?php echo $elementId; ?>-toggle" aria-expanded="false" aria-label="<?php echo __('Show'); ?> <?php echo __('Description'); ?>" class="drawer-toggle" data-action-selector="opened" aria-labelledby="<?php echo $buttonToggleLabel; ?>" title="<?php echo __($element->name); ?> <?php echo __('Description'); ?>"><span class="icon" aria-hidden="true"></span></button>
                    <?php if (is_allowed('ItemTypes', 'delete-element')): ?>
                    <button type="button" id="remove-element-link-<?php echo $elementId; ?>" class="delete-drawer" data-action-selector="deleted" title="<?php echo __('Remove'); ?>" aria-label="<?php echo __('Remove'); ?>" aria-labelledby="remove-element-link-<?php echo $elementId; ?> element-<?php echo $elementId; ?>-name"><span class="icon" aria-hidden="true"></span></button>
                    <button type="button" id="return-element-link-<?php echo $elementId; ?>" class="undo-delete" data-action-selector="deleted" title="<?php echo __('Undo'); ?>" aria-label="<?php echo __('Undo'); ?> <?php echo __('Remove'); ?>" aria-labelledby="return-element-link-<?php echo $elementId; ?> element-<?php echo $elementId; ?>-name"><span class="icon" aria-hidden="true"></span></button>
                    <?php echo $this->formHidden("elements[$elementId][order]", $elementOrder, ['size'=>2, 'class' => 'element-order']); ?>
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
                        [
                            'from_post' => true,
                            'elementTempId' => $elementTempId,
                            'elementName' => $element->name,
                            'elementDescription' => $element->description,
                            'elementOrder' => $elementOrder
                        ]
                    );
                    ?>
                    <?php else: ?>
                    <?php echo $this->action(
                        'add-existing-element', 'item-types', null,
                        [
                            'from_post' => true,
                            'elementTempId' => $elementTempId,
                            'elementId' => $elementId,
                            'elementOrder' => $elementOrder
                        ]
                    );
                    ?>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; // end for each $elementInfos ?> 
                <li>
                    <div class="add-new">
                        <?php echo __('Add Element'); ?>
                    </div>
                    <div class="drawer-contents opened">
                        <div class="sr-only flash success" id="add-element-success" aria-live="polite" style="display: none"><?php echo __('New element added. Total Elements: '); ?><span class="element-count"></span></div>
                        <p>
                            <label><input type="radio" name="add-element-type" value="existing" checked="checked" /><?php echo __('Existing'); ?></label>
                            <label><input type="radio" name="add-element-type" value="new" /><?php echo __('New'); ?></label>
                        </p>
                        <button type="button" id="add-element" name="add-element"><?php echo __('Add Element'); ?></button>
                    </div>
                </li>
            </ul>
            <?php echo $this->form->getElement(Omeka_Form_ItemTypes::REMOVE_HIDDEN_ELEMENT_ID); ?>
        </div>
    </fieldset>
    <?php echo $this->form->getElement('csrf_token') ?>
    <?php fire_plugin_hook('admin_item_types_form', ['item_type' => $item_type, 'view' => $this]); ?>
</section>
