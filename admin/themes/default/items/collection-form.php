<div class="field">
<?php echo $this->formLabel('collection-id', __('Collection'));?>
<div class="inputs">
    <?php
        echo $this->formSelect(
            'collection_id',
            $item->collection_id,
            array('id' => 'collection-id'),
            get_table_options('Collection')
        );
    ?>
</div>
</div>

<?php fire_plugin_hook('admin_append_to_items_form_collection', array('item' => $item)); ?>
