<?php    
    $itemTitle = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
    if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = __('Item #%s', metadata('item', 'id')) . $itemTitle;
?>

<?php echo head(array('title' => $itemTitle, 'bodyclass'=>'items show')); ?>

<?php echo js_src('items'); ?>
        
<div id="edit" class="three columns omega">
    
    <ul class="pagination">
        <?php if (link_to_previous_item()): ?>
        <li id="previous-item" class="previous">
            <?php echo link_to_previous_item('Prev Item'); ?>
        </li>
        <?php endif; ?>
        <?php if (link_to_next_item()): ?>
        <li id="next-item" class="next">
            <?php echo link_to_next_item('Next Item'); ?>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="panel">
        <?php if (has_permission($item, 'edit')): ?>
        <?php 
        echo link_to_item(__('Edit'), array('class'=>'big green button'), 'edit'); ?>
        <?php endif; ?>
        <a href="<?php echo html_escape(public_url('items/show/'.metadata('item', 'id'))); ?>" class="big blue button" target="_blank">View Public Page</a>
        <?php echo link_to_item(__('Delete'), array('class' => 'delete-confirm big red button'), 'delete-confirm'); ?>
    </div>
    
    <div class="public-featured panel">
        <p><span class="label"><?php echo __('Public'); ?>:</span> <?php echo ($item->public) ? __('Yes') : __('No'); ?></p>
        <p><span class="label"><?php echo __('Featured'); ?>:</span> <?php echo ($item->featured) ? __('Yes') : __('No'); ?></p>
    </div>

    <div class="info panel">
        <h4><?php echo __('Bibliographic Citation'); ?></h4>
        <div>
            <p><?php echo item_citation();?></p>
        </div>
    </div>

    <div class="collection panel">
        <h4><?php echo __('Collection'); ?></h4>
        <div>
           <p><?php echo link_to_collection_for_item(); ?></p>
        </div>
    </div>

    <?php if (item_has_tags()): ?>
    <div class="tags panel">
        <h4><?php echo __('Tags'); ?></h4>
        <div id="tag-cloud">
            <?php echo common('tag-list', compact('item'), 'items'); ?>
        </div>
     </div>
    <?php endif; ?>
    
    <div class="file-metadata panel">
        <h4><?php echo __('View File Metadata'); ?></h4>
        <div id="file-list">
            <?php if (!item_has_files()):?>
                <p><?php echo __('There are no files for this item yet.');?> <?php echo link_to_item(__('Add a File'), array(), 'edit'); ?>.</p>
            <?php else: ?>
                <ul>
                    <?php foreach (loop('files', $this->item->Files) as $file): ?>
                        <li><?php echo link_to_file_metadata(array('class'=>'show', 'title'=>__('View File Metadata'))); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif;?>
        </div>
    </div>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <div><?php echo output_format_list(); ?></div>
    </div>

<?php fire_plugin_hook('admin_append_to_items_show_secondary', array('item' => $item, 'view' => $this)); ?>
</div>

<div class="seven columns alpha">
    <?php echo flash(); ?>            
        <?php if (item_has_files()): ?>
        <div id="item-images">
        <?php echo files_for_item(array('imageSize' => 'square_thumbnail'), array('class' => 'admin-thumb panel')); ?> 
        </div>
        <?php endif; ?>
    <?php echo all_element_texts('item'); ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_show_secondary', array('item' => $item, 'view' => $this)); ?>
</div>        
<?php echo foot();?>
