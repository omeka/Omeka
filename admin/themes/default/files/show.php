<?php
$fileTitle = metadata('file', 'display title');

if ($fileTitle != '') {
    $fileTitle = ': &ldquo;' . $fileTitle . '&rdquo; ';
} else {
    $fileTitle = '';
}
$fileTitle = __('File #%s', metadata('file', 'id')) . $fileTitle;

echo head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary'));
echo flash();
?>

<section class="seven columns alpha">
    <div id="item-images">
        <?php echo file_markup($file); ?>
    </div>
    
    <?php echo all_element_texts('file'); ?>
    
    <?php fire_plugin_hook('admin_files_show', array('file' => $file, 'view' => $this)); ?>
</section>

<section class="three columns omega">
    <?php if (is_allowed('Files', 'edit') or $file->getItem()->wasAddedBy(current_user())): ?>    
    <div id="edit" class="panel">
        <?php echo link_to($file, 'edit', __('Edit'), array('class'=>'big green button')); ?>
        <?php if (is_allowed('Files', 'delete')): ?>
            <?php echo link_to($file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div id="item-metadata" class="panel">
        <h4><?php echo __('Item'); ?></h4>
        <p><?php echo link_to_item(null, array(), 'show', $file->getItem()); ?></p>
    </div>

    <div id="format-metadata" class="panel">
        <h4><?php echo __('Format Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Filename'); ?>:</dt>
        <dd><?php echo metadata('file', 'Filename'); ?></dd>
        <dt><?php echo __('Original Filename'); ?>:</dt>
        <dd><?php echo metadata('file', 'Original Filename'); ?></dd>
        <dt><?php echo __('File Size'); ?>:</dt>
        <dd><?php echo __('%s bytes', metadata('file', 'Size')); ?></dd>
        </dl>
    </div>

    <div id="file-history" class="panel">
        <h4><?php echo __('File History'); ?></h4>
        <dl>
        <dt><?php echo __('Date Added'); ?></dt>
        <dd><?php echo format_date(metadata('file', 'Added'), Zend_Date::DATE_MEDIUM); ?></dd>
        <dt><?php echo __('Date Modified'); ?></dt> 
        <dd><?php echo format_date(metadata('file', 'Modified'), Zend_Date::DATE_MEDIUM); ?></dd>
        <dt><?php echo __('Authentication'); ?></dt> 
        <dd><?php echo metadata('file', 'Authentication'); ?></dd>
        </dl>
    </div>

    <div id="type-metadata" class="panel">
        <h4><?php echo __('Type Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Mime Type'); ?>:</dt>
        <dd><?php echo metadata('file', 'MIME Type'); ?></dd>
        <dt><?php echo __('File Type / OS'); ?>:</dt>
        <dd><?php echo metadata('file', 'Type OS'); ?></dd>
        </dl>
    </div>

    <?php if (file_id3_metadata()): ?>
    <div id="id3-metadata" class="panel">
        <h4><?php echo __('Embedded Metadata'); ?></h4>
        <?php echo file_id3_metadata(); ?>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <?php echo output_format_list(); ?>
    </div>

    <?php fire_plugin_hook('admin_files_show_sidebar', array('file' => $file, 'view' => $this)); ?>
</section>
    
<?php echo foot();?>
