if (!Omeka) {
    var Omeka = {};
}

Omeka.ItemsBrowse = {};

(function ($) {
    Omeka.ItemsBrowse.setupDetails = function (detailsText, showDetailsText, hideDetailsText) {
        $('.details').hide();
        $('.action-links').prepend('<li class="details-link">' + detailsText + '</li> ');

        $('tr.item').each(function() {
            var itemDetails = $(this).find('.details');
            if ($.trim(itemDetails.html()) != '') {
                $(this).find('.details-link').css({'color': '#4E7181', 'cursor': 'pointer'}).click(function() {
                    itemDetails.slideToggle('fast');
                });
            }
        });

        var toggleList = '<a href="#" class="toggle-all-details small blue button">' + showDetailsText + '</a>';

        $('.advanced-search-link').before(toggleList);

        // Toggle item details.
        var detailsShown = false;
        $('.toggle-all-details').click(function (e) {
            e.preventDefault();
            if (detailsShown) {
                $('.toggle-all-details').text(hideDetailsText);
                $('.details').slideDown('fast');
            } else {
                $('.toggle-all-details').text(showDetailsText);
                $('.details').slideUp('fast');
            }
            detailsShown = !detailsShown;
        });
    };

    Omeka.ItemsBrowse.setupBatchEdit = function () {
        var itemCheckboxes = $("table#items tbody input[type=checkbox]");
        var globalCheckbox = $('th.batch-edit-heading').html('<input type="checkbox">').find('input');
        var batchEditSubmit = $('.batch-edit-option input');
        /**
         * Disable the batch submit button first, will be enabled once item
         * checkboxes are checked.
         */
        batchEditSubmit.prop('disabled', true);

        /**
         * Check all the itemCheckboxes if the globalCheckbox is checked.
         */
        globalCheckbox.change(function() {
            itemCheckboxes.prop('checked', !!this.checked);
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
            checkBatchEditSubmitButton();
        });

        /**
         * Check whether the batchEditSubmit button should be enabled.
         * If any of the itemCheckboxes is checked, the batchEditSubmit button
         * is enabled.
         */
        function checkBatchEditSubmitButton() {
            var checked = false;
            itemCheckboxes.each(function() {
                if (this.checked) {
                    checked = true;
                    return false;
                }
            });

            batchEditSubmit.prop('disabled', !checked);
        }
    };
})(jQuery);
