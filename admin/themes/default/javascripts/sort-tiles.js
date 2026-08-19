if (!Omeka) {
    var Omeka = {};
}

Omeka.SortTiles = {};

(function ($) {
    // Enable drag and drop reordering of sort tiles. The trailing add-row
    // (li.add-tile-row, no "element" class) is excluded, so it stays pinned
    // at the bottom of the list.
    Omeka.SortTiles.enableSorting = function () {
        $('.sort-tiles').sortable({
            items: 'li.element',
            forcePlaceholderSize: true,
            forceHelperSize: true,
            revert: 200,
            placeholder: 'ui-sortable-highlight',
            containment: 'document'
        });
    };

    // Collapse/expand the "Add Sort Option" row via the shared drawer toggle.
    // The whole row is clickable, not just the arrow button.
    Omeka.SortTiles.enableAddRowToggle = function () {
        Omeka.manageDrawers('.sort-tiles-widget', '.add-tile-row');
        $(document).on('click', '.add-tile-row .sortable-item', function (event) {
            if ($(event.target).closest('.drawer-toggle').length) {
                return;
            }
            $(this).find('.drawer-toggle').trigger('click');
        });
    };

    // Mark/unmark a tile for removal via the same delete/undo drawer toggle
    // Element Sets and Item Types use (Omeka.manageDrawers() defaults to
    // '.element' as the container, matching this widget's tile <li>s).
    // Marked tiles are excluded when the list is serialized on submit.
    Omeka.SortTiles.enableTileDeleteToggle = function () {
        Omeka.manageDrawers('.sort-tiles-widget');
        $(document).on('omeka:delete-drawer omeka:undo-drawer-delete', '.sort-tiles-widget .delete-drawer, .sort-tiles-widget .undo-delete', function () {
            Omeka.SortTiles.syncDefaultFieldOptions($(this).closest('.sort-tiles-widget'));
        });
    };

    // Rebuild the "Default Sort Field" select's options from the widget's
    // current, non-deleted tiles, preserving the current selection if it
    // still exists.
    Omeka.SortTiles.syncDefaultFieldOptions = function (widget) {
        var select = widget.find('.default-field-select');
        var currentValue = select.val();
        var stillExists = false;
        select.empty();
        widget.find('li.element').each(function () {
            if ($(this).find('.sortable-item.drawer').hasClass('deleted')) {
                return;
            }
            var field = $(this).find('.tile-field').val();
            select.append($('<option></option>')
                .val(field)
                .text($(this).find('.tile-label').text()));
            if (field === currentValue) {
                stillExists = true;
            }
        });
        if (stillExists) {
            select.val(currentValue);
        }
    };

    // Ask the server to render a new tile from the field input (reusing the
    // same PHP template the initial page render uses) and insert it just
    // before the add-row, so it stays last in the draggable list.
    Omeka.SortTiles.enableAddTile = function () {
        $(document).on('click', '.add-sort-tile-button', function () {
            var widget = $(this).closest('.sort-tiles-widget');
            var fieldInput = widget.find('.new-tile-field');
            var field = $.trim(fieldInput.val());
            if (!field) {
                return;
            }
            var type = widget.find('.sort-tiles').data('type');
            var index = widget.find('.sort-tiles li.element').length;
            $.ajax({
                url: Omeka.SortTiles.addTileUrl,
                dataType: 'text',
                data: {type: type, field: field, index: index},
                success: function (responseText) {
                    widget.find('.sort-tiles li.add-tile-row').before(responseText);
                    fieldInput.val('');
                    Omeka.SortTiles.syncDefaultFieldOptions(widget);
                },
                error: function () {
                    alert(Omeka.SortTiles.addTileErrorText);
                }
            });
        });
    };

    // On form submit, serialize each tile list's current, non-deleted order
    // into its corresponding hidden input as JSON.
    Omeka.SortTiles.setUpFormSubmission = function () {
        $('#appearance-form').submit(function () {
            $('.sort-tiles').each(function () {
                var list = $(this);
                var tiles = [];
                list.find('li.element').each(function () {
                    if ($(this).find('.sortable-item.drawer').hasClass('deleted')) {
                        return;
                    }
                    tiles.push({
                        label: $(this).find('.tile-label').text(),
                        field: $(this).find('.tile-field').val()
                    });
                });
                $('#' + list.data('hidden-input')).val(JSON.stringify(tiles));
            });
        });
    };
})(jQuery);
