<?php
    $fileTitle = metadata('file', 'original filename');
    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('Edit File #%s', metadata('file', 'id')) . $fileTitle;
?>
<?php echo head(array('title' => $fileTitle, 'bodyclass'=>'files edit-file primary')); ?>
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
    </div>
</form>
<?php echo foot(); ?>
