<div class="field" id="type-select">
    <div class="two columns alpha">
        <?php echo $this->formLabel('item-type', __('Item Type')); ?>
    </div>
    <div class="inputs five columns omega">
    <?php
        echo $this->formSelect(
            'item_type_id',
            $item->item_type_id,
            array('id' => 'item-type'),
            get_table_options('ItemType')
        );
    ?>
    </div>
<input type="submit" name="change_type" id="change_type" value="<?php echo __('Pick this type'); ?>" />  
</div>
<div id="type-metadata-form">
<?php echo common('change-type', compact('item'), 'items'); ?>
</div>
<?php fire_plugin_hook('admin_items_form_item_types', array('item' => $item, 'view' => $this)); ?>
