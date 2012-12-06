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
            placeholder: 'ui-sortable-highlight',
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
        $('div.sortable-item').each(function () {
            $(this).append('<div class="drawer"></div>');
        });
        $('.drawer')
            .click(function (event) {
                event.preventDefault();
                $(event.target).parent().next().toggle();
                $(this).toggleClass('opened');
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
        $(button).parent().toggleClass('deleted');
        $(button).parent().next().toggleClass('deleted');
        $(button).toggle();
        if($(button).nextAll('input[type=hidden]').val() != 1) {
            $(button).next().val(1);
            $(button).prev().toggle();
        } else {
            $(button).nextAll('input[type=hidden]').val(0);
            $(button).next().toggle();
        }
    };
    
    /**
     * Callback for confirming a delete element.
     */
    Omeka.ElementSets.confirmDeleteElement = function () {
        $(document).on('click', '#submit_edit_item_type', function (event) {
            event.preventDefault();
            if ($('.undo-delete:visible')[0]) {
                $( "#dialog" ).dialog('open');
            } else {
                $('#edit-item-type-elements').submit();
            }
        });
    };

})(jQuery);
