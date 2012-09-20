if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Elements = {};

/**
 * Send an AJAX request to update a <div class="field"> that contains all
 * the form inputs for an element.
 *
 * @param {jQuery} fieldDiv
 * @param {Object} params Parameters to pass to AJAX URL.
 * @param {string} elementFormPartialUri AJAX URL.
 * @param {string} recordType Current record type.
 * @param {string} recordId Current record ID.
 */
Omeka.Elements.elementFormRequest = function (fieldDiv, params, elementFormPartialUri, recordType, recordId) {
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
    
    recordId = typeof recordId !== 'undefined' ? recordId : 0;
    
    params.element_id = elementId;
    params.record_id = recordId;
    params.record_type = recordType;

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
 * @param {string} recordType Current record type.
 * @param {string} recordId Current record ID.
 */
Omeka.Elements.makeElementControls = function (element, elementFormPartialUrl, recordType, recordId) {
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
        } else {
          removeButtons.hide();
        }
    });

    // When an add button is clicked, make an AJAX request to add another input.
    context.find(addSelector).click(function (event) {
        event.preventDefault();
        var fieldDiv = jQuery(this).parents(fieldSelector);

        Omeka.Elements.elementFormRequest(fieldDiv, {add: '1'}, elementFormPartialUrl, recordType, recordId);
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
 * Set up a "Uses HTML" checkbox to enable the WYSIWYG editor.
 *
 * @param {Element} checkbox
 */
Omeka.Elements.enableWysiwygCheckbox = function (checkbox) {
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
Omeka.Elements.enableWysiwyg = function (element) {
    jQuery(element).find('div.inputs input[type="checkbox"]').each(function () {
        Omeka.Elements.enableWysiwygCheckbox(this);
    });
};