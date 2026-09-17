if (!Omeka) {
    var Omeka = {};
}

Omeka.ItemsBrowse = {};

(function ($) {
    Omeka.ItemsBrowse.setupDetails = function (detailsText, showDetailsText, hideDetailsText) {
        $('.details').hide();
        $('.title').after('<button type="button" class="details-link" aria-expanded="false"><span class="sr-only">' + detailsText + '</span></button></li>');

        $('tr.item').each(function() {
            var itemDetails = $(this).find('.details');
            var itemDetailsId = itemDetails.attr('id');
            var itemDetailsButton = $(this).find('.details-link');
            if ($.trim(itemDetails.html()) != '') {
                itemDetails.attr('aria-controls', itemDetailsId);
                $(this).on('click', '.details-link', function () {
                    if (itemDetailsButton.attr('aria-expanded') == "false") {
                        itemDetailsButton.attr('aria-expanded', 'true');
                    } else {
                        itemDetailsButton.attr('aria-expanded', 'false');
                    }
                    itemDetails.slideToggle('fast');
                });
            }
        });

        var toggleList = '<button type="button" class="toggle-all-details full-width-mobile blue button">' + showDetailsText + '</button>';

        $('.advanced-search-link').before(toggleList);

        // Toggle item details.
        var detailsShown = false;
        var srAlerts = $('#details-sr-alerts');
        var showAllSuccess = srAlerts.data('show-details-success');
        var hideAllSuccess = srAlerts.data('hide-details-success');
        $('.toggle-all-details').click(function (e) {
            if (detailsShown) {
            	$('.toggle-all-details').text(showDetailsText);
            	$('.details').slideUp('fast');
                $('.details-link').attr('aria-expanded', "false");
                srAlerts.text(showAllSuccess);
            } else {
            	$('.toggle-all-details').text(hideDetailsText);
            	$('.details').slideDown('fast');
                $('.details-link').attr('aria-expanded', "true");
                srAlerts.text(hideAllSuccess);
            }
            detailsShown = !detailsShown;
        });
    };

    Omeka.ItemsBrowse.setupBatchEdit = function () {
        var itemCheckboxes = $("table#items tbody input[type=checkbox]");
        var globalCheckboxLabel = $('th.batch-edit-heading label').text();
        var globalCheckbox = $('th.batch-edit-heading').append('<input type="checkbox" name="batch-all-checkbox" id="batch-all-checkbox" title="' + globalCheckboxLabel + '">').find('input');
        var batchEditSubmit = $('.batch-edit-option input[type=submit]');
        var batchAllButton = $('.batch-all-toggle');
        var batchAllInput = $('#batch-all');
        var selectedCounter = $('.selected .count');

        /**
         * Disable the batch submit button first, will be enabled once item
         * checkboxes are checked.
         */
        batchEditSubmit.prop('disabled', true);

        /**
         * Disable all the itemCheckboxes if the batchAllButton is checked.
         */
        batchAllButton.click(function() {
            batchAllButton.toggleClass('active');
            if (batchAllButton.hasClass('active')) {
                batchAllInput.removeAttr('disabled');
                selectedCounter.text($(this).data('records-count'));
                globalCheckbox.prop('disabled', 'disabled');
                itemCheckboxes.prop('disabled', 'disabled');
            } else  {
                batchAllInput.prop('disabled', 'disabled');
                selectedCounter.text($("table#items tbody input[type=checkbox]:checked").length);
                globalCheckbox.removeAttr('disabled');
                itemCheckboxes.removeAttr('disabled');
            }
            checkBatchEditSubmitButton();
        });

        /**
         * Check all the itemCheckboxes if the globalCheckbox is checked.
         */
        globalCheckbox.change(function() {
            itemCheckboxes.prop('checked', !!this.checked);
            selectedCounter.text($("table#items tbody input[type=checkbox]:checked").length);
            checkBatchEditSubmitButton();
        });

        /**
         * Uncheck the global checkbox if any of the itemCheckboxes are
         * unchecked.
         */
        itemCheckboxes.change(function(){
            if (!this.checked) {
                globalCheckbox.prop('checked', false);
            }
            selectedCounter.text($("table#items tbody input[type=checkbox]:checked").length);
            checkBatchEditSubmitButton();
        });

        /**
         * Check whether the batchEditSubmit button should be enabled.
         * If any of the itemCheckboxes or the batchAllButton is checked, the
         * batchEditSubmit button is enabled.
         */
        function checkBatchEditSubmitButton() {
            var checked = batchAllButton.hasClass('active');
            if (!checked) {
                itemCheckboxes.each(function() {
                    if (this.checked) {
                        checked = true;
                        return false;
                    }
                });
            }

            batchEditSubmit.prop('disabled', !checked);
        }
    };
})(jQuery);
