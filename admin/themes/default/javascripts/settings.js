if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Settings = {};

/**
 * Adds button/div that uses AJAX to check the given ImageMagick path.
 *
 * @param {string} url Url to the checkImageMagick action.
 * @param {string} label Label for the test button.
 */
Omeka.Settings.checkImageMagick = function (url, label) {
    var imageMagickInput = jQuery('#path_to_convert');
    if (!imageMagickInput.length) {
        return;
    }
    var testButton = '<button type="button" id="test-button">' + label + '</button>';
    var resultDiv = '<div id="im-result" />';
    
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
