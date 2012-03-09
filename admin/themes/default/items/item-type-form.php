<div class="field" id="type-select">
    <?php echo $this->formLabel('item-type', __('Item Type')); ?>
    <div class="inputs">
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
<?php common('change-type', compact('item'), 'items'); ?>
</div>
