Event.observe(window,'load',function() {
    if(!$('cancel_changes')) return;
    $('cancel_changes').onclick = function() {
        return confirm( 'Are you sure you want to cancel your changes? Anything you added will not be saved!' );
    };
});