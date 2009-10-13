<?php head(array('title'=>'Edit Security Settings', 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
Event.observe(window, 'load', function(){
    var loaderGif = new Element('img', {'src': <?php echo js_escape(img("loader.gif")); ?>});
     
    var buildRestoreButton = function(whitelistInput, ajaxUri, buttonText) {
        // Insert a button after any given element.
        var buttonAfter = function(element, text) {
            button = new Element('button', {'type': 'button'});
            button.update(text);
            element.insert({'after': button});
            return button;
        }
        
        // Make an AJAX request to restore the form input value.                
        var restore = function(clickedButton, useDefault) {
            clickedButton.insert({'after': loaderGif});
            new Ajax.Request(ajaxUri, {
                method: 'get',
                parameters: (useDefault ? 'default=true' : ''),
                onComplete: function(t) {
                    loaderGif.hide();
                    whitelistInput.update(t.responseText);
                }
            });
        }
        var restoreButton = buttonAfter(whitelistInput, buttonText);
        restoreButton.observe('click', function(e){
            restore(restoreButton, true);
            // "undo" file extension whitelist button
            if (!restoreButton.next('button')) {
                var undoButton = buttonAfter(restoreButton, 'Undo');
                undoButton.observe('click', function(e){
                    restore(undoButton, false);
                    undoButton.remove();
                });
            }
        });
    }
    
    buildRestoreButton($('file_extension_whitelist'), 
                         <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-extension-whitelist'))); ?>,
                         'Restore Default File Extensions');
    
    buildRestoreButton($('file_mime_type_whitelist'), 
                      <?php echo js_escape(uri(array('controller'=>'security','action'=>'get-file-mime-type-whitelist'))); ?>,
                      'Restore Default File Mime Types');
});
//]]>
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