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

<fieldset id="type-information">
    <legend><?php echo __('Item Type Information'); ?> <span id="required-note">* <?php echo __('Required Fields'); ?></span></legend>
    
    <div class="field">
        <?php echo label(array('name'=>'name', 'class' => 'required'), __('Name')); ?>
        <div class="inputs">
        <?php echo text(array('name'=>'name', 'class'=>'textinput', 'id'=>'name'),$itemtype->name); ?>
        </div>
    </div>
    <div class="field">
    <?php echo label('description', __('Description')); ?>
        <div class="inputs">
        <?php echo $this->formTextarea('description', $itemtype->description, array('class'=>'textinput', 'rows'=>'10', 'cols'=>'40')); ?>
        </div>
    </div>
</fieldset>
<?php if ($itemtype->exists()): ?>

<fieldset id="type-elements">
    <legend><?php echo __('Elements'); ?></legend>
    <div id="element-list">
        <?php echo $this->action('element-list', 'item-types', null, array('item-type-id' => $itemtype->id)); ?>
    </div>
</fieldset>
<?php endif; ?>

<?php fire_plugin_hook('admin_append_to_item_types_form', $itemtype); ?>
