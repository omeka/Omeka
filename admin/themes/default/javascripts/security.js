if (!Omeka) {
    var Omeka = {};
}

Omeka.Security = {};

(function ($) {
    /**
     * Create a button that retrieves and sets default values for an input.
     * Used to allow users to reset the whitelist settings to their defaults.
     *
     * @param {string} whitelistInput Selector for whitelist input.
     * @param {string} ajaxUri URI for AJAX action to retrieve default.
     * @param {string} buttonText Text for the restore button label.
     */
    Omeka.Security.buildRestoreButton = function (whitelistInput, ajaxUri, buttonText) {
        var input = $(whitelistInput);

        /**
         * Insert a button after any given element.
         *
         * @param {string|Element|jQuery} Element to insert after.
         * @param {string} text Text of button.
         */
        function buttonAfter(element, text) {
            button = $('<button type="button">' + text + '</button>');
            return button.insertAfter(element);
        }

        /**
         * Make an AJAX request to restore the form input value.
         *
         * @param {boolean} useDefault Whether the default value should be returned.
         */
        function restore(useDefault) {
            var params = {};
            if (useDefault) {
                params['default'] = 'true';
            }
            $.ajax({
                url: ajaxUri,
                dataType: 'html',
                data: params,
                success: function (data) {
                    input.val(data);
                }
            });
        }

        var restoreButton = buttonAfter(input, buttonText);
        restoreButton.click(function () {
            restore(true);
            if (!restoreButton.next().is('button')) {
                var undoButton = buttonAfter(restoreButton, 'Undo');
                undoButton.click(function () {
                    restore(false);
                    undoButton.remove();
                });
            }
        });
    };
})(jQuery);
