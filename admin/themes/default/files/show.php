<?php
    $fileTitle = metadata('file', array('Dublin Core', 'Title')) ? strip_formatting(metadata('file', array('Dublin Core', 'Title'))) : metadata('file', 'original filename');

    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('File #%s', metadata('file', 'id')) . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>


<div id="save" class="three columns omega">

    <?php if (has_permission('Files', 'edit') or $file->getItem()->wasAddedBy(current_user())): ?>    
    <div class="panel">
        <?php echo link_to($file, 'edit', __('Edit this File'), array('class'=>'big green button')); ?>
    <?php if (has_permission('Files', 'delete')): ?>
        <?php echo delete_button(null, 'delete-file', __('Delete this File'), array('class' => 'big red button'), 'delete-record-form'); ?>
    <?php endif; ?>        
    </div>
    <?php endif; ?>

    
    <div id="format-metadata" class="panel">
        <h4><?php echo __('Format Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Filename'); ?>:</dt>
        <dd><?php echo metadata('file', 'Filename'); ?></dd>
        <dt><?php echo __('Original Filename'); ?>:</dt>
        <dd><?php echo metadata('file', 'Original Filename'); ?></dd>
        <dt><?php echo __('File Size'); ?>:</dt>
        <dd><?php echo metadata('file', 'Size'); ?> bytes</dd>
        </dl>
    </div>

    <div id="file-history" class="panel">
        <h4><?php echo __('File History'); ?></h4>
        <dt><?php echo __('Date Added'); ?></dt>
        <dd><?php echo metadata('file', 'Date Added'); ?></dd>
        <dt><?php echo __('Date Modified'); ?></dt> 
        <dd><?php echo metadata('file', 'Date Modified'); ?></dd>
        <dt><?php echo __('Authentication'); ?></dt> 
        <dd><?php echo metadata('file', 'Authentication'); ?></dd>
    </div>

    <div id="type-metadata" class="panel">
        <h4><?php echo __('Type Metadata'); ?></h4>
        <dt><?php echo __('Mime Type / Browser'); ?>:</dt>
        <dd><?php echo metadata('file', 'MIME Type'); ?></dd>
        <dt><?php echo __('Mime Type / OS'); ?>:</dt>
        <dd><?php echo metadata('file', 'MIME Type OS'); ?></dd>
        <dt><?php echo __('File Type / OS'); ?>:</dt>
        <dd><?php echo metadata('file', 'File Type OS'); ?></dd>
    </div>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <?php echo output_format_list(); ?>
    </div>

</div>

<div class="seven columns alpha">

    <?php echo flash(); ?>            
    
    <div id="fullsize-file">
        <?php echo display_file($file, array('imageSize' => 'fullsize')); ?>
    </div>
    <?php echo show_file_metadata(); ?>
    <?php fire_plugin_hook('admin_append_to_files_show_primary', $file); ?>
    
</div>
    
    <?php fire_plugin_hook('admin_append_to_files_show_secondary', $file); ?>
<?php foot();?>
