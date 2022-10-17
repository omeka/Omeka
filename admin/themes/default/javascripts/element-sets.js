if (!Omeka) {
    var Omeka = {};
}

Omeka.ElementSets = {};

(function ($) {
    /**
     * Enable drag and drop sorting for elements.
     */
    Omeka.ElementSets.enableSorting = function () {
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
    Omeka.ElementSets.addHideButtons = function () {
        $('.sortable .drawer-contents').each(function () {
            $(this).hide();
        });
        $('.drawer-toggle')
            .click(function (event) {
                event.preventDefault();
                $(event.target).parents('.element').find('.drawer-contents').toggle();
                $(this).toggleClass('opened');
                Omeka.toggleAriaExpanded($(this));
            })
            .mousedown(function (event) {
                event.stopPropagation();
            });
    };

    /**
     * Set up tag remove/undo buttons for each element.
     *
     */
    Omeka.ElementSets.enableElementRemoval = function () {
        $(document).on('click', '.delete-element', function (event) {
            event.preventDefault();
            Omeka.ElementSets.toggleElement(this);
        });

        $(document).on('click', '.undo-delete', function (event) {
            event.preventDefault();
            Omeka.ElementSets.toggleElement(this);
        });
    };


    /**
     * Callback for element delete and undoing delete actions.
     *
     * @param {Element} button Clicked button.
     */
    Omeka.ElementSets.toggleElement = function (button) {
        var buttonElement = $(button).parents('.element');
        var elementDeleteHidden = buttonElement.find('.element-delete-hidden');
        buttonElement.find('.drawer, .drawer-contents').toggleClass('deleted');
        if(elementDeleteHidden.val() != 1) {
            elementDeleteHidden.val(1);
        } else {
            elementDeleteHidden.val(0);
        }
    };
    
    /**
     * Callback for confirming a delete element.
     */
    Omeka.ElementSets.confirmDeleteElement = function (okText, cancelText) {
        $("#confirm-delete-dialog").dialog({autoOpen: false, modal: true, buttons: [
            {text: okText, class: 'small green button', click: function () {
                $('#edit-item-type-elements').off('submit').submit();
            }}, 
            {text: cancelText, class: 'small red button', title: '', click: function () {
                $(this).dialog('close')
            }}
        ]});
        $('#edit-item-type-elements').on('submit', function (event) {
            if ($('.undo-delete:visible')[0]) {
                event.preventDefault();
                $('#confirm-delete-dialog').dialog('open');
            }
        });
    };

})(jQuery);
