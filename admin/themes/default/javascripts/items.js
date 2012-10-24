if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Items = {};

/**
 * Set up JS hide/show tabs for the edit page/
 */
Omeka.Items.initializeTabs = function () {
    var tabLinks = jQuery('#section-nav > li > a');
    var tabIds = tabLinks.map(function () {
        // Rely on the fact that the links have pound signs.
        // Workaround IE7's creation of absolute URLs.
        return '#' + this.getAttribute('href').split('#')[1];
    }).toArray().join(',');
    var tabs = jQuery(tabIds);

    function selectTab(tabLink) {
        tabLinks.removeClass('active');
        tabs.hide();

        tabLink.addClass('active');
        jQuery(tabLink.attr('href')).show();
        tabLink.trigger('omeka:tabselected');
    }

    tabLinks.click(function (event) {
        event.preventDefault();
        selectTab(jQuery(this));
    });

    // Select the tab given in the anchor, if any, or the first tab.
    var selectedTab;
    var url = document.location.toString();
    if (url.match('#')) {
        var anchor = '#' + url.split('#')[1];
        selectedTab = tabLinks.filter('[href=' + anchor + ']');
    }
    if (!selectedTab || !selectedTab.length) {
        selectedTab = tabLinks.first();
    }

    selectTab(selectedTab);
};

/**
 * Enable drag and drop sorting for files.
 */

Omeka.Items.enableSorting = function () {
    jQuery( ".sortable" ).sortable({
        'items': 'li.file',
        'forcePlaceholderSize': true, 
        'forceHelperSize': true,
        'placeholder': "ui-sortable-highlight",
        'update': function (event, ui) {
            jQuery(this).find('.file-order').each(function (index) {
                jQuery(this).val(index + 1);
            });
        }
    });
    jQuery( ".sortable" ).disableSelection();
    
    jQuery( ".sortable input[type=checkbox]" ).each( function() {
        jQuery(this).css("display", "none");
    });
}

/**
 * Make links to files open in a new window.
 */
 
Omeka.Items.makeFileWindow = function () {
    jQuery('#file-list a').click(function (event) {
        event.preventDefault();
        if( jQuery(this).hasClass("delete") == true ) {
            Omeka.Items.enableFileDeletion(jQuery(this));
        } else {
            window.open(this.getAttribute('href'));
        }
    });
}

/**
 * Set up toggle for marking files for deletion. 
 */

Omeka.Items.enableFileDeletion = function (deleteLink) {
    if( !deleteLink.next().is(":checked") ) {
        deleteLink.text("Undo").next().prop('checked', true).parents('.sortable-item').addClass("deleted");
    } else {
        deleteLink.text("Delete").next().prop('checked', false).parents('.sortable-item').removeClass("deleted");
    }
}

/**
 * Make the item type selector AJAX in the right item type form.
 *
 * @param {string} changeItemTypeUrl URL for getting form.
 * @param {string} itemId Item ID.
 */
Omeka.Items.changeItemType = function (changeItemTypeUrl, itemId) {
    jQuery('#change_type').hide();
    jQuery('#item-type').change(function () {
        var params = {
            type_id: jQuery(this).val()
        };
        if (itemId) {
            params.item_id = itemId;
        }
        jQuery.ajax({
            url: changeItemTypeUrl,
            type: 'POST',
            dataType: 'html',
            data: params,
            success: function (response) {
                var form = jQuery('#type-metadata-form');
                form.hide();
                form.find('textarea').each(function () {
                    tinyMCE.execCommand('mceRemoveControl', true, this.id);
                });
                form.html(response);
                form.trigger('omeka:elementformload');
                form.slideDown(1000, function () {
                    // Explicit show() call fixes IE7
                    jQuery(this).show();
                });
            }
        });
    });
};


/**
 * Add tag elements for new tags from the input box.
 *
 * @param {string} tags Comma-separated tags to be added.
 * @param {string} addImage URL for add button image.
 * @param {string} deleteImage URL for delete button image.
 */
Omeka.Items.addTags = function (tags, addImage, deleteImage) {
    var newTags = tags.split(Omeka.Items.tagDelimiter);

    // only add tags from the input box that are new
    var oldTags = jQuery('#all-tags-list input.remove-tag').map(function () {
        return jQuery.trim(this.value);
    });

    jQuery.each(newTags, function () {
        var tag = jQuery.trim(this);
        if (tag && jQuery.inArray(tag, oldTags) === -1) {
            Omeka.Items.addTagElement(tag, addImage, deleteImage);
        }
    });

    jQuery('#tags').val('');
};

