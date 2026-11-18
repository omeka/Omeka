if (!Omeka) {
    var Omeka = {};
}

Omeka.Users = {};

(function ($) {
    Omeka.Users.detectChanges = function () {
        let changedCheckboxes = $('#user-api-keys').find('[type="checkbox"]:checked');
        let formChanged = ($('#api_key_label').val() !== '') || (changedCheckboxes.length > 0);
        return formChanged;
    };

    Omeka.Users.flashNoChangesError = function () {
        let errorText = $('#user-api-keys').data('no-changes-text');
        console.log(errorText);
        let errorElement;
        if (errorText) {
            errorElement = $('<li class="error" tabindex="-1" id="no-changes">' + errorText + '</li>');
            let flash = $('#flash');
            if (flash.length == 0) {
                flash = $('<div id="flash"><ul></ul></div>');
                $('.content-wrapper').prepend(flash);
            }
            flash.find('ul').append(errorElement);
        } 
        errorElement.focus();
    }

    $(document).ready(function () {
        $('#save').on('click', '[type="submit"]', function(e) {
            if (!Omeka.Users.detectChanges()) {
                e.preventDefault();
                Omeka.Users.flashNoChangesError();
            } 
        })
    });
})(jQuery);
