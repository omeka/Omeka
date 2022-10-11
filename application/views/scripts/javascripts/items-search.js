if (!Omeka) {
    var Omeka = {};
}

Omeka.Search = {};

(function ($) {
    /**
     * Activate onclick handlers for the dynamic add/remove buttons on the
     * advanced search form.
     */
    Omeka.Search.activateSearchButtons = function () {
        var addButton = $('.add_search');

        var removeButtons = $('.remove_search');

        handleRemoveButtons();

        function incrementScreenReaderLabels(attribute, element, index) {
            var oldAttribute = element.attr(attribute);
            var newAttribute = oldAttribute.replace(/\d+/, parseInt(index) + 1);
            element.attr(attribute, newAttribute);
        }

        /**
         * Callback for adding a new row of advanced search options.
         */
        function addAdvancedSearch() {
            //Copy the div that is already on the search form
            var oldDiv = $('.search-entry').last();

            //Clone the div and append it to the form
            //Passing true should copy listeners, interacts badly with Prototype.
            var div = oldDiv.clone();

            oldDiv.parent().append(div);

            var inputs = div.find('input');
            var selects = div.find('select');
            var rowName = div.attr('id');

            //Find the index of the last advanced search formlet and inc it
            //I.e. if there are two entries on the form, they should be named advanced[0], advanced[1], etc
            var inputName = inputs.last().attr('name');

            //Match the index, parse into integer, increment and convert to string again
            var index = inputName.match(/advanced\[(\d+)\]/)[1];
            var newIndex = (parseInt(index, 10) + 1).toString();

            //Reset the selects, inputs, and aria labels.
            inputs.val('');
            inputs.attr('name', function () {
                return this.name.replace(/\d+/, newIndex);
            });
            incrementScreenReaderLabels('id', div, newIndex);
            incrementScreenReaderLabels('aria-label', div, newIndex);
            div.find('input, select, button').each(function() {
                incrementScreenReaderLabels('aria-labelledby', $(this), newIndex);
            });

            selects.each(function () {
                this.selectedIndex = 0;
            });
            selects.attr('name', function () {
                return this.name.replace(/\d+/, newIndex);
            });

            div.find('.advanced-search-terms').prop('disabled', false);
            //Add the event listener.
            div.find('button.remove_search').click(function () {
                removeAdvancedSearch(this);
            });

            updateAdvancedSearchCount('#search-narrow-by-fields', '#search-narrow-by-field-alerts', '.search-entry');
            handleRemoveButtons();
        }

        function updateAdvancedSearchCount(fieldId, alertId, rowClass) {
            var field =  $(fieldId);
            var countSpan = $(alertId).find('.count');
            var countValue = field.find(rowClass).length;
            countSpan.text(countValue);
        }

        /**
         * Callback for removing an advanced search row.
         *
         * @param {Element} button The clicked delete button.
         */
        function removeAdvancedSearch(button) {
            $(button).parent().remove();
            handleRemoveButtons();
            updateAdvancedSearchCount('#search-narrow-by-fields', '#search-narrow-by-field-alerts', '.search-entry');
        }

        /**
         * Check the number of advanced search elements on the page and only enable
         * the remove buttons if there is more than one.
         */
        function handleRemoveButtons() {
            var removeButtons = $('.remove_search');
            if (removeButtons.length <= 1) {
                removeButtons.attr('disabled', 'disabled').hide();
            } else {
                removeButtons.removeAttr('disabled').show();
            }
        }

        /**
         * Disable term input when the search type doesn't take a term.
         */
        function disableTermInput() {
            var value = $(this).val();
            var disable = value === 'is empty' || value === 'is not empty';
            $(this).siblings('.advanced-search-terms').prop('disabled', disable);
        }

        $('#search-narrow-by-fields')
            .on('change', '.advanced-search-type', disableTermInput)
            .find('.advanced-search-type').each(disableTermInput);

        //Make each button respond to clicks
        addButton.click(function () {
            addAdvancedSearch();
        });
        removeButtons.click(function () {
            removeAdvancedSearch(this);
        });
    };
})(jQuery);
