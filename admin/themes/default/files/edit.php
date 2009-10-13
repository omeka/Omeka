<?php
    $fileTitle = strip_formatting(item_file('original filename'));
    if ($fileTitle != '') {
        $fileTitle = ': &quot;' . $fileTitle . '&quot; ';
    } else {
        $fileTitle = '';
    }
    $fileTitle = 'Edit File #' . item_file('id') . $fileTitle;
?>
<?php head(array('title' => $fileTitle, 'bodyclass'=>'files edit-file primary')); ?>
<script type="text/javascript" charset="utf-8">
    Event.observe(window, 'load', function(){
        // Get rid of the add/remove buttons and 'Use HTML' checkbox.
        // This may be added back in future releases.
        
        var buttons = $$('input.add-element, input.remove-element, label.use-html');
        buttons.invoke('remove');
    });
</script>

<h1><?php echo $fileTitle; ?></h1>

<div id="primary">

<?php echo flash(); ?>

<div id="edit-file-preview"><?php echo display_file($file, array('imageSize'=>'square_thumbnail')); ?></div>

    
<form method="post" id="editfile" action="<?php echo html_escape(uri('files/edit/'.$file->id)); ?>" name="editFile">

<fieldset>

<legend>Dublin Core</legend>    

<?php echo display_element_set_form($file, 'Dublin Core'); ?>

<fieldset>
<legend>Format (Legacy) Metadata</legend>   

<?php echo display_element_set_form($file, 'Omeka Legacy File'); ?>

</fieldset>

<?php fire_plugin_hook('admin_append_to_files_form', $file); ?>

<fieldset>
<input type="submit" name="submit" class="submit submit-medium" value="Save Changes" id="file_edit" />
</fieldset>

</form>
<?php if (has_permission('Files', 'delete')): ?>
<p id="delete-file-link"><?php echo link_to($file, 'delete', 'Delete this File', array('class'=>'delete')); ?></p>    
<?php endif; ?>
</div>
<?php foot(); ?>