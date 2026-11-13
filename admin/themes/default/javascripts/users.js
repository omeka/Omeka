if (!Omeka) {
    var Omeka = {};
}

Omeka.Users = {};

(function ($) {
    Omeka.Users.detectChanges = function () {
        let changedCheckboxes = $('#user-api-keys').find('[type="checkbox"]:checked');
        if (($('#api_key_label').val() !== '') || (changedCheckboxes.length > 0)) {
            return true;
        } else {
            return false;
        }
    };

    Omeka.Users.flashNoChangesError = function () {
        let error = $('#no-changes');
        if (error.length == 0) {
            const errorText = $('#no-changes-text').text();
            error = $('<li class="error" tabindex="-1" id="no-changes">' + errorText + '</li>');
            let flash = $('#flash');
            if (flash.length == 0) {
                flash = $('<div id="flash"><ul></ul></div>');
                $('.content-wrapper').prepend(flash);
            }
            flash.find('ul').append(error);
        } 
        error.focus();
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
