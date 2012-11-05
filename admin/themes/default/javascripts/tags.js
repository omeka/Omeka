if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Tags = {};

/**
 * Enable edit-in-place functionality for tags and update links after renaming tags.
 */

Omeka.Tags.enableEditInPlace = function (editableURL,tagURLBase) {
    jQuery('.edit-tag').editable(editableURL, {
        callback: function (value, settings) {
            jQuery(this).prev().attr("href", tagURLBase + jQuery(this).text());
        }
    }); 
};
