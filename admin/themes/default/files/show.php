<?php
    $fileTitle = metadata('file', array('Dublin Core', 'Title')) ? strip_formatting(metadata('file', array('Dublin Core', 'Title'))) : metadata('file', 'original filename');

    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('File #%s', metadata('file', 'id')) . $fileTitle;
?>
<?php echo head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>


<div id="edit" class="three columns omega">

    <?php if (is_allowed('Files', 'edit') or $file->getItem()->wasAddedBy(current_user())): ?>    
    <div class="panel">
        <?php echo link_to($file, 'edit', __('Edit'), array('class'=>'big green button')); ?>
        <?php if (is_allowed('Files', 'delete')): ?>
            <?php echo link_to($file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
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
        <dl>
        <dt><?php echo __('Date Added'); ?></dt>
        <dd><?php echo metadata('file', 'Date Added'); ?></dd>
        <dt><?php echo __('Date Modified'); ?></dt> 
        <dd><?php echo metadata('file', 'Date Modified'); ?></dd>
        <dt><?php echo __('Authentication'); ?></dt> 
        <dd><?php echo metadata('file', 'Authentication'); ?></dd>
        </dl>
    </div>

    <div id="type-metadata" class="panel">
        <h4><?php echo __('Type Metadata'); ?></h4>
        <dl>
        <dt><?php echo __('Mime Type / Browser'); ?>:</dt>
        <dd><?php echo metadata('file', 'MIME Type'); ?></dd>
        <dt><?php echo __('Mime Type / OS'); ?>:</dt>
        <dd><?php echo metadata('file', 'MIME Type OS'); ?></dd>
        <dt><?php echo __('File Type / OS'); ?>:</dt>
        <dd><?php echo metadata('file', 'File Type OS'); ?></dd>
        </dl>
    </div>

    <?php if (file_id3_metadata()): ?>
    <div id="id3-metadata" class="panel">
        <h4><?php echo __('ID3 Metadata'); ?></h4>
        <?php echo file_id3_metadata(); ?>
    </div>

    <div class="panel">
        <h4><?php echo __('Output Formats'); ?></h4>
        <?php echo output_format_list(); ?>
    </div>
    <?php endif; ?>

</div>

<div class="seven columns alpha">

    <?php echo flash(); ?>            
    
    <?php if (file_marksup($file)): ?>
    <div id="item-images">
        <?php echo file_markup($file, array('imageSize' => 'square_thumbnail'), array('class' => 'admin-thumb panel')); ?>
    </div>
    <?php endif; ?>
    
    <?php echo all_element_texts('file'); ?>
    
    <?php fire_plugin_hook('admin_append_to_files_show_primary', array('file' => $file, 'view' => $this)); ?>
    
</div>
    <?php fire_plugin_hook('admin_append_to_files_show_secondary', array('file' => $file, 'view' => $this)); ?>
<?php echo foot();?>
