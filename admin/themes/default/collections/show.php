<?php
    $collectionTitle = strip_formatting(metadata('collection', 'Name'));
    if ($collectionTitle != '') {
        $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
    } else {
        $collectionTitle = '';
    }
    $collectionTitle = __('Edit Collection #%s', metadata('collection', 'id')) . $collectionTitle;
?>
<?php head(array('title'=> $collectionTitle, 'bodyclass'=>'collections show')); ?>

            <div id="save" class="three columns omega">
            
                <div class="panel">
                    <?php if (has_permission(get_current_collection(), 'edit')): ?>    
                    <?php echo link_to_collection(__('Edit Collection'), array('class'=>'big green button'), 'edit'); ?>
                    <?php endif; ?>
                    <a href="<?php echo html_escape(public_uri('collections/show/'.metadata('collection', 'id'))); ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
                </div>            
                
            </div>
            
            <div class="seven columns alpha">

                <?php echo flash(); ?>
                <h2><?php echo __('Description'); ?></h2> 
                <p><?php echo metadata('collection', 'Description'); ?></p>
                                
                <h2><?php echo __('Collectors'); ?></h2>
                <ul id="collector-list">
                    <?php if (collection_has_collectors()): ?> 
                    <li><?php echo metadata('collection', 'Collectors', array('delimiter'=>'</li><li>')); ?></li>
                    <?php else: ?>
                    <li><?php echo __('No collectors.'); ?></li>
                    <?php endif; ?> 
                </ul>
                
                <?php if(total_items_in_collection() > 0): ?>
                <h2><?php echo __('Recently Added Items'); ?></h2>
                <ul>
                <?php while (loop_items_in_collection(10)): ?>
                    <li><span class="date"><?php echo format_date(metadata('item', 'Date Added')); ?></span><span class="title"> <?php echo link_to_item(); ?></span></li>
                <?php endwhile;?>
                </ul>
                <?php endif; ?>
                
                <h2><?php echo __('Total Number of Items'); ?></h2>
                <p><?php echo link_to_items_in_collection(); ?></p>

                
                <?php fire_plugin_hook('admin_append_to_collections_show_primary', array('collection' => $collection)); ?>
            
            </div>
        
<?php foot(); ?>
