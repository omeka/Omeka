<?php 
$pageTitle = __('Browse Item Types') . ' ' . __('(%s total)', $total_records);
head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>

<?php if (has_permission('ItemTypes', 'add')): ?>
<?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
<?php endif ?>

    <table class="full">
        <thead>
            <tr>
                <th><?php echo __('Type Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
                <th><?php echo __('Total Items'); ?></th>
            </tr>
        </thead>
        <tbody>
        
<?php foreach (loop('ItemType') as $itemType): ?>
<tr class="itemtype">
    <td class="itemtype-name">
        <a href="<?php echo html_escape(record_url($itemType, 'show', 'item-types')); ?>"><?php echo html_escape($itemType->name); ?></a>
        <ul class="action-links group">
        <?php if (has_permission('ItemTypes', 'edit')): ?>
            <li><a class="edit" href="<?php echo html_escape(url('item-types/edit/' . $itemType->id)); ?>"><?php echo __('Edit'); ?></a></li>
        <?php endif; ?>        
        </ul>
    </td>
    <td class="itemtype-description"><?php echo html_escape($itemType->description); ?></td>
    <td><?php echo link_to_items_with_item_type(); ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php fire_plugin_hook('admin_append_to_item_types_browse_primary', array('item_types' => $this->item_types, 'view' => $this)); ?>
</div>
<?php foot(); ?>
