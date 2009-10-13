<?php
    $fileTitle = strip_formatting(item_file('original filename'));
    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = 'File #' . item_file('id') . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files show primary-secondary')); ?>

<h1><?php echo $fileTitle; ?></h1>
<?php if (has_permission('Files', 'edit') or $file->getItem()->wasAddedBy(current_user())): ?>
    <p id="edit-file" class="edit-button"><?php echo link_to($file, 'edit', 'Edit this File', array('class'=>'edit')); ?></p>
<?php endif; ?>
<div id="primary">
    <?php echo display_file($file, array('imageSize'=>'fullsize')); ?>
    <?php echo show_file_metadata(); ?>
    <div id="file-history" class="section">
    <h2>File History</h2>
    <h3>Date Added:</h3> 
    <div class="element-text"><?php echo item_file('Date Added'); ?></div>
    <h3>Date Modified:</h3> 
    <div class="element-text"><?php echo item_file('Date Modified'); ?></div>
    <h3>Authentication:</h3> 
    <div class="element-text"><?php echo item_file('Authentication'); ?></div>
    </div>
</div><!--end primary-->
<div id="secondary">
    
    <div id="format-metadata" class="info-panel">
        <h2>Format Metadata</h2>
    <dl>
    <dt>Archive Filename:</dt> <dd><?php echo item_file('Archive Filename'); ?></dd>
    <dt>Original Filename:</dt> <dd><?php echo item_file('Original Filename'); ?></dd>
    <dt>File Size:</dt> <dd><?php echo item_file('Size'); ?> bytes</dd>
    </dl>
    </div>

    <div id="type-metadata" class="info-panel">
    <h2>Type Metadata</h2>
    <dl>
    <dt>Mime Type / Browser:</dt> <dd><?php echo item_file('MIME Type'); ?></dd>
    <dt>Mime Type / OS:</dt> <dd><?php echo item_file('MIME Type OS'); ?></dd>
    <dt>File Type / OS:</dt> <dd><?php echo item_file('File Type OS'); ?></dd>
    </dl>
    </div>

    <div class="info-panel">
        <h2>Output Formats</h2>
        <div>
            <?php echo output_format_list(); ?>
        </div>
    </div>
    
</div>
<?php foot();?>