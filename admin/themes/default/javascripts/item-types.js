if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.ItemTypes = {};

/**
 * Add AJAX-enabled buttons to item type form for adding and removing elements.
 *
 * @param {string} addNewRequestUrl
 * @param {string} addExistingRequestUrl
 * @param {string} changeExistingElementUrl
 */
Omeka.ItemTypes.manageItemTypes = function (addNewRequestUrl, addExistingRequestUrl, changeExistingElementUrl) {
    /**
     * Activate dropdown for selecting from existing elements.
     */
    function activateSelectElementDropdowns() {
        jQuery('select.existing-element-drop-down').change(function () {
            var dropDown = jQuery(this);
            var elementId = dropDown.val();
            var addExistingElementIdPrefix = 'add-existing-element-id-';
            var addExistingElementId = this.getAttribute('id');
            if (addExistingElementId) {
                var elementTempId = addExistingElementId.substring(addExistingElementIdPrefix.length);
                jQuery.ajax({
                    url: changeExistingElementUrl,
                    dataType: 'json',
                    data: {elementId: elementId, elementTempId: elementTempId},
                    success: function (response) {
                        var elementDescriptionCol = dropDown.parent().next();
                        elementDescriptionCol.html(response.elementDescription);
                        var elementDataTypeNameCol = elementDescriptionCol.next();
                        elementDataTypeNameCol.html(response.elementDataTypeName);
                    },
                    error: function () {
                        alert('Unable to get selected element data.');
                    }
                });
            }
        });
    }
    
    /**
     * Turn all the links into AJAX requests that will mark the element for deletion and update the list.
     */
    function activateRemoveElementLinks() {
        jQuery('a.delete-element').click(function (event) {
            event.preventDefault();
            var elementsToRemove = jQuery('#elements-to-remove');

            var removeElementLinkPrefix = 'remove-element-link-';
            var removeElementLinkId = this.getAttribute('id');
            if (removeElementLinkId) {
                var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
                if (elementId) {
                    if (!confirm('Are you sure you want to delete this element? This will remove the element from this particular item type. Items that are assigned to this item type will lose metadata that is specific to this element.')) {
                        return;
                    }
                    elementsToRemove.attr('value', elementsToRemove.attr('value') + elementId + ',');
                }
            }
            var row = jQuery(this).parent().parent();
            row.remove();
        });
    }

    jQuery('#add-element').click(function (event) {
        event.preventDefault();
        var elementCount = jQuery('#element-list-tbody tr').length;
        var typeValue = jQuery('input[name=add-element-type]:checked').val();
        var requestUrl;
        if (typeValue === 'new') {
            requestUrl = addNewRequestUrl;
        } else {
            requestUrl = addExistingRequestUrl;
        }
        jQuery.ajax({
            url: requestUrl,
            dataType: 'text',
            data: {elementCount: elementCount},
            success: function (responseText) {
                var response = responseText || 'no response text';
                var list = jQuery('#element-list-tbody');
                list.append(response);
                activateRemoveElementLinks();
                activateSelectElementDropdowns();
            },
            error: function () {
                alert('Unable to get a new element.');
            }
        });
    });

    activateRemoveElementLinks();
};
