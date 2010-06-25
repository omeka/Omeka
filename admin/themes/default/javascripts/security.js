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
        new Ajax.Request(ajaxUri, {
            method: 'get',
            parameters: (useDefault ? 'default=true' : ''),
            onComplete: function(t) {
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