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
        var advanced_form = $('#advanced-form');

        if (!advanced_form.length) {
            return;
        }

        advanced_form.addClass('closed');
        $('#search-container').addClass('with-advanced');

        $('.show-advanced').click(function(e) {
            e.preventDefault();
            var advanced_toggle = $(this);
            advanced_toggle.toggleClass('open').toggleClass('closed');
            advanced_form.toggleClass('open').toggleClass('closed');
            if (advanced_toggle.hasClass('open')) {
                advanced_toggle.attr('aria-expanded', true);
            } else {
                advanced_toggle.attr('aria-expanded', false);
            }
        });
    };

    Omeka.addTooltipEventListeners = function (buttons, tooltips) {
        for (let i = 0; i < buttons.length; i++) {
            buttons[i].addEventListener("mouseover", () => {
                tooltips[i].showPopover({ source: buttons[i] });
                $(buttons[i]).addClass('active');
            });
            buttons[i].addEventListener("mouseout", () => {
                tooltips[i].hidePopover();
                $(buttons[i]).removeClass('active');
            });
            buttons[i].addEventListener("focus", () => {
                tooltips[i].showPopover({ source: buttons[i] });
                $(buttons[i]).addClass('active');
            });
            buttons[i].addEventListener("blur", () => {
                tooltips[i].hidePopover();
                $(buttons[i]).removeClass('active');
            });
        }
    }

    Omeka.setupTooltips = function () {
        const tooltips = $('.tooltip');
        const buttons = $('.has-tooltip');

        Omeka.addTooltipEventListeners(buttons, tooltips);
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
        Omeka.setupTooltips();
        $('.omeka-media').on('error', function () {
            if (this.networkState === HTMLMediaElement.NETWORK_NO_SOURCE ||
                this.networkState === HTMLMediaElement.NETWORK_EMPTY
            ) {
                $(this).replaceWith(this.innerHTML);
            }
        });
    });
})(jQuery);
