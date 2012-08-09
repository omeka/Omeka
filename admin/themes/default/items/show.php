<?php    
    $itemTitle = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
    if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
        $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
    } else {
        $itemTitle = '';
    }
    $itemTitle = __('Item #%s', metadata('item', 'id')) . $itemTitle;
?>

<?php head(array('title' => $itemTitle, 'bodyclass'=>'items show')); ?>

<?php echo js('items'); ?>
        
<div id="save" class="three columns omega">
    
    <ul class="pagination">
        <?php if(link_to_previous_item()): ?>
        <li id="previous-item" class="previous">
            <?php echo link_to_previous_item('Prev Item'); ?>
        </li>
        <?php endif; ?>
        <?php if(link_to_next_item()): ?>
        <li id="next-item" class="next">
            <?php echo link_to_next_item('Next Item'); ?>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="panel">
        <?php if (has_permission($item, 'edit')): ?>
        <?php 
        echo link_to_item(__('Edit Item'), array('class'=>'big green button'), 'edit'); ?>
        <?php endif; ?>
        <a href="<?php echo html_escape(public_uri('items/show/'.metadata('item', 'id'))); ?>" class="big blue button" target="_blank">View Public Page</a>
        <?php echo delete_button(null, 'delete-item', __('Delete this Item'), array('class'=>'big red button'), 'delete-record-form'); ?>
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
           <p><?php if (item_belongs_to_collection()) echo metadata('item', 'Collection Name'); else echo __('No Collection'); ?></p>
        </div>
    </div>

    <div class="panel">
        <div id="tags" class="info-panel">
            <h4><?php echo __('Tags'); ?></h4>
            <div id="tag-cloud">
                <?php common('tag-list', compact('item'), 'items'); ?>
            </div>
        </div>
      </div>
    
    <div class="panel">
        <h4><?php echo __('View File Metadata'); ?></h4>
            <div id="file-list">
                <?php if(!item_has_files()):?>
                    <p><?php echo __('There are no files for this item yet.');?> <?php echo link_to_item(__('Add a File'), array(), 'edit'); ?>.</p>
                <?php else: ?>
                    <ul>
                        <?php while(loop_files_for_item()): ?>
                            <li><?php echo link_to_file_metadata(array('class'=>'show', 'title'=>__('View File Metadata'))); ?></li>
                        <?php endwhile; ?>
                    </ul>
                <?php endif;?>
            </div>
    </div>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <div><?php echo output_format_list(); ?></div>
    </div>

<?php fire_plugin_hook('admin_append_to_items_show_secondary', $item); ?>



</div>

<div class="seven columns alpha">
    <?php echo flash(); ?>            
        <?php if(item_has_files()): ?>
        <div id="item-images">
        <?php echo display_files_for_item(array('imageSize' => 'square_thumbnail', 'imgAttributes' => array('class' => 'admin-thumb'))); ?> 
        </div>
        <?php endif; ?>
    <?php echo show_item_metadata(); ?>
</div>

<?php fire_plugin_hook('admin_append_to_items_show_secondary', $item); ?>
</div>
        
    <?php foot();?>
