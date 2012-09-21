<?php
    $type_name = strip_formatting($item_type->name);
    if ($type_name != '') {
        $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
    } else {
        $type_name = '';
    }
    $title = __('Item Type #%s', $item_type->id) . $type_name;
?>
<?php echo head(array('title'=> $title,'bodyclass'=>'item-types'));?>
    <div id="edit" class="three columns omega">
        <div class="panel">
        <?php if ( has_permission('ItemTypes','edit') ): ?>
        <a class="edit big green button" href="<?php echo html_escape(record_url($item_type, 'edit', 'item-types')); ?>"><?php echo __('Edit'); ?></a>
        <?php endif; ?>
        <?php if ( has_permission('ItemTypes','delete') ): ?>
        <a class="edit big red button" href="<?php echo html_escape(record_url($item_type, 'delete-confirm', 'item-types')); ?>"><?php echo __('Delete'); ?></a>
        <?php endif; ?>
        </div>
    </div>
    <div class="seven columns alpha">
        <?php echo flash(); ?>
        <div id="type-info">
            <h2><?php echo __('Description'); ?></h2>
            <p><?php echo html_escape($item_type->description); ?></p>
            <h2><?php echo __('Type Metadata'); ?></h2>
            <dl class="type-metadata">
                <?php foreach($item_type->Elements as $element): ?>
                <dt><?php echo html_escape($element->name); ?></dt>
                <dd><?php echo html_escape($element->description); ?></dd>
                <?php endforeach; ?>
            </dl>
        </div>
    
        <div id="type-items">
            <h2><?php echo __('Recently Added Items'); ?></h2>
            <?php if($item_type->Items != null): ?>
            <ul>
            <?php set_loop_records('items', $item_type->Items); ?>
            <?php foreach (loop('items') as $item): ?>
            <li><span class="date"><?php echo format_date(metadata('item', 'Date Added')); ?></span> <?php echo link_to_item('<span class="title">' . metadata('item', array('Dublin Core', 'Title')) . '</span>') ?></li>
            <?php endforeach;?>
            </ul>
            <?php else: ?>
            <p><?php echo __('There are no recently added items.'); ?></p>
            <?php endif;?>
            
            <h2><?php echo __('Total Number of Items'); ?></h2>
            <p><?php echo link_to_items_with_item_type(); ?></p>
        </div>
    </div>
    
    <?php fire_plugin_hook('admin_append_to_item_types_show_primary', array('item_type' => $item_type, 'view' => $this)); ?>
</div>
<?php echo foot();?>
