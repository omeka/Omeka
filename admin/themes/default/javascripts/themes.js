if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Themes = {};

/**
 * Create a link/button to allow users to change uploaded files in theme config.
 *
 * @param {string} fileInputName Name of the file input being changed.
 * @returns {jQuery} jQuery object for created button.
 */
Omeka.Themes.createChangeFileButton = function (fileInputName) {
    var button = jQuery(document.createElement('a'));
    button.text('Change');
    button.attr('class', 'submit');
    button.click(function () {
        hiddenFile = jQuery("#hidden_file_" + fileInputName);
        hiddenFile.attr("value", "");

        fileInput = jQuery("#" + fileInputName);
        fileInput.show();
        fileInput.parent().siblings('.explanation').show();

        fileNameDiv = jQuery("#x_hidden_file_" + fileInputName);
        fileNameDiv.hide();

        jQuery(this).hide();
    });
    return button;
};

jQuery(document).ready(function () {
    var files = jQuery("input[type=file]");

    files.each(function (i, val) {
        fileInput = jQuery(val);
        fileInputName = fileInput.attr("name");

        hiddenFile = jQuery("#hidden_file_" + fileInputName);
        hiddenFileUri = jQuery.trim(hiddenFile.attr("value"));
        if (hiddenFileUri != "") {
            var fileNameDiv = jQuery('<div class="theme-file" id="x_hidden_file_' + fileInputName + '"></div>');

            var fileImgLink = jQuery('<a href="' + hiddenFileUri + '"><img src="' + hiddenFileUri + '" /></a>');

            var changeFileButton = Omeka.Themes.createChangeFileButton(fileInputName);
            fileNameDiv.append(changeFileButton);

            fileNameDiv.append(fileImgLink);

            fileInput.after(fileNameDiv);
            fileInput.hide();
            fileInput.parent().siblings('.explanation').hide();
        }
    });
});

jQuery(window).load(function () {
    Omeka.wysiwyg({
        mode: "specific_textareas",
        editor_selector: "html-input",
        forced_root_block: ""
    });
});
