<?php
    $fileTitle = item_file('Dublin Core', 'Title') ? item_file('Dublin Core', 'Title') : strip_formatting(item_file('original filename'));

    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('File #%s', item_file('id')) . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>

<h1><?php echo $fileTitle; ?></h1>
<?php if (has_permission('Files', 'edit') or $file->getItem()->wasAddedBy(current_user())): ?>
    <p id="edit-file" class="edit-button"><?php echo link_to($file, 'edit', __('Edit this File'), array('class'=>'edit')); ?></p>
<?php endif; ?>
<div id="primary">
    <div id="fullsize-file">
        <?php echo display_file($file, array('imageSize' => 'fullsize')); ?>
    </div>
    <?php echo show_file_metadata(); ?>
    <div id="file-history" class="section">
    <h2><?php echo __('File History'); ?></h2>
    <h3><?php echo __('Date Added'); ?></h3> 
    <div class="element-text"><?php echo item_file('Date Added'); ?></div>
    <h3><?php echo __('Date Modified'); ?></h3> 
    <div class="element-text"><?php echo item_file('Date Modified'); ?></div>
    <h3><?php echo __('Authentication'); ?></h3> 
    <div class="element-text"><?php echo item_file('Authentication'); ?></div>
    </div>
    <?php fire_plugin_hook('admin_append_to_files_show_primary', $file); ?>
</div><!--end primary-->
<div id="secondary">
    
    <div id="format-metadata" class="info-panel">
        <h2><?php echo __('Format Metadata'); ?></h2>
    <dl>
    <dt><?php echo __('Archive Filename'); ?>:</dt> <dd><?php echo item_file('Archive Filename'); ?></dd>
    <dt><?php echo __('Original Filename'); ?>:</dt> <dd><?php echo item_file('Original Filename'); ?></dd>
    <dt><?php echo __('File Size'); ?>:</dt> <dd><?php echo item_file('Size'); ?> bytes</dd>
    </dl>
    </div>

    <div id="type-metadata" class="info-panel">
    <h2><?php echo __('Type Metadata'); ?></h2>
    <dl>
    <dt><?php echo __('Mime Type / Browser'); ?>:</dt> <dd><?php echo item_file('MIME Type'); ?></dd>
    <dt><?php echo __('Mime Type / OS'); ?>:</dt> <dd><?php echo item_file('MIME Type OS'); ?></dd>
    <dt><?php echo __('File Type / OS'); ?>:</dt> <dd><?php echo item_file('File Type OS'); ?></dd>
    </dl>
    </div>

    <div class="info-panel">
        <h2><?php echo __('Output Formats'); ?></h2>
        <div>
            <?php echo output_format_list(); ?>
        </div>
    </div>
    
    <?php fire_plugin_hook('admin_append_to_files_show_secondary', $file); ?>
</div>
<?php foot();?>
