if (!Omeka) {
    var Omeka = {};
}

Omeka.ElementSets = {
    /**
     * Enable drag and drop sorting for elements.
     */
    enableSorting: function () {
        jQuery('.sortable').sortable({
            items: 'li.element',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            placeholder: 'ui-sortable-highlight',
            update: function (event, ui) {
                jQuery(this).find('.element-order').each(function (index) {
                    jQuery(this).val(index + 1);
                });
            }
        });
    },

    /**
     * Add link that collapses and expands content.
     */
    addHideButtons: function () {
        jQuery('.sortable .drawer-contents').each( function() {
            jQuery(this).hide();
        });
        jQuery('div.sortable-item').each(function() {
            jQuery(this).append('<div class="drawer"></div>');
        });
        jQuery('.drawer')
            .click(function(event) {
                event.preventDefault();
                jQuery(event.target).parent().next().toggle();
                jQuery(this).toggleClass('opened');
            })
            .mousedown(function(event) {
                event.stopPropagation();
            });
    }
};
