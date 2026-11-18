if (!Omeka) {
    var Omeka = {};
}

Omeka.Users = {};

(function ($) {
    Omeka.Users.detectChanges = function () {
        let changedCheckboxes = $('#user-api-keys').find('[type="checkbox"]:checked');
        return ($('#api_key_label').val() !== '') || (changedCheckboxes.length > 0);
    };

    Omeka.Users.flashNoChangesError = function () {
        let errorElement = $('#no-changes');
        if (errorElement.length == 0) {
            errorElement = $('<li class="error" tabindex="-1" id="no-changes">' + $('#user-api-keys').data('no-changes-text') + '</li>');
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
        $('#user-api-keys').on('submit', function(e) {
            if (!Omeka.Users.detectChanges()) {
                e.preventDefault();
                Omeka.Users.flashNoChangesError();
            } 
        })
    });
})(jQuery);
