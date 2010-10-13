if (typeof Omeka === 'undefined') {
    Omeka = {};
}

/**
 * Flash divs with the "alert" class.
 * Called on document.ready.
 */
Omeka.alertBox = function () {
    var alerts = jQuery('div.alert');
    alerts.css('backgroundColor', 'white');
    alerts.css('backgroundColor', '#ff9');
    alerts.animate({backgroundColor: 'white'}, 1000);
};

/**
 * Add the TinyMCE WYSIWG editor to a page.
 * Default is to add to all textareas.
 *
 * @param {Object} [params] Parameters to pass to TinyMCE, these override the
 * defaults.
 */
Omeka.wysiwyg = function (params) {
    // Default parameters
    initParams = {
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

// Flash "alert" divs.
jQuery(window).load(Omeka.alertBox);

jQuery(document).ready(function () {
    // Adds confirm dialog for delete buttons.
    jQuery('.delete-button').click(function () {
        var confirmText = 'Are you sure you want to delete this?';
        var element = jQuery(this);
        if (element.hasClass('delete-element-set')) {
            confirmText = 'Are you sure you want to delete this element set? This will delete the element set and all elements assigned to the element set. Items will lose all metadata that is specific to this element set.';
        } else if (element.hasClass('delete-item')) {
            confirmText = 'Are you sure you want to delete this item? This will delete the item and its associated metadata. It will also delete all files and file metadata associated with this item.';
        } else if (element.hasClass('delete-item-type')) {
            confirmText = 'Are you sure you want to delete this item type? This will delete the item type but will not delete the elements assigned to the item type. Items that are assigned to this item type will lose all metadata that is specific to the item type.';
        } else if (element.hasClass('delete-collection')) {
            confirmText = 'Are you sure you want to delete this collection? This will delete the collection and its associated metadata. This will not delete any items in this collection, but will delete the referece to this collection in each item.';
        } else if (element.hasClass('delete-user')) {
            confirmText = 'Are you sure you want to delete this user? This will delete the user from the system, and will prevent the user from logging in unless a new account is created. Items, collections, and tags created by this user will remain in the archive, but will no longer be associated with this user.';
        }

        return confirm(confirmText);
    });

    // Adds "cancel changes" confirm dialog.
    jQuery('#cancel_changes').click(function () {
        return confirm('Are you sure you want to cancel your changes? Anything you have added will not be saved.');
    });
});
