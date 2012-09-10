<?php 
$pageTitle = __('Browse Collections') . ' ' .  __('(%s total)', $total_records);
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>

    <?php echo flash(); ?>
    <?php if (total_collections() > 0): ?>
        <?php if (has_permission('Collections', 'add')): ?>
            <a href="<?php echo html_escape(uri('collections/add')); ?>" class="small green button"><?php echo __('Add a Collection'); ?></a>
        <?php endif; ?>
        <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if (has_loop_records('collections')): ?>
        <table id="collections" class="full" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                <?php browse_headings(array(
                    __('Name') => 'name',
                    __('Collectors') => null,
                    __('Date Added') => 'added',
                    __('Total Number of Items') => null
                )); ?>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0;?>
        <?php foreach (loop('Collection') as $collection): ?>
            <tr class="collection<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
                <td class="title<?php if ($collection->featured) { echo ' featured';} ?>">
                    <?php echo link_to_collection(); ?>
                    <?php if(!$collection->public): ?>
                    <?php echo __('(Private)'); ?>
                    <?php endif; ?>
                    <?php if (has_permission($collection, 'edit')): ?>
                    <ul class="action-links">
                        <li><?php echo link_to_collection(__('Edit'), array('class'=>'edit'), 'edit'); ?></li>
                    </ul>
                    <?php endif; ?>
                </td>
                <td>
                <?php if (collection_has_collectors()): ?> 
                    <?php echo metadata('collection', 'Collectors', array('delimiter'=>'<br>')); ?>
                <?php else: ?>
                    <?php echo __('No collectors'); ?>
                <?php endif; ?>
                
                </td>   
                <td><?php if($time = metadata('collection', 'Date Added')):?>
                    <?php echo format_date($time); ?>
                <?php endif; ?>
                </td>
                <td><?php echo link_to_items_in_collection(); ?></td>
            </tr>
        
            

        <?php endforeach; ?>
        </tbody>
        </table>

        <?php if (has_permission('Collections', 'add')): ?>
            <a href="<?php echo html_escape(uri('collections/add')); ?>" class="small green button"><?php echo __('Add a Collection'); ?></a>
        <?php endif; ?>

      <?php else: ?>
      
        <p><?php echo __('There are no collections on this page.'); ?> <?php echo link_to('collections', null, __('View All Collections')); ?></p>
      
      <?php endif; ?> 
    
    <?php else: ?>

        <h2><?php echo __('You have no collections.'); ?></h2>
        <?php if(has_permission('Collections', 'add')): ?>
            <p><?php echo __('Get started by adding your first collection.'); ?></p>
            <a href="<?php echo html_escape(uri('collections/add')); ?>" class="add big green button"><?php echo __('Add a collection'); ?></a>
        <?php endif; ?>

    <?php endif; ?>
    
    <?php fire_plugin_hook('admin_append_to_collections_browse_primary', array('collections' => $collections, 'view' => $this)); ?>
</div>      
<?php foot(); ?>
