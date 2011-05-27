if (typeof Omeka === 'undefined') {
    Omeka = {};
}

/**
 * Add the TinyMCE WYSIWYG editor to a page.
 * Default is to add to all textareas.
 *
 * @param {Object} [params] Parameters to pass to TinyMCE, these override the
 * defaults.
 */
Omeka.wysiwyg = function (params) {
    // Default parameters
    initParams = {
        plugins: "paste,inlinepopups",
        convert_urls: false,
        mode: "textareas", // All textareas
        theme: "advanced",
        theme_advanced_toolbar_location: "top",
        force_br_newlines: false,
        forced_root_block: 'p', // Needed for 3.x
        remove_linebreaks: true,
        fix_content_duplication: false,
        fix_list_elements: true,
        valid_child_elements: "ul[li],ol[li]",
        theme_advanced_buttons1: "bold,italic,underline,justifyleft,justifycenter,justifyright,bullist,numlist,link,formatselect,code",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        theme_advanced_toolbar_align: "left"
    };

    // Overwrite default params with user-passed ones.
    for (var attribute in params) {
        // Account for annoying scripts that mess with prototypes.
        if (params.hasOwnProperty(attribute)) {
            initParams[attribute] = params[attribute];
        }
    }

    tinyMCE.init(initParams);
};

jQuery(document).ready(function () {
    // Adds confirm dialog for delete buttons.
    jQuery('.delete-confirm').click(function () {
        if (jQuery(this).is('input')) {
            var url = jQuery(this).parents('form').attr('action');
        } else if (jQuery(this).is('a')) {
            var url = jQuery(this).attr('href');
        }
        jQuery.post(url, function (response){
            jQuery(response).dialog({modal:true});
        });
        return false;
    });
});
