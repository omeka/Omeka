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
})(jQuery);
