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
            if (drawerActionSelector !== undefined) {
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

    Omeka.enableSorting = function(itemsSelector, orderSelector) {
        $('.sortable').sortable({
            items: itemsSelector,
            forcePlaceholderSize: true,
            forceHelperSize: true,
            revert: 200,
            placeholder: 'ui-sortable-highlight',
            containment: 'document',
            update: function (event, ui) {
                Omeka.updateSortingOrder($(this), orderSelector);
            }
        });
    };

    Omeka.updateSortingOrder = function(sortable, orderSelector) {
        sortable.find(orderSelector).each(function (index) {
            $(this).val(index + 1);
        });
    };

    Omeka.enableKeyboardNavigation = function(nodeSelector, orderSelector) {
        var reorderAlertElement = $('#reorder-alerts');
        var sortableNode = '';
        if ((typeof nodeSelector == 'undefined') || nodeSelector == '') {
            sortableNode = 'li';
        } else {
            sortableNode = nodeSelector;
        }

        $(document).on('click', '.keyboard-reorder', function() {
            var keyboardReorderButton = $(this);
            var currentSortableElement = keyboardReorderButton.parents('li').first();
            if (currentSortableElement.hasClass('selected')) {
                currentSortableElement.removeClass('selected');
                keyboardReorderButton.attr('aria-expanded', 'false');
            } else { 
                var selectedNode = $(sortableNode + '.selected');
                selectedNode.find('.keyboard-reorder').attr('aria-expanded', 'false');
                selectedNode.removeClass('selected');
                currentSortableElement.addClass('selected');
                keyboardReorderButton.attr('aria-expanded', 'true');
                keyboardReorderButton.next('.keyboard-reorder-panel').find('button').first().focus();
            }
        });

        $(document).on('click', '.keyboard-reorder-panel button', function(e) {
            var activeButton = $(this);
            var selectedNavItem = activeButton.parents('.selected');
            var selectedNavItemTitle = selectedNavItem.find('.drawer-name').first().text();

            var activeClass = activeButton.attr('class');
            var nextNavItem = selectedNavItem.next(sortableNode);
            var prevNavItem = selectedNavItem.prev(sortableNode);
            var prevNavItemChildren, parentNavItem, positionalNavItem, positionalNavItemTitle;
            switch(activeClass) {
                case 'keyboard-reorder-down':
                    selectedNavItem.insertAfter(nextNavItem);
                    positionalNavItem = nextNavItem;
                    break;
                case 'keyboard-reorder-up':
                    selectedNavItem.insertBefore(prevNavItem);
                    positionalNavItem = prevNavItem;
                    break;
                case 'keyboard-reorder-nest':
                    if (prevNavItem.length > 0) {
                        positionalNavItem = prevNavItem;
                        prevNavItemChildren = prevNavItem.children('.nav-list-item-children').first();
                        if (prevNavItemChildren.length == 0) {
                            prevNavItemChildren = $('<ul></ul>');
                            prevNavItemChildren.appendTo(prevNavItem);
                        }
                        selectedNavItem.appendTo(prevNavItemChildren);
                        prevNavItem = selectedNavItem.prev();
                    }
                    break;
                case 'keyboard-reorder-unnest':
                    parentNavItem = selectedNavItem.parents('.nav-list-item').first();
                    selectedNavItem.insertAfter(parentNavItem);
                    positionalNavItem = parentNavItem;
                    break;
                default:
                    console.log('no reorder');
            }
            positionalNavItemTitle = (positionalNavItem) ? positionalNavItem.find('.drawer-name').first().text() : '';

            if (typeof orderSelector !== 'undefined') {
                var sortable = $('.sortable');
                Omeka.updateSortingOrder(sortable, orderSelector);
            }

            var reorderAction = activeClass.replace('keyboard-reorder-', '');
            selectedNavItem.find('.' + activeClass).first().focus();

            var newAlert = constructAlert(selectedNavItemTitle, reorderAction, positionalNavItemTitle);
            reorderAlertElement.text(newAlert);
            console.log(reorderAlertElement.text());
        });

        $(document).on('click', '.delete-drawer', function() {
            var deleteButton = $(this);
            var parentLi = deleteButton.parents('li').first();
            if (parentLi.hasClass('selected')) {
                var keyboardReorder = parentLi.find('.keyboard-reorder').first();
                keyboardReorder.click();
            }
        });

        var constructAlert = function(selectedNavItemTitle, reorderAction, positionalNavItemTitle) {
            var newAlert = '';
            if (positionalNavItemTitle !== '') {
                var successAlert = reorderAlertElement.data('successAlertTemplate');
                var actionAlert = reorderAlertElement.data(reorderAction + 'ActionAlertTemplate');
                newAlert = successAlert.replace("{ACTIVE}", selectedNavItemTitle) + actionAlert.replace("{RELATIVE}", positionalNavItemTitle);
            } else {
                newAlert = reorderAlertElement.data('failAlertTemplate');
            }
            return newAlert;
        }
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
