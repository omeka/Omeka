<?php 
$pageTitle = __('Browse Collections');
head(array('title'=>$pageTitle, 'bodyclass'=>'collections')); ?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', $total_records); ?></h1>
<?php if (has_permission('Collections', 'add')): ?>
    <p id="add-collection" class="add-button"><a href="<?php echo html_escape(uri('collections/add')); ?>" class="add add-collection"><?php echo __('Add a Collection'); ?></a></p>
<?php endif; ?>

<div id="primary">
    <?php echo flash(); ?>
    <?php if (has_collections()): ?>
        <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if (has_collections_for_loop()): ?>
        <table id="collections" class="simple" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                <?php browse_headings(array(
                    __('ID') => 'id',
                    __('Name') => 'name',
                    __('Collectors') => null,
                    __('Date Added') => 'added',
                    __('Total Number of Items') => null
                )); ?>
                <?php if (has_permission('Collections', 'edit')): ?>
                    <th scope="col"><?php echo __('Edit?'); ?></th>                
                <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php $key = 0;?>
        <?php while (loop_collections()): ?>
        
            <tr class="collection<?php if(++$key%2==1) echo ' odd'; else echo ' even'; ?>">
                <td scope="row"><?php echo collection('id');?>
                </td> 
                <td class="title"><?php echo link_to_collection(); ?></td>
                <td>
                <?php if (collection_has_collectors()): ?> 
                    <ul>
                        <li><?php echo collection('Collectors', array('delimiter'=>'</li><li>')); ?></li>
                    </ul>
                <?php else: ?>
                    <ul>
                        <li><?php echo __('No collectors'); ?></li>
                    </ul>
                <?php endif; ?>
                
                </td>   
                <td><?php if($time = collection('Date Added')):?>
                    <?php echo format_date($time); ?>
                <?php endif; ?>
                </td>
                <td><?php echo link_to_items_in_collection(); ?></td>
                
                <?php if (has_permission('Collections', 'edit')): ?>
                <td>
                    <?php echo link_to_collection(__('Edit'), array('class'=>'edit'), 'edit'); ?>
                </td>
                <?php endif; ?>
            </tr>
        
            

        <?php endwhile; ?>
        </tbody>
        </table>
      <?php else: ?>
        <p><?php echo __('There are no collections on this page.'); ?> <?php echo link_to('collections', null, __('View All Collections')); ?></p>
      <?php endif; ?> 
    <?php else: ?>
        <p><?php echo __('There are no collections in your archive.'); ?> <?php if (has_permission('Collections', 'add')): ?><?php link_to('collections', 'add', __('Add a collection.')); ?><?php endif; ?></p>
    <?php endif; ?>
    
    <?php fire_plugin_hook('admin_append_to_collections_browse_primary', $collections); ?>
</div>      
<?php foot(); ?>
