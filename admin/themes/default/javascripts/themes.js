if (!Omeka) {
    var Omeka = {};
}

Omeka.Themes = {};

(function ($) {
    /**
     * Create a link/button to allow users to change uploaded files in theme config.
     *
     * @param {string} fileInputName Name of the file input being changed.
     * @returns {jQuery} jQuery object for created button.
     */
    Omeka.Themes.createChangeFileButton = function (fileInputName) {
        var button = $(document.createElement('a'));
        button.text('Change');
        button.attr('class', 'green button');
        button.click(function () {
            hiddenFile = $("#hidden_file_" + fileInputName);
            hiddenFile.attr("value", "");

            fileInput = $("#" + fileInputName);
            fileInput.show();
            fileInput.parent().siblings('.explanation').show();

            fileNameDiv = $("#x_hidden_file_" + fileInputName);
            fileNameDiv.hide();

            $(this).hide();
        });
        return button;
    };

    $(document).ready(function () {
        var files = $("input[type=file]");

        files.each(function (i, val) {
            fileInput = $(val);
            fileInputName = fileInput.attr("name");

            hiddenFile = $("#hidden_file_" + fileInputName);
            hiddenFileUri = $.trim(hiddenFile.attr("value"));
            if (hiddenFileUri !== "") {
                var fileNameDiv = $('<div class="theme-file" id="x_hidden_file_' + fileInputName + '"></div>');

                var fileImgLink = $('<a href="' + hiddenFileUri + '"><img src="' + hiddenFileUri + '" /></a>');

                var changeFileButton = Omeka.Themes.createChangeFileButton(fileInputName);
                fileNameDiv.append(changeFileButton);

                fileNameDiv.append(fileImgLink);

                fileInput.after(fileNameDiv);
                fileInput.hide();
                fileInput.parent().siblings('.explanation').hide();
            }
        });
    });

    $(window).load(function () {
        Omeka.wysiwyg({
            mode: "specific_textareas",
            editor_selector: "html-input",
            forced_root_block: ""
        });
    });
})(jQuery);
