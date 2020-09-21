if (!Omeka) {
    var Omeka = {};
}

Omeka.Settings = {};

(function ($) {
    /**
     * Adds button/div that uses AJAX to check the given ImageMagick path.
     *
     * @param {string} url Url to the checkImageMagick action.
     * @param {string} label Label for the test button.
     */
    Omeka.Settings.checkImageMagick = function (url, label) {
        var imageMagickInput = $('#path_to_convert');
        if (!imageMagickInput.length) {
            return;
        }
        var testButton = '<button type="button" id="test-button">' + label + '</button>';
        var resultDiv = '<div id="im-result" />';
        
        imageMagickInput.after(resultDiv);
        imageMagickInput.after(testButton);
        $('#test-button').click(function () {
            $.ajax({
                url: url,
                dataType: 'html',
                data: {
                    'path-to-convert': imageMagickInput.val()
                },
                success: function (data) {
                    $('#im-result').html(data);
                }
            });
        });
    };
})(jQuery);
