<?php 
$pageTitle = __('Browse Item Types') . ' ' . __('(%s total)', $total_results);
echo head(array('title' => $pageTitle,'bodyclass' => 'item-types')); ?>

<div class="table-actions">
    <?php if (is_allowed('ItemTypes', 'add')): ?>
    <?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
    <?php endif ?>
</div>

<table>
    <thead>
        <tr>
            <th><?php echo __('Type Name'); ?></th>
            <th><?php echo __('Description'); ?></th>
            <th><?php echo __('Total Items'); ?></th>
        </tr>
    </thead>
    <tbody>        
        <?php foreach (loop('ItemType') as $item_type): ?>
        <tr class="itemtype">
            <td class="itemtype-name">
                <a href="<?php echo html_escape(record_url($item_type, 'show', 'item-types')); ?>"><?php echo html_escape($item_type->name); ?></a>
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

<div class="table-actions">
    <?php if (is_allowed('ItemTypes', 'add')): ?>
    <?php echo link_to('item-types', 'add', __('Add an Item Type'), array('class'=>'add green button')); ?>
    <?php endif ?>
</div>

<?php fire_plugin_hook('admin_item_types_browse', array('item_types' => $this->item_types, 'view' => $this)); ?>

<?php echo foot(); ?>
