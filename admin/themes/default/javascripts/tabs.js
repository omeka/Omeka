if (!Omeka) {
    var Omeka = {};
}

Omeka.Tabs = {};

(function ($) {
    /**
     * Set up JS hide/show tabs for the edit page/
     */
    Omeka.Tabs.initialize = function () {
        var tabLinks = $('#section-nav > li > a');
        var tabIds = tabLinks.map(function () {
            // Rely on the fact that the links have pound signs.
            // Workaround IE7's creation of absolute URLs.
            return '#' + this.getAttribute('href').split('#')[1];
        }).toArray().join(',');
        var tabs = $(tabIds);

        function selectTab(tabLink) {
            tabLinks.removeClass('active');
            tabs.hide();

            tabLink.addClass('active');
            $(tabLink.attr('href')).show();
            tabLink.trigger('omeka:tabselected');
        }

        tabLinks.click(function (event) {
            event.preventDefault();
            selectTab($(this));
        });

        // Select the tab given in the anchor, if any, or the first tab.
        var selectedTab;
        var url = document.location.toString();
        if (url.match('#')) {
            var anchor = '#' + url.split('#')[1];
            selectedTab = tabLinks.filter('[href=' + anchor + ']');
        }
        if (!selectedTab || !selectedTab.length) {
            selectedTab = tabLinks.first();
        }

        selectTab(selectedTab);
    };
})(jQuery);
