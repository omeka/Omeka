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
     * Add link that collapses and expands content.
     */
    Omeka.ItemTypes.addHideButtons = function () {
        $('.sortable .drawer-contents').each(function () {
            if( $(this).prev().hasClass("sortable-item") ) {
                $(this).hide();
            }
        });
        $('div.sortable-item').each(function () {
            $(this).append('<div class="drawer"></div>');
        });
        $('.drawer').click( function (event) {
                event.preventDefault();
                $(event.target).parent().next().toggle();
                $(this).toggleClass('opened');
            })
            .mousedown(function (event) {
                event.stopPropagation();
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

            $(document).on('click', '.delete-element', function (event) {
                event.preventDefault();
                toggleElements(this);
            });
            $('a.undo-delete').click( function (event) {
                event.preventDefault();
                toggleElements(this);
            });
        }
        
        function toggleElements(button) {
            var elementsToRemove = $('#itemtypes_remove');
            var removeElementLinkPrefix = 'remove-element-link-';
            var removeElementLinkId = button.getAttribute('id');
            if ($(button).hasClass('delete-element')) {
                if (removeElementLinkId !== null) {
                    var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
                    if (elementId) {
                        elementsToRemove.attr('value', elementsToRemove.attr('value') + elementId + ',');
                    }
                    $(button).prevAll('.element-order').attr('name', '');
                    $(button).parent().addClass('deleted');
                    $(button).parent().next().addClass('deleted');
                    $(button).prev().toggle();
                    $(button).toggle();
                } else {
                    var row = $(button).parent().parent();
                    row.remove();
                }
            } else {
                if (removeElementLinkId) {
                    var elementId = removeElementLinkId.substring(removeElementLinkPrefix.length);
                    $(button).prevAll('.element-order').attr('name', 'elements[' + elementId + '][order]');
                }
                $(button).parent().removeClass('deleted');
                $(button).parent().next().removeClass('deleted');
                $(button).next().toggle();
                $(button).toggle();
            }
        }

        $('#add-element').click( function (event) {
            event.preventDefault();
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
                },
                error: function () {
                    alert('Unable to get a new element.');
                }
            });
        });

        activateRemoveElementLinks();
    };
})(jQuery);
