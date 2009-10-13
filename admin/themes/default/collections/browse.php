<?php head(array('title'=>'Browse Collections', 'bodyclass'=>'collections')); ?>
<h1>Browse Collections (<?php echo $total_records; ?> total)</h1>
<?php if (has_permission('Collections', 'add')): ?>
    <p id="add-collection" class="add-button"><a href="<?php echo html_escape(uri('collections/add')); ?>" class="add add-collection">Add a Collection</a></p>
<?php endif; ?>

<div id="primary">
    <?php if (has_collections()): ?>
        <div class="pagination"><?php echo pagination_links(); ?></div>
      <?php if (has_collections_for_loop()): ?>
        <table id="collections" class="simple" cellspacing="0" cellpadding="0">
            <thead>
                <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Collectors</th>
                <th scope="col">Date Added</th>
                <th scope="col">Total Number of Items</th>
                <?php if (has_permission('Collections', 'edit')): ?>
                    <th scope="col">Edit?</th>                
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
                        <li>No collectors</li>
                    </ul>
                <?php endif; ?>
                
                </td>   
                <td><?php if($time = collection('Date Added')):?>
                    <?php echo date('m.d.Y', strtotime($time)); ?>
                <?php endif; ?>
                </td>
                <td><?php echo link_to_items_in_collection(); ?></td>
                
                <?php if (has_permission('Collections', 'edit')): ?>
                <td>
                    <?php echo link_to_collection('Edit', array('class'=>'edit'), 'edit'); ?>
                </td>
                <?php endif; ?>
            </tr>
        
            

        <?php endwhile; ?>
        </tbody>
        </table>
      <?php else: ?>
        <p>There are no collections on this page.  <?php echo link_to('collections', null, 'View All Collections'); ?></p>
      <?php endif; ?> 
    <?php else: ?>
        <p>There are no collections in your archive. Why don't you <?php echo link_to('collections', 'add', 'add one'); ?>?</p>
        
    <?php endif; ?>
    
    <?php fire_plugin_hook('admin_append_to_collections_browse_primary', $collections); ?>
</div>      
<?php foot(); ?>
