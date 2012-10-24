if (!Omeka) {
    var Omeka = {};
}

Omeka.Tabs = {};

/**
 * Set up JS hide/show tabs for the edit page/
 */
Omeka.Tabs.initialize = function () {
    var tabLinks = jQuery('#section-nav > li > a');
    var tabIds = tabLinks.map(function () {
        // Rely on the fact that the links have pound signs.
        // Workaround IE7's creation of absolute URLs.
        return '#' + this.getAttribute('href').split('#')[1];
    }).toArray().join(',');
    var tabs = jQuery(tabIds);

    function selectTab(tabLink) {
        tabLinks.removeClass('active');
        tabs.hide();

        tabLink.addClass('active');
        jQuery(tabLink.attr('href')).show();
        tabLink.trigger('omeka:tabselected');
    }

    tabLinks.click(function (event) {
        event.preventDefault();
        selectTab(jQuery(this));
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
