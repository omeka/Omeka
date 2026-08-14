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

    // Remove a tile when its delete button is clicked.
    Omeka.SortTiles.enableTileRemoval = function () {
        $(document).on('click', '.sort-tiles-widget .delete-drawer', function () {
            $(this).closest('li.element').remove();
        });
    };

    // Derive a tile's label from its field value: the element name half of
    // an "Element Set,Element Name" pair, or the field itself, title-cased.
    Omeka.SortTiles.deriveTileLabel = function (field) {
        var match = field.match(/^[^,]+,\s*(.+)$/);
        var label = match ? match[1].trim() : field.trim();
        return label.charAt(0).toUpperCase() + label.slice(1);
    };

    // Append a new tile from the field input when "Add" is clicked, inserted
    // just before the add-row so it stays last in the draggable list.
    Omeka.SortTiles.enableAddTile = function () {
        $(document).on('click', '.add-sort-tile-button', function () {
            var widget = $(this).closest('.sort-tiles-widget');
            var fieldInput = widget.find('.new-tile-field');
            var field = $.trim(fieldInput.val());
            if (!field) {
                return;
            }
            var label = Omeka.SortTiles.deriveTileLabel(field);
            var li = $('<li class="element"></li>');
            var item = $('<div class="sortable-item drawer"></div>');
            item.append($('<span class="move icon"></span>')
                .attr('title', Omeka.SortTiles.moveText)
                .attr('aria-label', Omeka.SortTiles.moveText));
            item.append($('<span class="drawer-name tile-label"></span>').text(label));
            item.append($('<input type="hidden" class="tile-field">').val(field));
            item.append($('<button type="button" class="delete-drawer"><span class="icon" aria-hidden="true"></span></button>')
                .attr('title', Omeka.SortTiles.removeText));
            li.append(item);
            widget.find('.sort-tiles li.add-tile-row').before(li);
            fieldInput.val('');
        });
    };

    // On form submit, serialize each tile list's current order into its
    // corresponding hidden input as JSON.
    Omeka.SortTiles.setUpFormSubmission = function () {
        $('#appearance-form').submit(function () {
            $('.sort-tiles').each(function () {
                var list = $(this);
                var tiles = [];
                list.find('li.element').each(function () {
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
