if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Settings = {};

/**
 * Adds button/div that uses AJAX to check the given ImageMagick path.
 *
 * @param {string} url Url to the checkImageMagick action.
 */
Omeka.Settings.checkImageMagick = function (url) {
    if (!$('path_to_convert')) {
        return;
    }
    var testButton = '<button type="button" id="test-button">Test</button>';
    var resultDiv = '<div id="im-result" />';
    var imageMagickInput = jQuery('#path_to_convert');
    imageMagickInput.after(resultDiv);
    imageMagickInput.after(testButton);
    jQuery('#test-button').click(function () {
        jQuery.ajax({
            url: url,
            dataType: 'html',
            data: {
                'path-to-convert': imageMagickInput.val()
            },
            success: function (data) {
                jQuery('#im-result').html(data);
            }
        });
    });
};
