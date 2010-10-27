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
        hiddenFileName = jQuery.trim(hiddenFile.attr("value"));
        if (hiddenFileName != "") {
            var fileNameDiv = jQuery(document.createElement('div'));
            fileNameDiv.attr('id', 'x_hidden_file_' + fileInputName);
            fileNameDiv.text(hiddenFileName);

            var changeFileButton = Omeka.Themes.createChangeFileButton(fileInputName);
            fileNameDiv.append(changeFileButton);

            fileInput.after(fileNameDiv);
            fileInput.hide();
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
