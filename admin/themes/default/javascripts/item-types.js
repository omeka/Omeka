if (!Omeka) {
    var Omeka = {};
}

Omeka.ItemTypes = {};

(function ($) {
    /**
     * Enable drag and drop sorting for elements.
     */
    Omeka.ItemTypes.enableSorting = function () {
        $('.sortable').sortable({
            items: 'li.element',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            revert: 200,
            placeholder: 'ui-sortable-highlight',
            containment: 'document',
            update: function (event, ui) {
                $(this).find('.element-order').each(function (index) {
                    $(this).val(index + 1);
                });
            }
        });
    };

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
            $('select.existing-element-drop-down').change(function () {
                var dropDown = $(this);
                var elementId = dropDown.val();
                var addExistingElementIdPrefix = 'add-existing-element-id-';
                var addExistingElementId = this.getAttribute('id');
                if (addExistingElementId) {
                    var elementTempId = addExistingElementId.substring(addExistingElementIdPrefix.length);
                    $.ajax({
                        url: changeExistingElementUrl,
                        dataType: 'json',
                        data: {elementId: elementId, elementTempId: elementTempId},
                        success: function (response) {
                            var elementDescriptionCol = dropDown.parent().next();
                            if(response.elementDescription) {
                                elementDescriptionCol.html('<div class="element-description">' + response.elementDescription + '</div>');
                                elementDescriptionCol.toggle();
                            } else {
                                elementDescriptionCol.hide();
                            }
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

            $(document).on('click', '.delete-drawer, .undo-delete', function () {
                toggleElements(this);
            });
        }
        
        function toggleElements(button) {
            var deleteButton = $(button);
            var elementsToRemove = $('#itemtypes_remove');
            var removeElementLinkPrefix = 'remove-element-link-';
            var removeElementLinkId = deleteButton.attr('id');
            var element = deleteButton.parents('.element');
            if (deleteButton.hasClass('delete-drawer')) {
                if (removeElementLinkId !== undefined) {
                    var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
                    if (elementId) {
                        elementsToRemove.attr('value', elementsToRemove.attr('value') + elementId + ',');
                    }
                    element.find('.element-order').attr('name', '');
                    element.find('select,input').attr('disabled', 'true');
                }
            } else {
                if (removeElementLinkId) {
                    var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
                    element.find('.element-order').attr('name', 'elements[' + elementId + '][order]');
                }
                element.find('select,input').removeAttr('disabled');
            }
        }

        $('#add-element').click( function () {
            $('#add-element-success').hide();
            var elementCount = $('#item-type-elements li').length;
            var typeValue = $('input[name=add-element-type]:checked').val();
            var requestUrl;
            if (typeValue === 'new') {
                requestUrl = addNewRequestUrl;
            } else {
                requestUrl = addExistingRequestUrl;
            }
            $.ajax({
                url: requestUrl,
                dataType: 'text',
                data: {elementCount: elementCount},
                success: function (responseText) {
                    var response = responseText || 'no response text';
                    $('.add-new').parent().before(response);
                    var totalElements = $('#item-type-elements .element').length;
                    $('#add-element-success .element-count').text(totalElements);
                    $('#add-element-success').show();
                },
                error: function () {
                    alert('Unable to get a new element.');
                }
            });
        });

        activateRemoveElementLinks();
    };
})(jQuery);
