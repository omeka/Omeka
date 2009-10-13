<?php head(array('title'=>'Browse Item Types','bodyclass'=>'item-types')); ?>
<h1>Browse Item Types (<?php echo $total_records; ?> total)</h1>
<?php if (has_permission('ItemTypes', 'add')): ?>
<p id="add-item-type" class="add-button"><?php echo link_to('item-types', 'add', 'Add an Item Type', array('class'=>'add')); ?></p>
<?php endif ?>

<div id="primary">
    <table>
        <thead>
            <tr>
                <th>Type Name</th>
                <th>Description</th>
                <th>Total Number of Items</th>
                <?php if (has_permission('ItemTypes', 'edit')): ?>
                <th>Edit?</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        
<?php while (loop_item_types()): ?>
<?php $itemtype = get_current_item_type();?>
<tr class="itemtype">
    <td width="20%"><a href="<?php echo html_escape(record_uri($itemtype, 'show', 'item-types')); ?>"><?php echo html_escape($itemtype->name); ?></a></td>
    <td width="60%"><?php echo html_escape($itemtype->description); ?></td>
    <td><?php echo link_to_items_with_item_type(); ?></td>
    <?php if (has_permission('ItemTypes', 'edit')): ?><td>
        <a class="edit" href="<?php echo html_escape(uri('item-types/edit/'.$itemtype->id)); ?>">Edit</a>
    </td><?php endif; ?>
</tr>
<?php endwhile; ?>
</tbody>
</table>
<?php fire_plugin_hook('admin_append_to_item_types_browse_primary', $itemtypes); ?>
</div>
<?php foot(); ?>