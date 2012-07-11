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
 * Make links to files open in a new window.
 */
Omeka.Items.makeFileWindow = function () {
    jQuery('#file-list a').click(function () {
        window.open(this.getAttribute('href'));
        return false;
    });
};

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
 * Add container for tag remove buttons.
 */
Omeka.Items.createMyTagsHeaderAndList = function () {
    if (!jQuery('#my-tags-list').length) {
        var myTags = jQuery('#my-tags');
        myTags.append('<h3>My Tags</h3>');
        myTags.append('<ul id="my-tags-list"/>');
    }
};

/**
 * Add remove/undo buttons for removing a tag.
 *
 * @param {string} tag Tag to add buttons for.
 * @param {string} addImage URL for add button image.
 * @param {string} deleteImage URL for delete button image.
 */
Omeka.Items.addTagElement = function (tag, addImage, deleteImage) {
    Omeka.Items.createMyTagsHeaderAndList();
    var tagLi = jQuery('<li class="tag-delete"/>');

    var undoButton = jQuery('<input type="image" class="undo_remove_tag" />').appendTo(tagLi);
    undoButton.attr('src', addImage).val(tag);
    undoButton.click(function (event) {
        event.preventDefault();
        Omeka.Items.undoRemoveTag(this);
    });

    var deleteButton = jQuery('<input type="image" class="remove_tag" />').appendTo(tagLi);
    deleteButton.attr('src', deleteImage).val(tag);
    deleteButton.click(function (event) {
        event.preventDefault();
        Omeka.Items.removeTag(this);
    });

    tagLi.append(tag);

    jQuery('#my-tags-list').append(tagLi);

    Omeka.Items.updateTagsField();
    return false;
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
    var oldTags = jQuery('#my-tags-list input.remove_tag').map(function () {
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
Omeka.Items.removeTag = function (button) {
    jQuery(button).hide().parent().css('opacity', '.3');
    Omeka.Items.updateTagsField();
};

/**
 * Callback for tag undo buttons.
 *
 * @param {Element} button Clicked button.
 */
Omeka.Items.undoRemoveTag = function (button) {
    jQuery(button).next('input.remove_tag').show().parent().css('opacity', '1');
    Omeka.Items.updateTagsField();
};

/**
 * Update the hidden tags fields to only include the tags that have not been removed.
 */
Omeka.Items.updateTagsField = function () {
    var myTagsToAdd = [];
    var myTagsToDelete = [];
    var otherTagsToDelete = [];

    jQuery('#my-tags-list input.remove_tag').each(function () {
        var button = jQuery(this);
        var tag = jQuery.trim(button.val());
        if (button.parent().css('opacity') == 1) {
            myTagsToAdd.push(tag);
        } else {
            myTagsToDelete.push(tag);
        }
    });
    jQuery('#other-tags-list input.remove_tag').each(function () {
        var button = jQuery(this);
        var tag = jQuery.trim(button.val());
        if (button.parent().css('opacity') != 1) {
            otherTagsToDelete.push(tag);
        }
    });

    jQuery('#my-tags-to-add').val(myTagsToAdd.join(Omeka.Items.tagDelimiter));
    jQuery('#my-tags-to-delete').val(myTagsToDelete.join(Omeka.Items.tagDelimiter));
    jQuery('#other-tags-to-delete').val(otherTagsToDelete.join(Omeka.Items.tagDelimiter));
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

    jQuery('input.remove_tag').click(function (event) {
        event.preventDefault();
        Omeka.Items.removeTag(this);
    });

    jQuery('input.undo_remove_tag').click(function (event) {
        event.preventDefault();
        Omeka.Items.undoRemoveTag(this);
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
 * Send an AJAX request to update a <div class="field"> that contains all
 * the form inputs for an element.
 *
 * @param {jQuery} fieldDiv
 * @param {Object} params Parameters to pass to AJAX URL.
 * @param {string} elementFormPartialUri AJAX URL.
 * @param {string} itemId Current Item ID.
 */
Omeka.Items.elementFormRequest = function (fieldDiv, params, elementFormPartialUri, itemId) {
    var elementId = fieldDiv.attr('id').replace(/element-/, '');
    
    fieldDiv.find('input, textarea, select').each(function () {
        var element = jQuery(this);
        // Workaround for annoying jQuery treatment of checkboxes.
        if (element.is('[type=checkbox]')) {
            params[this.name] = element.is(':checked') ? '1' : '0';
        } else {
            params[this.name] = element.val();
        }
    });
    
    params.element_id = elementId;
    params.item_id = itemId;

    jQuery.ajax({
        url: elementFormPartialUri,
        type: 'POST',
        dataType: 'html',
        data: params,
        success: function (response) {
            fieldDiv.find('textarea').each(function () {
                tinyMCE.execCommand('mceRemoveControl', false, this.id);
            });
            fieldDiv.html(response);
            fieldDiv.trigger('omeka:elementformload');
        }
    });
};

/**
 * Set up add/remove element buttons for ElementText inputs.
 *
 * @param {Element} element The element to search at and below.
 * @param {string} elementFormPartialUrl AJAX URL for form inputs.
 * @param {string} itemId Current Item ID.
 */
Omeka.Items.makeElementControls = function (element, elementFormPartialUrl, itemId) {
    var addSelector = '.add-element';
    var removeSelector = '.remove-element';
    var fieldSelector = 'div.field';
    var inputBlockSelector = 'div.input-block';
    var context = jQuery(element);
    var fields;

    if (context.is(fieldSelector)) {
        fields = context;
    } else {
        fields = context.find(fieldSelector);
    }

    // Show remove buttons for fields with 2 or more inputs.
    fields.each(function () {
        var removeButtons = jQuery(this).find(removeSelector);
        if (removeButtons.length > 1) {
            removeButtons.show();
        }
    });

    // When an add button is clicked, make an AJAX request to add another input.
    context.find(addSelector).click(function (event) {
        event.preventDefault();
        var fieldDiv = jQuery(this).parents(fieldSelector);

        Omeka.Items.elementFormRequest(fieldDiv, {add: '1'}, elementFormPartialUrl, itemId);
    });

    // When a remove button is clicked, remove that input from the form.
    context.find(removeSelector).click(function (event) {
        event.preventDefault();
        var removeButton = jQuery(this);

        // Don't delete the last input block for an element.
        if (removeButton.parents(fieldSelector).find(inputBlockSelector).length === 1) {
            return;
        }

        if (!confirm('Do you want to delete this input?')) {
            return;
        }

        var inputBlock = removeButton.parents(inputBlockSelector);
        inputBlock.find('textarea').each(function () {
            tinyMCE.execCommand('mceRemoveControl', false, this.id);
        });
        inputBlock.remove();

        // Hide remove buttons for fields with one input.
        jQuery(fieldSelector).each(function () {
            var removeButtons = jQuery(this).find(removeSelector);
            if (removeButtons.length === 1) {
                removeButtons.hide();
            }
        });
    });
};

/**
 * Allow adding an arbitrary number of file input elements to the items form so that
 * more than one file can be uploaded at once.
 *
 * @param {string} label
 */
Omeka.Items.enableAddFiles = function (label) {
    var filesDiv = jQuery('#file-inputs .files').first();
    var filesDivWrap = jQuery('#file-inputs');

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
    filesDivWrap.append(link);
};

/**
 * Set up a "Uses HTML" checkbox to enable the WYSIWYG editor.
 *
 * @param {Element} checkbox
 */
Omeka.Items.enableWysiwygCheckbox = function (checkbox) {
    var textarea = jQuery(checkbox).parents('.input-block').find('textarea');
    if (textarea.length) {
        var textareaId = textarea.attr('id');
        var enableIfChecked = function () {
            if (checkbox.checked) {
                tinyMCE.execCommand("mceAddControl", false, textareaId);
            } else {
                tinyMCE.execCommand("mceRemoveControl", false, textareaId);
            }
        };

        enableIfChecked();

        // Whenever the checkbox is toggled, toggle the WYSIWYG editor.
        jQuery(checkbox).click(enableIfChecked);
    }
};

/**
 * Enable the WYSIWYG editor for "html-editor" fields on the form, and allow
 * checkboxes to create editors for more fields.
 *
 * @param {Element} element The element to search at and below.
 */
Omeka.Items.enableWysiwyg = function (element) {
    jQuery(element).find('div.inputs input[type="checkbox"]').each(function () {
        Omeka.Items.enableWysiwygCheckbox(this);
    });
};
