<?php 
$pageTitle = __('Browse Item Types');
head(array('title'=>$pageTitle,'bodyclass'=>'item-types')); ?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', $total_records); ?></h1>
<?php if (has_permission('ItemTypes', 'add')): ?>
<p id="add-item-type" class="add-button"><?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add')); ?></p>
<?php endif ?>

<div id="primary">
    <table>
        <thead>
            <tr>
                <th><?php echo __('Type Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
                <th><?php echo __('Total Items'); ?></th>
                <?php if (has_permission('ItemTypes', 'edit')): ?>
                <th><?php echo __('Edit?'); ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        
<?php while (loop_item_types()): ?>
<?php $itemtype = get_current_item_type();?>
<tr class="itemtype">
    <td class="itemtype-name"><a href="<?php echo html_escape(record_uri($itemtype, 'show', 'item-types')); ?>"><?php echo html_escape($itemtype->name); ?></a></td>
    <td class="itemtype-description"><?php echo html_escape($itemtype->description); ?></td>
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
