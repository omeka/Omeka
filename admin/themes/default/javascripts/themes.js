if (!Omeka) {
    var Omeka = {};
}

Omeka.Themes = {};

(function ($) {
    $(document).ready(function () {
        var files = $("input[type=file]");

        $(document).on('click', '.theme-file button', function() {
            var fileInput = $(this).parent().prev('input[type="file"]');
            var fileInputName = fileInput.attr('name');
            var hiddenFile = $("#hidden_file_" + fileInputName);
            hiddenFile.attr("value", "");

            fileInput.show().focus();
            fileInput.siblings('.explanation').show();

            fileNameDiv = $("#x_hidden_file_" + fileInputName);
            fileNameDiv.hide();
        });

        files.each(function (i, val) {
            var fileInput = $(val);
            var fileInputName = fileInput.attr("name");

            var hiddenFile = $("#hidden_file_" + fileInputName);
            var hiddenFileUri = $.trim(hiddenFile.attr("value"));
            if (hiddenFileUri !== "") {
                var uploadedFileInput = $('.theme-file.template').clone();
                uploadedFileInput.attr('id', 'x_hidden_file_' + fileInputName);
                uploadedFileInput.find('a').attr('href', hiddenFileUri);
                uploadedFileInput.find('img').attr('src', hiddenFileUri);
                fileInput.after(uploadedFileInput);
                uploadedFileInput.removeClass('template');
                fileInput.hide();
                fileInput.siblings('.explanation').hide();
            }
        });

        Omeka.wysiwyg({
            selector: ".html-input",
            forced_root_block: false
        });
    });
})(jQuery);
