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
            mode: "textareas", // All textareas
            theme: "advanced",
            theme_advanced_toolbar_location: "top",
            theme_advanced_statusbar_location: "none",
            theme_advanced_toolbar_align: "left",
            theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,formatselect,code",
            theme_advanced_buttons2: "",
            theme_advanced_buttons3: "",
            theme_advanced_blockformats: "p,address,pre,h1,h2,h3,h4,h5,h6,blockquote,address,div",
            plugins: "paste,inlinepopups,media,autoresize",
            media_strict: false,
            width: "100%",
            autoresize_max_height: 500,
            entities: "160,nbsp,173,shy,8194,ensp,8195,emsp,8201,thinsp,8204,zwnj,8205,zwj,8206,lrm,8207,rlm",
            verify_html: false,
            add_unload_trigger: false
        };

        tinyMCE.init($.extend(initParams, params));
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
                if($window.scrollTop() > offset.top && $window.width() > 767 && ($window.height() - topPadding - 85) >  $save.height()) {
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

    Omeka.stickyNav = function() {
        var $nav    = $("#content-nav"),
            $window = $(window);
        if ($window.height() - 50 < $nav.height()) {
            $nav.addClass("unfix");
        }
        $window.resize( function() {
            if ($window.height() - 50 < $nav.height()) {
                $nav.addClass("unfix");
            } else {
                $nav.removeClass("unfix");
            }
        });
    };


    Omeka.showAdvancedForm = function () {
        var advancedForm = $('#advanced-form');
        $('#search-form').addClass("with-advanced");
        $('#search-form button').addClass("blue button");
        advancedForm.before('<a href="#" id="advanced-search" class="blue button">Advanced Search</a>');
        advancedForm.click(function (event) {
            event.stopPropagation();
        });
        $("#advanced-search").click(function (event) {
            event.preventDefault();
            event.stopPropagation();
            advancedForm.fadeToggle();
            $(document).click(function (event) {
                if (event.target.id == 'query') {
                    return;
                }
                advancedForm.fadeOut();
                $(this).unbind(event);
            });
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
        [Omeka.stickyNav, null],
        [Omeka.showAdvancedForm, null],
        [Omeka.skipNav, null],
        [Omeka.mediaFallback, null],
        [Omeka.warnIfUnsaved, null]
    ];
})(jQuery);
