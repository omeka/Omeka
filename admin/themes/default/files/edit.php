<?php
    $fileTitle = metadata('file', 'original filename');
    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('Edit File #%s', metadata('file', 'id')) . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files edit-file primary')); ?>
<form method="post" id="editfile" action="<?php echo html_escape(url('files/edit/' . $file->id)); ?>" name="editFile">
    <?php include 'form.php'; ?>
    <div id="save" class="three columns omega">
        <div class="panel">
            <fieldset>
                <input type="submit" name="submit" class="submit big green button" value="<?php echo __('Save Changes'); ?>" id="file_edit" />
            </fieldset>    
            <?php if (has_permission('Files', 'delete')): ?>
                <?php echo link_to($file, 'delete-confirm', __('Delete'), array('class' => 'big red button delete-confirm')); ?>
            <?php endif; ?>
        </div>
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
    </div>
</form>
<?php foot(); ?>
