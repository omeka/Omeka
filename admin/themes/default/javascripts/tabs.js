if (!Omeka) {
    var Omeka = {};
}

Omeka.Tabs = {};

(function ($) {
    /**
     * Set up JS hide/show tabs for the edit page/
     */
    Omeka.Tabs.initialize = function () {
        var tabButtons = $('#section-tabs [role="tab"]');
        var tabIds = tabButtons.map(function () {
            // Rely on the fact that the links have pound signs.
            // Workaround IE7's creation of absolute URLs.
            return '#' + this.getAttribute('aria-controls');
        }).toArray().join(',');
        var tabs = $(tabIds);

        function selectTab(tabButton) {
            tabButtons.removeClass('active').attr('aria-selected', 'false');
            tabs.hide();

            tabButton.addClass('active').attr('aria-selected', 'true');
            $('#' + tabButton.attr('aria-controls')).show();
            tabButton.trigger('omeka:tabselected');
        }

        tabButtons.click(function (event) {
            selectTab($(this));
        });

        // Select the tab given in the button, if any, or the first tab.
        var selectedTab;
        var url = document.location.toString();
        if (url.match('#')) {
            var anchor = '#' + url.split('#')[1];
            selectedTab = tabButtons.filter('[aria-controls="' + anchor + '"]');
        }
        if (!selectedTab || !selectedTab.length) {
            selectedTab = tabButtons.first();
        }

        selectTab(selectedTab);
    };
})(jQuery);
