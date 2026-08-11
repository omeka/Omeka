if (!Omeka) {
    var Omeka = {};
}

Omeka.SortTiles = {};

(function ($) {

    // Enable drag and drop reordering of sort tiles.
    Omeka.SortTiles.enableSorting = function () {
        $('.sortable-tiles').sortable({
            items: 'li.sort-tile',
            placeholder: 'ui-sortable-highlight',
            containment: 'document'
        });
    };

    // Remove a tile when its remove button is clicked.
    Omeka.SortTiles.enableTileRemoval = function () {
        $(document).on('click', '.remove-sort-tile-button', function () {
            $(this).closest('li.sort-tile').remove();
        });
    };

    // Append a new tile from the label/field inputs when "Add" is clicked.
    Omeka.SortTiles.enableAddTile = function () {
        $(document).on('click', '.add-sort-tile-button', function () {
            var widget = $(this).closest('.sort-tiles-widget');
            var labelInput = widget.find('.new-tile-label');
            var fieldInput = widget.find('.new-tile-field');
            var label = $.trim(labelInput.val());
            var field = $.trim(fieldInput.val());
            if (!label || !field) {
                return;
            }
            var li = $('<li class="sort-tile"></li>');
            li.append($('<span class="tile-handle">&#9776;</span>'));
            li.append($('<span class="tile-label"></span>').text(label));
            li.append($('<input type="hidden" class="tile-field">').val(field));
            li.append($('<button type="button" class="remove-sort-tile-button">&times;</button>')
                .attr('title', Omeka.SortTiles.removeText));
            widget.find('.sortable-tiles').append(li);
            labelInput.val('');
            fieldInput.val('');
        });
    };

    // On form submit, serialize each tile list's current order into its
    // corresponding hidden input as JSON.
    Omeka.SortTiles.setUpFormSubmission = function () {
        $('#appearance-form').submit(function () {
            $('.sortable-tiles').each(function () {
                var list = $(this);
                var tiles = [];
                list.find('li.sort-tile').each(function () {
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
