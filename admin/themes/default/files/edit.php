<?php head(array('body_class'=>'items primary')); ?>
<script type="text/javascript" charset="utf-8">
    Event.observe(window, 'load', function(){
        // Get rid of the add/remove buttons and 'Use HTML' checkbox.
        // This may be added back in future releases.
        
        var buttons = $$('input.add-element, input.remove-element, label.use-html');
        buttons.invoke('remove');
    });
</script>

<h1>Edit File #<?php echo htmlentities($file->id); ?></h1>

<div id="primary">

<?php echo flash(); ?>

<?php if (has_permission('Files', 'delete')): ?>
<p><?php echo link_to($file, 'delete', 'Delete', array('class'=>'delete')); ?></p>    
<?php endif; ?>

<?php if($file->hasThumbnail()): ?>
<div id="image"><?php echo thumbnail($file); ?><p>Thumbnail of File #<?php echo htmlentities($file->id); ?></div>
<?php endif; ?>
	
<form method="post" id="editfile" action="<?php echo uri('files/edit/'.$file->id); ?>" name="editFile">

<fieldset>

<legend>Core Metadata</legend>	

<?php echo display_element_set_form($file, 'Dublin Core'); ?>

<fieldset>
<legend>Format (Legacy) Metadata</legend>	

<?php echo display_element_set_form($file, 'Omeka Legacy File'); ?>

</fieldset>

<fieldset>
<input type="submit" name="submit" class="submit submit-medium" value="Save Changes" id="file_edit" />
</fieldset>

</form>

</div>
<?php foot(); ?>