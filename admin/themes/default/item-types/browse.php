<?php
$pageTitle = __('Browse Item Types') . ' ' . __('(%s total)', $total_results);
$totalItemsWithoutType = get_db()->getTable('Item')->count(array('item_type' => 0));
echo head(array('title' => $pageTitle,'bodyclass' => 'item-types browse'));
echo flash();
?>

<?php if (is_allowed('ItemTypes', 'add')): ?>
<?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
<?php endif ?>

<?php echo pagination_links(); ?>

<p class="without-item-type">
    <?php if ($totalItemsWithoutType):
        $withoutTypeMessage = __(plural('%s%d item%s has no type.', "%s%d items%s have no type.", $totalItemsWithoutType),
            '<a href="' . html_escape(url('items/browse?type=0')) . '">', $totalItemsWithoutType, '</a>');
    else:
        $withoutTypeMessage = __('All items have a type.');
    endif; ?>
    <?php echo $withoutTypeMessage; ?>
</p>

<div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th width="200px"><?php echo __('Type Name'); ?></th>
                <th><?php echo __('Description'); ?></th>
                <th><?php echo __('Total Items'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (loop('ItemType') as $item_type): ?>
            <tr class="itemtype">
                <td class="itemtype-name">
                    <span class="title"><a href="<?php echo html_escape(record_url($item_type, 'show', 'item-types')); ?>"><?php echo html_escape($item_type->name); ?></a></span>
                    <ul class="action-links group">
                    <?php if (is_allowed('ItemTypes', 'edit')): ?>
                        <li><a class="edit" href="<?php echo html_escape(url('item-types/edit/' . $item_type->id)); ?>"><?php echo __('Edit'); ?></a></li>
                    <?php endif; ?>
                    </ul>
                    <?php fire_plugin_hook('admin_item_types_browse_each', array('item_type' => $item_type, 'view' => $this)); ?>
                </td>
                <td class="itemtype-description"><?php echo html_escape($item_type->description); ?></td>
                <td><?php echo link_to_items_with_item_type(); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (is_allowed('ItemTypes', 'add')): ?>
<?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
<?php endif ?>

<?php echo pagination_links(); ?>

<p class="without-item-type"><?php echo $withoutTypeMessage; ?></p>

<?php fire_plugin_hook('admin_item_types_browse', array('item_types' => $this->item_types, 'view' => $this)); ?>

<?php echo foot(); ?>
