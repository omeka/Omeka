<?php 
$pageTitle = __('Browse Collections') . ' ' .  __('(%s total)', $total_records);
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>

<?php if (has_permission('Collections', 'add')): ?>
    <a href="<?php echo html_escape(uri('collections/add')); ?>" class="small green button"><?php echo __('Add a Collection'); ?></a>
<?php endif; ?>

    <?php echo flash(); ?>
    <?php if (total_collections() > 0): ?>
        <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if (has_collections_for_loop()): ?>
        <table id="collections" class="full" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                <?php browse_headings(array(
                    __('ID') => 'id',
                    __('Name') => 'name',
                    __('Collectors') => null,
                    __('Date Added') => 'added',
                    __('Total Number of Items') => null
                )); ?>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0;?>
        <?php while (loop_collections()): ?>
            <?php $collection = get_current_collection(); ?>
            <tr class="collection<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
                <td scope="row"><?php echo metadata('collection', 'id');?></td> 
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
        
            

        <?php endwhile; ?>
        </tbody>
        </table>
      <?php else: ?>
        <p><?php echo __('There are no collections on this page.'); ?> <?php echo link_to('collections', null, __('View All Collections')); ?></p>
      <?php endif; ?> 
    <?php else: ?>
        <p><?php echo __('There are no collections in your system.'); ?> <?php if (has_permission('Collections', 'add')): ?><?php link_to('collections', 'add', __('Add a collection.')); ?><?php endif; ?></p>
    <?php endif; ?>
    
    <?php fire_plugin_hook('admin_append_to_collections_browse_primary', array('collections' => $collections, 'view' => $this)); ?>
</div>      
<?php foot(); ?>
