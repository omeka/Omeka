<div class="field">
<?php echo label('collection-id', __('Collection'));?>
<div class="inputs">
    <?php echo select_collection(array('name'=>'collection_id', 'id'=>'collection-id'),$item->collection_id); ?>
</div>
</div>

<?php fire_plugin_hook('admin_append_to_items_form_collection', $item); ?>