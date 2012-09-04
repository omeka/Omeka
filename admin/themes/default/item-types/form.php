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
            <?php echo $this->formLabel('name', __('Name'), array('class' => 'required two columns alpha')); ?>
            <div class="inputs five columns omega">
            <?php echo $this->formText('name', $itemtype->name); ?>
            </div>
        </div>
        <div class="field">
        <?php echo $this->formLabel('description', __('Description'), array('class' => 'two columns alpha')); ?>
            <div class="inputs five columns omega">
            <?php echo $this->formTextarea('description', $itemtype->description, array('rows'=>'10', 'cols'=>'40')); ?>
            </div>
        </div>
    </fieldset>
    <?php if ($itemtype->exists()): ?>
    
    <fieldset id="type-elements">
        <h2><?php echo __('Elements'); ?></h2>
        <div id="element-list">
            <?php echo $this->action('element-list', 'item-types', null, array('item-type-id' => $itemtype->id)); ?>
        </div>
    </fieldset>

</div>
<?php endif; ?>

<?php fire_plugin_hook('admin_append_to_item_types_form', array('item_type' => $itemtype, 'view' => $this)); ?>
