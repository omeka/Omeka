<div class="field" id="type-select">
    <?php echo label('item-type', __('Item Type')); ?>
    <div class="inputs">
    <?php echo select_item_type_for_item(array( 
                'name'  => 'item_type_id',
                'id'    => 'item-type' )); ?>
            </div>
<input type="submit" name="change_type" id="change_type" value="<?php echo __('Pick this type'); ?>" />  
</div>
<div id="type-metadata-form">
<?php common('change-type', compact('item'), 'items'); ?>
</div>