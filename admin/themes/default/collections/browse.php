<?php 
$pageTitle = __('Browse Collections') . ' ' .  __('(%s total)', $total_results);
echo head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>

    <?php echo flash(); ?>
    <?php if (total_records('Collection') > 0): ?>
        <div class="table-actions">
        <?php if (is_allowed('Collections', 'add')): ?>
            <a href="<?php echo html_escape(url('collections/add')); ?>" class="small green button"><?php echo __('Add a Collection'); ?></a>
        <?php endif; ?>
        </div>
        <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if (has_loop_records('collections')): ?>
        <table id="collections" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                <?php echo browse_headings(array(
                    __('Title') => 'Dublin Core,Title',
                    __('Contributors') => null,
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
                    <?php if (is_allowed($collection, 'edit')): ?>
                    <ul class="action-links">
                        <li><?php echo link_to_collection(__('Edit'), array('class'=>'edit'), 'edit'); ?></li>
                    </ul>
                    <?php endif; ?>
                </td>
                <td>
                <?php if ($collection->hasContributor()): ?> 
                    <?php echo metadata('collection', array('Dublin Core', 'Contributor'), array('all'=>true, 'delimiter'=>'<br>')); ?>
                <?php else: ?>
                    <?php echo __('No contributors'); ?>
                <?php endif; ?>
                
                </td>   
                <td><?php if($time = metadata('collection', 'Added')):?>
                    <?php echo format_date($time); ?>
                <?php endif; ?>
                </td>
                <td><?php echo link_to_items_in_collection(); ?></td>
            </tr>
        
            

        <?php endforeach; ?>
        </tbody>
        </table>

        <?php if (is_allowed('Collections', 'add')): ?>
            <a href="<?php echo html_escape(url('collections/add')); ?>" class="small green button"><?php echo __('Add a Collection'); ?></a>
        <?php endif; ?>

      <?php else: ?>
      
        <p><?php echo __('There are no collections on this page.'); ?> <?php echo link_to('collections', null, __('View All Collections')); ?></p>
      
      <?php endif; ?> 
    
    <?php else: ?>

        <h2><?php echo __('You have no collections.'); ?></h2>
        <?php if(is_allowed('Collections', 'add')): ?>
            <p><?php echo __('Get started by adding your first collection.'); ?></p>
            <a href="<?php echo html_escape(url('collections/add')); ?>" class="add big green button"><?php echo __('Add a Collection'); ?></a>
        <?php endif; ?>

    <?php endif; ?>
    
    <?php fire_plugin_hook('admin_append_to_collections_browse_primary', array('collections' => $collections)); ?>
</div>      
<?php echo foot(); ?>
