if (typeof Omeka === 'undefined') {
    Omeka = {};
}

Omeka.Search = {};

/**
 * Activate onclick handlers for the dynamic add/remove buttons on the
 * advanced search form.
 */
Omeka.Search.activateSearchButtons = function () {
    var addButton = jQuery('.add_search');

    var removeButtons = jQuery('.remove_search');

    handleRemoveButtons();

    /**
     * Callback for adding a new row of advanced search options.
     */
    function addAdvancedSearch() {
        //Copy the div that is already on the search form
        var oldDiv = jQuery('.search-entry').last();

        //Clone the div and append it to the form
        //Passing true should copy listeners, interacts badly with Prototype.
        var div = oldDiv.clone();

        oldDiv.parent().append(div);

        var inputs = div.find('input');
        var selects = div.find('select');

        //Find the index of the last advanced search formlet and inc it
        //I.e. if there are two entries on the form, they should be named advanced[0], advanced[1], etc
        var inputName = inputs.last().attr('name');

        //Match the index, parse into integer, increment and convert to string again
        var index = inputName.match(/advanced\[(\d+)\]/)[1];
        var newIndex = (parseInt(index) + 1).toString();

        //Reset the selects and inputs
        inputs.val('');
        inputs.attr('name', function () {
            return this.name.replace(/\d+/, newIndex);
        });

        selects.val('');
        selects.attr('name', function () {
            return this.name.replace(/\d+/, newIndex);
        });

        //Add the event listener.
        div.find('button.remove_search').click(function () {
            removeAdvancedSearch(this);
        });

        handleRemoveButtons();
    }

    /**
     * Callback for removing an advanced search row.
     *
     * @param {Element} button The clicked delete button.
     */
    function removeAdvancedSearch(button) {
        jQuery(button).parent().remove();
        handleRemoveButtons();
    }

    /**
     * Check the number of advanced search elements on the page and only enable
     * the remove buttons if there is more than one.
     */
    function handleRemoveButtons() {
        var removeButtons = jQuery('.remove_search');
        if (removeButtons.length <= 1) {
            removeButtons.attr('disabled', 'disabled').hide();
        } else {
            removeButtons.removeAttr('disabled').show();
        }
    }

    //Make each button respond to clicks
    addButton.click(function () {
        addAdvancedSearch();
    });
    removeButtons.click(function () {
        removeAdvancedSearch(this);
    });
};
