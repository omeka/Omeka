if (!Omeka) {
    var Omeka = {};
}

(function ($) {
    /**
     * Add the TinyMCE WYSIWYG editor to a page.
     * Default is to add to all textareas.
     *
     * @param {Object} [params] Parameters to pass to TinyMCE, these override the
     * defaults.
     */
    Omeka.wysiwyg = function (params) {
        // Default parameters
        initParams = {
            convert_urls: false,
            selector: "textarea",
            menubar: false,
            statusbar: false,
            toolbar_items_size: "small",
            toolbar: [
                { name: 'formatting', items: [ 'bold', 'italic', 'underline' ] },
                { name: 'alignment', items: [ 'alignleft', 'aligncenter', 'alignright' ] },
                { name: 'lists', items: [ 'bullist', 'numlist' ] },
                { name: 'advanced', items: [ 'link', 'formatselect', 'code' ] },
            ],
            plugins: "lists,link,code,paste,media,autoresize,help",
            autoresize_max_height: 500,
            entities: "160,nbsp,173,shy,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm",
            verify_html: false,
            add_unload_trigger: false,
            cache_suffix: '?v=' + tinymce.majorVersion + '.' + tinymce.minorVersion,
            help_accessibility: true,
        };

        tinymce.init($.extend(initParams, params));
    };

    Omeka.deleteConfirm = function () {
        $('.delete-confirm').click(function (event) {
            var url;

            event.preventDefault();
            if ($(this).is('input')) {
                url = $(this).parents('form').attr('action');
            } else if ($(this).is('a')) {
                url = $(this).attr('href');
            } else {
                return;
            }

            $.post(url, function (response){
                $(response).dialog({modal:true});
            });
        });
    };

    Omeka.saveScroll = function () {
        var $save   = $("#save"),
            $window = $(window),
            offset  = $save.offset(),
            topPadding = 62,
            $contentDiv = $("#content");
        if (document.getElementById("save")) {
            $window.scroll(function () {
                if($window.scrollTop() > offset.top && $window.width() > 991 && ($window.height() - topPadding - 85) >  $save.height()) {
                    $save.stop().animate({
                        marginTop: $window.scrollTop() - offset.top + topPadding
                        });
                } else {
                    $save.stop().animate({
                        marginTop: 0
                    });
                }
            });
        }
    };

    /**
     * Add link that collapses and expands content.
     */
    Omeka.manageDrawers = function (drawerList, containerName) {
        if (!containerName) {
            containerName = '.element';
        }
        $(drawerList).on('click', containerName + ' > .drawer button', function() { 
            var drawerButton = $(this);
            var container = drawerButton.parents(containerName).first();
            var drawerActionSelector = drawerButton.data('action-selector');
            container.find('.drawer').first().toggleClass(drawerActionSelector);
            container.find('.drawer-contents').first().toggleClass(drawerActionSelector);
            if (drawerButton.attr('aria-expanded') && drawerButton.hasClass('drawer-toggle')) {
                Omeka.toggleAriaExpanded(drawerButton);
                drawerButton.trigger('omeka:toggle-drawer');
            }
            if (drawerButton.hasClass('delete-drawer')) {
                container.find('.undo-delete').first().focus();
                drawerButton.trigger('omeka:delete-drawer');
                
            }
            if (drawerButton.hasClass('undo-delete')) {
                container.find('.delete-drawer').first().focus();
                drawerButton.trigger('omeka:undo-drawer-delete');
            }
        });
    };

    Omeka.toggleAriaExpanded = function(element) {
        if (element.attr('aria-expanded') == 'true') {
            element.attr('aria-expanded', 'false');
        } else {
            element.attr('aria-expanded', 'true');
        }
    };
    
    Omeka.toggleMobileMenu = function() {
	    $('.mobile-menu').click(function (event) {
            var button = $(this);
			var target = button.data('target');
			$(target).toggleClass('in');
            button.parent('nav').toggleClass('open');
            Omeka.toggleAriaExpanded(button);
	    });
    };
    
    Omeka.moveNavList = function () {
        nav = $('.content-wrapper > .navigation');
        nav.insertAfter(nav.parent().parent().find('.subhead'));
    };

    Omeka.showAdvancedForm = function () {
        $('#search-form').on('click', '.show-advanced', function() {
            var advanced_toggle = $(this);
            advanced_toggle.toggleClass('open');
            if (advanced_toggle.hasClass('open')) {
                advanced_toggle.attr('aria-expanded', true);
            } else {
                advanced_toggle.attr('aria-expanded', false);
            }
        });
    };

    Omeka.skipNav = function () {
        $("#skipnav").click(function() {
            $("#content").attr("tabindex", -1).focus();
        });

        $("#content").on("blur focusout", function () {
            $(this).removeAttr("tabindex");
        });
    };

    Omeka.addReadyCallback = function (callback, params) {
        this.readyCallbacks.push([callback, params]);
    };

    Omeka.runReadyCallbacks = function () {
        for (var i = 0; i < this.readyCallbacks.length; ++i) {
            var params = this.readyCallbacks[i][1] || [];
            this.readyCallbacks[i][0].apply(this, params);
        }
    };

    Omeka.mediaFallback = function () {
        $('.omeka-media').on('error', function () {
            if (this.networkState === HTMLMediaElement.NETWORK_NO_SOURCE ||
                this.networkState === HTMLMediaElement.NETWORK_EMPTY
            ) {
                $(this).replaceWith(this.innerHTML);
            }
        });
    };

    Omeka.warnIfUnsaved = function() {
        var deleteConfirmed = false;
        var setSubmittedFlag = function () {
            $(this).data('omekaFormSubmitted', true);
        };

        var setOriginalData = function () {
            $(this).data('omekaFormOriginalData', $(this).serialize());
        };

        var formsToCheck = $('form[method=POST]:not(.disable-unsaved-warning)');
        formsToCheck.on('o:form-loaded', setOriginalData);
        formsToCheck.each(function () {
            var form = $(this);
            form.trigger('o:form-loaded');
            form.submit(setSubmittedFlag);
        });

        $('body').on('submit', 'form.delete-confirm-form', function () {
            deleteConfirmed = true;
        });

        $(window).on('beforeunload', function() {
            var preventNav = false;
            formsToCheck.each(function () {
                var form = $(this);
                var originalData = form.data('omekaFormOriginalData');
                var hasFile = false;
                if (form.data('omekaFormSubmitted') || deleteConfirmed) {
                    return;
                }

                form.trigger('o:before-form-unload');

                if (window.tinyMCE) {
                    tinyMCE.triggerSave();
                }

                form.find('input[type=file]').each(function () {
                    if (this.files.length) {
                        hasFile = true;
                        return false;
                    }
                });

                if (form.data('omekaFormDirty')
                    || (originalData && originalData !== form.serialize())
                    || hasFile
                ) {
                    preventNav = true;
                    return false;
                }
            });

            if (preventNav) {
                return 'You have unsaved changes.';
            }
        });
    };

    Omeka.readyCallbacks = [
        [Omeka.deleteConfirm, null],
        [Omeka.saveScroll, null],
        [Omeka.toggleMobileMenu, null],
        [Omeka.showAdvancedForm, null],
        [Omeka.skipNav, null],
        [Omeka.moveNavList, null],
        [Omeka.mediaFallback, null],
        [Omeka.warnIfUnsaved, null]
    ];

    /**
     * Run version notification for addons (active plugins & current theme).
     *
     * Normalizes addon versions by adding a PATCH version if none given. Addons
     * often don't include the PATCH version that's required by the semver spec.
     * Semver's JS doesn't include a way to coerce a version and the "loose"
     * option doesn't apply here.
     *
     * @see https://semver.org/
     * @param string endpoint
     */
    Omeka.runVersionNotification = function (endpoint) {
        $.get(endpoint).done(function(data) {
            var normalizeVersion = function(version) {
                version = String(version);
                if (1 === (version.split('.').length - 1)) {
                    version = version + '.0';
                }
                return version;
            };
            $('.version-notification').each(function(index) {
                var addon = $(this);
                var addonId = addon.data('addon-id');
                if (addonId in data) {
                    if (semver.lt(
                        normalizeVersion(addon.data('current-version')),
                        normalizeVersion(data[addonId]['latest_version'])
                    )) {
                        addon.addClass('active');
                    }
                }
            });
        });
    };

    Omeka.quickFilter = function () {
        var quickFilterSelect = $('select.quick-filter');
        quickFilterSelect.change(function() {
            var url = $(this).val();
            if (url) {
                window.location = url;
            } 
            return false;
        });
    }
})(jQuery);
