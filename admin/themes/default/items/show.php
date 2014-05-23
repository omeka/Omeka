<?php    
$itemTitle = strip_formatting(metadata('item', array('Dublin Core', 'Title')));
if ($itemTitle != '' && $itemTitle != __('[Untitled]')) {
    $itemTitle = ': &quot;' . $itemTitle . '&quot; ';
} else {
    $itemTitle = '';
}
$itemTitle = __('Item #%s', metadata('item', 'id')) . $itemTitle;


echo head(array('title' => $itemTitle, 'bodyclass'=>'items show'));
echo flash();
?>

<section class="seven columns alpha">
    <?php echo flash(); ?>
    <?php
    echo item_image_gallery(
        array('linkWrapper' => array('class' => 'admin-thumb panel')),
        'square_thumbnail', true);
    ?>
    <?php echo all_element_texts('item'); ?>
    <?php fire_plugin_hook('admin_items_show', array('item' => $item, 'view' => $this)); ?>
</section>

<section class="three columns omega">
    <ul class="pagination">
        <?php if (($prevLink = link_to_previous_item_show(__('Prev Item')))): ?>
        <li id="previous-item" class="previous">
            <?php echo $prevLink; ?>
        </li>
        <?php endif; ?>
        <?php if (($nextLink = link_to_next_item_show(__('Next Item')))): ?>
        <li id="next-item" class="next">
            <?php echo $nextLink; ?>
        </li>
        <?php endif; ?>
    </ul>
    
    <div id="edit" class="panel">
        <?php if (is_allowed($item, 'edit')): ?>
        <?php 
        echo link_to_item(__('Edit'), array('class'=>'big green button'), 'edit'); ?>
        <?php endif; ?>
        <a href="<?php echo html_escape(public_url('items/show/'.metadata('item', 'id'))); ?>" class="big blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
        <?php if (is_allowed($item, 'delete')): ?>
        <?php echo link_to_item(__('Delete'), array('class' => 'delete-confirm big red button'), 'delete-confirm'); ?>
        <?php endif; ?>
    </div>
    
    <div class="public-featured panel">
        <p><span class="label"><?php echo __('Public'); ?>:</span> <?php echo ($item->public) ? __('Yes') : __('No'); ?></p>
        <p><span class="label"><?php echo __('Featured'); ?>:</span> <?php echo ($item->featured) ? __('Yes') : __('No'); ?></p>
    </div>

    <div class="collection panel">
        <h4><?php echo __('Collection'); ?></h4>
        <div>
           <p><?php echo link_to_collection_for_item(); ?></p>
        </div>
    </div>

    <?php if (metadata('item', 'has tags')): ?>
    <div class="tags panel">
        <h4><?php echo __('Tags'); ?></h4>
        <div id="tag-cloud">
            <?php echo common('tag-list', compact('item'), 'items'); ?>
        </div>
     </div>
    <?php endif; ?>
    
    <div class="file-metadata panel">
        <h4><?php echo __('File Metadata'); ?></h4>
        <div id="file-list">
            <?php if (!metadata('item', 'has files')):?>
                <p><?php echo __('There are no files for this item yet.');?> <?php echo link_to_item(__('Add a File'), array(), 'edit'); ?>.</p>
            <?php else: ?>
                <ul>
                    <?php foreach (loop('files', $this->item->Files) as $file): ?>
                        <li><?php echo link_to_file_show(array('class'=>'show', 'title'=>__('View File Metadata'))); ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif;?>
        </div>
    </div>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <div><?php echo output_format_list(); ?></div>
    </div>
    
    <div class="info panel">
        <h4><?php echo __('Bibliographic Citation'); ?></h4>
        <div>
            <p><?php echo metadata('item', 'citation', array('no_escape' => true));?></p>
        </div>
    </div>

    <?php fire_plugin_hook('admin_items_show_sidebar', array('item' => $item, 'view' => $this)); ?>
</section>

<?php echo foot();?>
