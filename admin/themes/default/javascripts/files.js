Event.observe(window, 'load', function(){
    // Get rid of the add/remove buttons and 'Use HTML' checkbox.
    // This may be added back in future releases.
    
    var buttons = $$('input.add-element, input.remove-element, label.use-html');
    buttons.invoke('remove');
});