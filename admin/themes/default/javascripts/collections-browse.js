if (!Omeka) {
    var Omeka = {};
}

Omeka.CollectionsBrowse = {};

(function ($) {
    Omeka.CollectionsBrowse.setupDetails = function (detailsText, showDetailsText, hideDetailsText) {
        $('.details').hide();
        $('.action-links').prepend('<li class="details-link">' + detailsText + '</li> ');

        $('tr.collection').each(function() {
            var collectionDetails = $(this).find('.details');
            if ($.trim(collectionDetails.html()) != '') {
                $(this).find('.details-link').css({'color': '#4E7181', 'cursor': 'pointer'}).click(function() {
                    collectionDetails.slideToggle('fast');
                });
            }
        });

        var toggleList = '<a href="#" class="toggle-all-details small blue button">' + showDetailsText + '</a>';

        $('.collection-add').after(toggleList);

        // Toggle collection details.
        var detailsShown = false;
        $('.toggle-all-details').click(function (e) {
            e.preventDefault();
            if (detailsShown) {
            	$('.toggle-all-details').text(showDetailsText);
            	$('.details').slideUp('fast');
            } else {
            	$('.toggle-all-details').text(hideDetailsText);
            	$('.details').slideDown('fast');
            }
            detailsShown = !detailsShown;
        });
    };
})(jQuery);