/**
 * Callback for tag remove buttons.
 *
 * @param {Element} button Clicked button.
 */
Omeka.Items.toggleTag = function (button) {
    jQuery(button).parent().toggleClass('tag-removed');
    Omeka.Items.updateTagsField();
};

/**
 * Update the hidden tags fields to only include the tags that have not been removed.
 */
Omeka.Items.updateTagsField = function () {
    var tagsToAdd = [];
    var tagsToDelete = [];

    jQuery('#all-tags-list input.remove-tag').each(function () {
        var button = jQuery(this);
        var tag = jQuery.trim(button.val());
        if (button.parent().hasClass('tag-removed')) {
            tagsToDelete.push(tag);
        } else {
            tagsToAdd.push(tag);
        }
    });

    jQuery('#tags-to-add').val(tagsToAdd.join(Omeka.Items.tagDelimiter));
    jQuery('#tags-to-delete').val(tagsToDelete.join(Omeka.Items.tagDelimiter));
};

/**
 * Set up tag remove/undo buttons and adding from tags field.
 *
 * @param {string} addImage URL for add button image.
 * @param {string} deleteImage URL for delete button image.
 */
Omeka.Items.enableTagRemoval = function (addImage, deleteImage) {
    jQuery('#add-tags-button').click(function (event) {
        event.preventDefault();
        Omeka.Items.addTags(jQuery('#tags').val(), addImage, deleteImage);
    });

    jQuery('span.remove-tag').click(function (event) {
        event.preventDefault();
        Omeka.Items.toggleTag(this);
    });

    jQuery('span.undo-remove-tag').click(function (event) {
        event.preventDefault();
        Omeka.Items.toggleTag(this);
    });

    Omeka.Items.updateTagsField();
};

/**
 * Set up autocomplete for tags field.
 *
 * @param {string} inputSelector Selector for input to autocomplete on.
 * @param {string} tagChoicesUrl Autocomplete JSON URL.
 */
Omeka.Items.tagChoices = function (inputSelector, tagChoicesUrl) {
    function split(val) {
        var escapedTagDelimiter = Omeka.Items.tagDelimiter.replace(/([.?*+^$[\]\\(){}-])/g, "\\$1");
        var re = new RegExp(escapedTagDelimiter + '\\s*');
        return val.split(re);
    }
    function extractLast(term) {
        return split(term).pop();
    }

    // Tokenized input based on
    // http://jqueryui.com/demos/autocomplete/multiple.html
    jQuery(inputSelector).autocomplete({
        source: function (request, response) {
            jQuery.getJSON(tagChoicesUrl, {
                term: extractLast(request.term)
            }, function (data) {
                response(data);
            });
        },
        focus: function () {
            return false;
        },
        select: function (event, ui) {
            var terms = split(this.value);
            // remove the current input
            terms.pop();
            // add the selected item
            terms.push(ui.item.value);
            // add placeholder to get the comma-and-space at the end
            terms.push('');
            this.value = terms.join(Omeka.Items.tagDelimiter + ' ');
            return false;
        }
    });
};

/**
 * Submit tag changes on items/show with AJAX.
 */
Omeka.Items.modifyTagsShow = function () {
    //Add the tags with this request
    jQuery('#tags-form').submit(function (event) {
        event.preventDefault();
        var form = jQuery(this);
        jQuery.post(form.attr('action'), form.serialize(), function (response) {
            jQuery('#tag-cloud').hide().html(response).fadeIn(1000);
        }, 'html');
    });
};

/**
 * Allow adding an arbitrary number of file input elements to the items form so that
 * more than one file can be uploaded at once.
 *
 * @param {string} label
 */
Omeka.Items.enableAddFiles = function (label) {
    var filesDiv = jQuery('#files-metadata .files.inputs');

    var link = jQuery('<a href="#" id="add-file" class="add-file">' + label + '</a>');
    link.click(function (event) {
        event.preventDefault();
        var inputs = filesDiv.find('input');
        var inputCount = inputs.length;
        var fileHtml = '<div id="fileinput' + inputCount + '" class="fileinput"><input name="file[' + inputCount + ']" id="file[' + inputCount + ']" type="file" class="fileinput" /></div>';
        jQuery(fileHtml).insertAfter(inputs.last()).hide().slideDown(200, function () {
            // Extra show fixes IE bug.
            jQuery(this).show();
        });
    });

    jQuery('#add-more-files').html('');
    jQuery('#file-inputs').append(link);
};
