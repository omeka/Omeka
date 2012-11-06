if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Tags = {};

(function ($) {
    /**
     * Enable edit-in-place functionality for tags and update links after renaming tags.
     */
    Omeka.Tags.enableEditInPlace = function (editableURL,tagURLBase) {
        $('.edit-tag').editable(editableURL, {
            callback: function (value, settings) {
                $(this).prev().attr("href", tagURLBase + $(this).text());
            }
        }); 
    };
})(jQuery);
