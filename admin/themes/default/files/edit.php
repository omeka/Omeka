<?php head(array('title' => 'Edit File # '.item_file('id'), 'bodyclass'=>'files edit-file primary')); ?>
<script type="text/javascript" charset="utf-8">
    Event.observe(window, 'load', function(){
        // Get rid of the add/remove buttons and 'Use HTML' checkbox.
        // This may be added back in future releases.
        
        var buttons = $$('input.add-element, input.remove-element, label.use-html');
        buttons.invoke('remove');
    });
</script>

<h1<?php if($file->hasThumbnail()) echo ' class="has-thumbnail"'; ?>
>Edit File #<?php echo item_file('Id'); ?></h1>

<?php if($file->hasThumbnail()): ?>
<div id="edit-file-image"><?php echo square_thumbnail($file); ?></div>
<?php endif; ?>
<div id="primary">

<?php echo flash(); ?>




	
<form method="post" id="editfile" action="<?php echo uri('files/edit/'.$file->id); ?>" name="editFile">

<fieldset>

<legend>Core Metadata</legend>	

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