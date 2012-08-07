<?php
    $itemTypeTitle = strip_formatting($itemtype->name);
    if ($itemTypeTitle != '') {
        $itemTypeTitle = ': &quot;' . html_escape($itemTypeTitle) . '&quot; ';
    } else {
        $itemTypeTitle = '';
    }
    $itemTypeTitle = __('Item Type #%s', $itemtype->id) . $itemTypeTitle;
?>
<?php head(array('title'=> $itemTypeTitle,'bodyclass'=>'item-types'));?>
    <div id="save" class="three columns omega">
        <div class="panel">
        <?php if ( has_permission('ItemTypes','edit') ): ?>
        <a class="edit big green button" href="<?php echo html_escape(record_uri($itemtype, 'edit', 'item-types')); ?>"><?php echo __('Edit this Item Type'); ?></a>
        <?php endif; ?>
        </div>
    </div>
    <div class="seven columns alpha">
        <?php echo flash(); ?>
        <div id="type-info">
            <h2><?php echo __('Description'); ?></h2>
            <p><?php echo html_escape($itemtype->description); ?></p>
            <h2><?php echo __('Type Metadata'); ?></h2>
            <dl class="type-metadata">
                <?php foreach($itemtype->Elements as $element): ?>
                <dt><?php echo html_escape($element->name); ?></dt>
                <dd><?php echo html_escape($element->description); ?></dd>
                <?php endforeach; ?>
            </dl>
        </div>
    
        <div id="type-items">
            <h2><?php echo __('Recently Added Items'); ?></h2>
            <?php if($itemtype->Items != null): ?>
            <ul>
            <?php set_items_for_loop($itemtype->Items); ?>
            <?php while(loop_items()): ?>
            <li><span class="date"><?php echo format_date(metadata('item', 'Date Added')); ?></span> <?php echo link_to_item('<span class="title">' . metadata('item', array('Dublin Core', 'Title')) . '</span>') ?></li>
            <?php endwhile;?>
            </ul>
            <?php else: ?>
            <p><?php echo __('There are no recently added items.'); ?></p>
            <?php endif;?>
            
            <h2><?php echo __('Total Number of Items'); ?></h2>
            <p><?php echo link_to_items_with_item_type(); ?></p>
        </div>
    </div>
    
    <?php fire_plugin_hook('admin_append_to_item_types_show_primary', $itemtype); ?>
</div>
<?php foot();?>
