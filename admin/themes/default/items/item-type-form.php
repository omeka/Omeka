<div class="field" id="type-select" role="group" aria-labelledby="item-type-label">
    <div class="two columns alpha">
        <label id="item-type-label"><?php echo __('Item Type'); ?></label>
    </div>
    <div class="inputs five columns omega">
    <?php
        echo $this->formSelect(
            'item_type_id',
            $item->item_type_id,
            [
                'id' => 'item-type', 
                'aria-labelledby' => 'item-type-label',
                'data-sr-alert' => __('Item type metadata updated below.')
            ],
            get_table_options('ItemType')
        );
    ?>
    </div>
</div>
<div id="type-metadata-form" role="group" aria-labelledby="element-set-title">
<?php echo common('change-type', compact('item'), 'items'); ?>
</div>
<?php fire_plugin_hook('admin_items_form_item_types', ['item' => $item, 'view' => $this]); ?>
