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
            return '#' + this.getAttribute('aria-controls');
        }).toArray().join(',');
        var tabPanels = $(tabIds);

        function selectTab(tabButton) {
            tabButtons.removeClass('active').attr('aria-selected', 'false');
            tabPanels.hide();

            tabButton.addClass('active').attr('aria-selected', 'true').attr('tabindex', '0');
            $('#' + tabButton.attr('aria-controls')).show();
            tabButton.trigger('omeka:tabselected');
        }

        $(document).on('click', '#section-tabs [role="tab"]', function (event) {
            selectTab($(this));
        });

        $(document).on('keydown', '#section-tabs [role="tab"]', (e) => {
            var tabButtonsArray = Array.from(tabButtons);
            var currentTab = e.target;
            var currentIndex = tabButtonsArray.indexOf(currentTab);
            if (currentIndex === -1) return; // Exit if the focused element is not a tab
            var newIndex = 0;

            switch (e.key) {
                case "ArrowRight":
                newIndex = (currentIndex + 1) % tabButtons.length;
                break;
                case "ArrowLeft":
                newIndex = (currentIndex - 1 + tabButtons.length) % tabButtons.length;
                break   ;
                case "Home":
                newIndex = 0;
                break;
                case "End":
                newIndex = tabs.length - 1;
                break;
                default:
                return; // Exit if the key is not recognized
            }

            e.preventDefault();
            e.stopPropagation();
            tabButtons[newIndex].focus();
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
