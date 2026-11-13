if (!Omeka) {
    var Omeka = {};
}

(function($) {
    // Skip to content
    Omeka.skipNav = function () {
      $("#skipnav").click(function(e) {
          e.preventDefault();
          $("#content").attr("tabindex", -1).focus();
      });
    
      $("#content").on("blur focusout", function () {
          $(this).removeAttr("tabindex");
      });
    };
    
    // Show advanced options for site-wide search.
    Omeka.showAdvancedForm = function () {
        var searchForm = $('#search-form');
        var advanced_toggle = $('#advanced-search');
        var advancedFormElement = document.getElementById('advanced-form');
        searchForm.on('click', '#advanced-search', function(e) {
            var autocloseEvents = 'keydown.autoclose click.autoclose focusin.autoclose';
            if (advanced_toggle.attr('aria-expanded') == 'false') {
                advanced_toggle.attr('aria-expanded', 'true');
                $(document).on('keydown.autoclose', function(e) {
                    if (e.key == 'Escape') {
                        advanced_toggle.attr('aria-expanded', 'false');
                        advanced_toggle.focus();
                        $(document).off(autocloseEvents);
                    };
                });
                e.stopPropagation();
                $(document).on('click.autoclose focusin.autoclose', function(e) {
                    if (!$.contains(advancedFormElement, e.target)) {
                        if (!$(e.target).hasClass('show-advanced')) {
                            advanced_toggle.attr('aria-expanded', 'false');
                            $(document).off(autocloseEvents);
                        }
                    }
                });
            } else {
                advanced_toggle.attr('aria-expanded', 'false');
            }
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
