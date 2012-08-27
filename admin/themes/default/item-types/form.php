<?php echo js('item-types'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
jQuery(document).ready(function () {
    var addNewRequestUrl = '<?php echo admin_uri('item-types/add-new-element'); ?>';
    var addExistingRequestUrl = '<?php echo admin_uri('item-types/add-existing-element'); ?>';
    var changeExistingElementUrl = '<?php echo admin_uri('item-types/change-existing-element'); ?>';

    Omeka.ItemTypes.manageItemTypes(addNewRequestUrl, addExistingRequestUrl, changeExistingElementUrl);
});
//]]>
</script>

<?php echo flash(); ?>

<div class="seven columns alpha">

    <fieldset id="type-information">

        <h2><?php echo __('Item Type Information'); ?></h2>
        <p id="required-note">* <?php echo __('Required Fields'); ?></p>
            
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
            <?php $itemTypeId = $itemtype ? $itemtype->id : null; ?>
            <?php echo $this->action('element-list', 'item-types', null, array('item-type-id' => $itemTypeId)); ?>
        </div>
    </fieldset>
</div>

<?php fire_plugin_hook('admin_append_to_item_types_form', array('item_type' => $itemtype)); ?>
