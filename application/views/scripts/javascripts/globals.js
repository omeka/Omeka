if (!Omeka) {
    var Omeka = {};
}

(function($) {
    // Skip to content
    Omeka.skipNav = function() {
        $("#skipnav").click(function() {
            $("#content").focus()
        });
    };

    // Show advanced options for site-wide search.
    Omeka.showAdvancedForm = function () {
        var advanced_form = $('#advanced-form');
        var show_advanced = '<a href="#" class="show-advanced button">Advanced Search</a>';
        var search_submit = $('#search-form button');

        // Set up classes and DOM elements jQuery will use.
        if (advanced_form.length > 0) {
            $('#search-container').addClass('with-advanced');
            advanced_form.addClass('closed').before(show_advanced);
        }

        $('.show-advanced').click(function(e) {
            e.preventDefault();
            advanced_form.toggleClass('open').toggleClass('closed');
        });
    };

    Omeka.megaMenu = function (menuSelector, customMenuOptions) {
        if (typeof menuSelector === 'undefined') {
            menuSelector = '#primary-nav';
        }

        var menuOptions = {
            /* prefix for generated unique id attributes, which are required
             to indicate aria-owns, aria-controls and aria-labelledby */
            uuidPrefix: "accessible-megamenu",

            /* css class used to define the megamenu styling */
            menuClass: "nav-menu",

            /* css class for a top-level navigation item in the megamenu */
            topNavItemClass: "nav-item",

            /* css class for a megamenu panel */
            panelClass: "sub-nav",

            /* css class for a group of items within a megamenu panel */
            panelGroupClass: "sub-nav-group",

            /* css class for the hover state */
            hoverClass: "hover",

            /* css class for the focus state */
            focusClass: "focus",

            /* css class for the open state */
            openClass: "open"
        };

        $.extend(menuOptions, customMenuOptions);

        $(menuSelector).accessibleMegaMenu(menuOptions);
    };

    $(document).ready(function () {
        $('.omeka-media').on('error', function () {
            if (this.networkState === HTMLMediaElement.NETWORK_NO_SOURCE ||
                this.networkState === HTMLMediaElement.NETWORK_EMPTY
            ) {
                $(this).replaceWith(this.innerHTML);
            }
        });
    });
})(jQuery);
