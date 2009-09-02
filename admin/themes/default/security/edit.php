<?php head(array('title'=>'Edit Security Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<script type="text/javascript" charset="utf-8">
Event.observe(window, 'load', function(){
    var loaderGif = new Element('img', {'src': '<?php echo img("loader.gif"); ?>'});
    
    // "default" file extension whitelist button
    var defaultFileExtensionWhitelistButton = new Element('button', {'type': 'button'});
    var fileExtensionWhitelistInput = $('file_extension_whitelist');
    fileExtensionWhitelistInput.insert({'after': defaultFileExtensionWhitelistButton});
    defaultFileExtensionWhitelistButton.update('Restore Default File Extensions').observe('click', function(e){
        defaultFileExtensionWhitelistButton.insert({'after': loaderGif});
        new Ajax.Request('<?php echo uri(array('controller'=>'security','action'=>'get-file-extension-whitelist')); ?>', {
            method: 'get',
            parameters: 'default=true',
            onComplete: function(t) {
                loaderGif.hide();
                fileExtensionWhitelistInput.update(t.responseText);
            }
        });
        // "undo" file extension whitelist button
        if (!defaultFileExtensionWhitelistButton.next('button')) {
            var undoFileExtensionWhitelistButton = new Element('button', {'type': 'button'});
            defaultFileExtensionWhitelistButton.insert({'after': undoFileExtensionWhitelistButton});
            undoFileExtensionWhitelistButton.update('Undo').observe('click', function(e){
                undoFileExtensionWhitelistButton.insert({'after': loaderGif});
                new Ajax.Request('<?php echo uri(array('controller'=>'security','action'=>'get-file-extension-whitelist')); ?>', {
                    method: 'get',
                    onComplete: function(t) {
                        loaderGif.hide();
                        fileExtensionWhitelistInput.update(t.responseText);
                    }
                });
                undoFileExtensionWhitelistButton.remove();
            });
        }
    });
    
    // "default" file mime type whitelist button
    var defaultFileMimeTypeWhitelistButton = new Element('button', {'type': 'button'});
    var fileMimeTypeWhitelistInput = $('file_mime_type_whitelist');
    fileMimeTypeWhitelistInput.insert({'after': defaultFileMimeTypeWhitelistButton});
    defaultFileMimeTypeWhitelistButton.update('Restore Default File Mime Types').observe('click', function(e){
        defaultFileMimeTypeWhitelistButton.insert({'after': loaderGif});
        new Ajax.Request('<?php echo uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist')); ?>', {
            method: 'get',
            parameters: 'default=true',
            onComplete: function(t) {
                loaderGif.hide();
                fileMimeTypeWhitelistInput.update(t.responseText);
            }
        });
        // "undo" file extension whitelist button
        if (!defaultFileMimeTypeWhitelistButton.next('button')) {
            var undoFileMimeTypeWhitelistButton = new Element('button', {'type': 'button'});
            defaultFileMimeTypeWhitelistButton.insert({'after': undoFileMimeTypeWhitelistButton});
            undoFileMimeTypeWhitelistButton.update('Undo').observe('click', function(e){
                undoFileMimeTypeWhitelistButton.insert({'after': loaderGif});
                new Ajax.Request('<?php echo uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist')); ?>', {
                    method: 'get',
                    onComplete: function(t) {
                        loaderGif.hide();
                        fileMimeTypeWhitelistInput.update(t.responseText);
                    }
                });
                undoFileMimeTypeWhitelistButton.remove();
            });
        }
    });
});
</script>
<h1>Edit Security Settings</h1>
<?php common('settings-nav'); ?>

<div id="primary">
<?php echo flash(); ?>

<form method="post" action="" id="security-form">
    <fieldset>
        <div class="field">
            <label for="disable_default_file_validation">Disable File Upload Validation</label>
                <div class="inputs">
            <?php echo $this->formCheckbox('disable_default_file_validation', 
                null, array('checked'=>get_option('disable_default_file_validation'))); ?>
            <p class="explanation">Check this field if you would like to allow any file to be uploaded to Omeka.</p>
            </div>
        </div>
        <div class="field">
            <label for="file_extension_whitelist">Allowed File Extensions</label>
            <div class="inputs">
            <?php echo $this->formTextarea('file_extension_whitelist', 
                    get_option('file_extension_whitelist'), 
                    array('class'=>'textinput', 'cols'=>50, 'rows'=>5)); ?>
            <p class="explanation">List of allowed extensions for file uploads.</p> 
            </div>
        </div>
        
        <div class="field">
            <label for="file_mime_type_whitelist">Allowed File Types</label>
            <div class="inputs">
            <?php echo $this->formTextarea('file_mime_type_whitelist',
                    get_option('file_mime_type_whitelist'),
                    array('class'=>'textinput', 'cols'=>50, 'rows'=>5)); ?>
            <p class="explanation">List of allowed MIME types for file uploads.</p>
            </div>
        </div>
        
    </fieldset>
    
    <fieldset>
        <input type="submit" id="security-submit" name="submit" class="submit submit-medium" value="Save Changes" />
    </fieldset>
</form>
</div>
<?php foot(); ?>