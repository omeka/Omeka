if (!Omeka) {
    var Omeka = {};
}

Omeka.ElementSets = {};

(function ($) {

    /**
     * Set up tag remove/undo buttons for each element.
     *
     */
    Omeka.ElementSets.enableElementRemoval = function () {
        $(document).on('click', '.delete-drawer, .undo-delete', function () {
            var buttonElement = $(this).parents('.element');
            var elementDeleteHidden = buttonElement.find('.element-delete-hidden');
            if(elementDeleteHidden.val() != 1) {
                elementDeleteHidden.val(1);
            } else {
                elementDeleteHidden.val(0);
            }
            });
    };

    /**
     * Callback for confirming a delete element.
     */
    Omeka.ElementSets.confirmDeleteElement = function (okText, cancelText) {
        $("#confirm-delete-dialog").dialog({autoOpen: false, modal: true, buttons: [
            {text: okText, class: 'small green button', click: function () {
                $('#edit-item-type-elements').off('submit').submit();
            }}, 
            {text: cancelText, class: 'small red button', title: '', click: function () {
                $(this).dialog('close')
            }}
        ]});
        $('#edit-item-type-elements').on('submit', function (event) {
            if ($('.undo-delete:visible')[0]) {
                event.preventDefault();
                $('#confirm-delete-dialog').dialog('open');
            }
        });
    };

})(jQuery);
