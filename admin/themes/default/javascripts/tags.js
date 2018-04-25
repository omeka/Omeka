if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Tags = {};

(function ($) {
    /**
     * Enable edit-in-place functionality for tags and update links after renaming tags.
     */
    $.fn.poshytip = { defaults: null };

    Omeka.Tags.enableEditInPlace = function (editableURL,tagURLBase, csrfToken) {
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.showbuttons = false;
        $('.edit-tag').editable({
            url: editableURL,
            params: function(params) {
                params.csrf_token = csrfToken;
                return params;
            },
            success: function (value, settings) {
                $(this).parents('li').find('a.count').first().attr("href", tagURLBase + value);
            },
        });
    };
})(jQuery);
