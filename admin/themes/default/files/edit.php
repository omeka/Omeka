<?php
    $fileTitle = strip_formatting(item_file('original filename'));
    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = __('Edit File #%s', item_file('id')) . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files edit-file primary')); ?>
<?php echo js('files'); ?>

<h1><?php echo $fileTitle; ?></h1>

<div id="primary">
<?php if (has_permission('Files', 'delete')): ?>
    <?php echo delete_button(null, 'delete-file', __('Delete this File'), array(), 'delete-record-form'); ?>
<?php endif; ?>
<?php echo flash(); ?>

<div id="edit-file-preview"><?php echo display_file($file, array('imageSize'=>'square_thumbnail')); ?></div>

    
<form method="post" id="editfile" action="<?php echo html_escape(uri('files/edit/'.$file->id)); ?>" name="editFile">

<?php foreach ($elementSets as $elementSet): ?>
<fieldset>
<legend><?php echo __($elementSet->name); ?></legend>    
<?php echo display_element_set_form($file, $elementSet->name); ?>
</fieldset>
<?php endforeach; ?>

<?php fire_plugin_hook('admin_append_to_files_form', $file); ?>

<fieldset>
<input type="submit" name="submit" class="submit" value="<?php echo __('Save Changes'); ?>" id="file_edit" />
</fieldset>

</form>
</div>
<?php foot(); ?>
