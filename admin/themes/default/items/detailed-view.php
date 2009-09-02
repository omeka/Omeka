<div id="detailed">
<?php while($item = loop_items()):?>
<div class="item">
    <h2><?php echo link_to_item(); ?></h2>
    
    <?php 
    // Note: this is duplicated elsewhere (items/show page).
    if (has_permission('Items', 'edit') or $item->wasAddedBy(current_user())): ?>
    <p class="edit-item"><?php echo link_to_item('Edit', array('class'=>'edit'), 'edit'); ?></p>
    <?php endif; ?>
    
    <ul class="public-featured-checkboxes">
    <li><span class="fieldname">Public</span> 
    <?php 
    $publicCheckboxProps = array('name'=>"items[" . item('id') . "][public]",'class'=>"make-public");
    if (!has_permission('Items', 'makePublic')) {
       $publicCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($publicCheckboxProps, item('Public')); ?></li>
    <li><span class="fieldname">Featured</span> 
    <?php 
    $featuredCheckboxProps = array('name'=>"items[" . item('id') . "][featured]",'class'=>"make-featured");
    if (!has_permission('Items', 'makeFeatured')) {
       $featuredCheckboxProps['disabled'] = 'disabled';
    }
    echo checkbox($featuredCheckboxProps, item('Featured')); ?>
    <?php echo hidden(array('name'=>"items[" . item('id') . "][id]"), item('id')); ?>   
    </li>
    </ul>
    <div class="item-description">
    <?php if (!item_has_thumbnail()): ?>
        <?php echo strip_formatting(item('Dublin Core', 'Description', array('snippet'=>300))); ?>
        <?php else: ?>
        <?php echo link_to_item(item_square_thumbnail(), array('class'=>'thumbnail')); ?>
        <?php echo strip_formatting(item('Dublin Core', 'Description', array('snippet'=>300))); ?>
    <?php endif; ?>
    </div>
    <div class="item-meta">
        <ul>
            <li><span class="fieldname">Creator:</span> <?php echo strip_formatting(item('Dublin Core', 'Creator', array('delimiter'=>', ', 'all'=>true))); ?></li>
            <li><span class="fieldname">Added:</span> <?php echo item('Date Added'); ?></li>
            <li><span class="fieldname">Collection:</span> <?php if (item_belongs_to_collection()) echo item('Collection Name'); else echo 'No Collection'; ?></li>
        </ul>
    </div>
    <div class="append-to-item-detail">
    <?php fire_plugin_hook('admin_append_to_items_browse_detailed_each'); ?>
    </div>
</div>
<?php endwhile; ?>
</div>