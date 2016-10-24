/*!
 * Adapted and simplified from: 
 * jQuery Plugin: Are-You-Sure (Dirty Form Detection)
 * https://github.com/codedance/jquery.AreYouSure/
 *
 * Copyright (c) 2012-2014, Chris Dance and PaperCut Software http://www.papercut.com/
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Author:  chris.dance@papercut.com
 * Version: 1.9.0
 * Date:    13th August 2014
 * Author:  Luk Puk
 * Date:    24th October 2016
 */
(function($) {

$.fn.areYouSure = function(options) {

    var settings = $.extend(
    {
        'message' : 'You have unsaved changes!',
        'watchClass' : 'ays-watching', // used for quick lookup of forms with initiated plugin
        'ignoreEvents': {
            // eventType: selector = maps events and selectors that will ignore the warning
            'click keydown': 'input#Delete'
        }
    }, options);

    var serializeForm = function($form) {
        var values = [$form.serialize()];
        // $.serialize() doesn't include <input type=file tags at all
        $form.find('input[type=file]').each(function() {
            var el = $(this);
            values.push(el.attr('name') +'='+ el.prop('value'));
        });
        return values.join('&');
    };

    var storeOrigState = function($form) {
        $form.data('ays-orig', serializeForm($form));
    };

    var getOrigState = function($form) {
        return $form.data('ays-orig');
    };

    var initForm = function($form) {
        storeOrigState($form);
        $form.addClass(settings.watchClass);
    };

    var rescan = function() {
        storeOrigState($(this));
    };

    var destroy = function() {
        $(this).removeData(['ays-orig', 'ays-ignore'])
            .removeClass(settings.watchClass);
    };

    var reinitialize = function() {
        initForm($(this));
    };

    if (!window.aysUnloadSet) {
        window.aysUnloadSet = true;
        $(window).on('beforeunload', function() {
            var ignoreWarning = false;
            $dirtyForms = $('form.' + settings.watchClass).filter(function() {
                var $form = $(this);
                return !$form.data('ays-ignore') && (getOrigState($form) != serializeForm($form));
            });
            if ($dirtyForms.length) {
                // Prevent multiple prompts - seen on Chrome and IE
                if (navigator.userAgent.toLowerCase().match(/msie|chrome/)) {
                    if (window.aysHasPrompted) {
                        return;
                    }
                    window.aysHasPrompted = true;
                    window.setTimeout(function() {window.aysHasPrompted = false;}, 900);
                }
                return settings.message;
            }
            // reset the ignore flag, if user stays on page
            $dirtyForms.data('ays-ignore', 0);
        });
    }

    return this.each(function(elem) {
        if (!$(this).is('form')) {
            return;
        }
        var $form = $(this);

        $form.on('submit', function() {
            $form.removeClass(settings.watchClass);
        });
        $form.on('reset', function() {
            $form.trigger('rescan.areYouSure');
        });
        // Disable/Ignore warning in specific cases
        if (settings.ignoreEvents) {
            var $document = $(document);
            $.each(settings.ignoreEvents, function(type, selector) {
                $document.on(type, selector, function(e) {
                    $form.data('ays-ignore', 1);
                });
            });
        }
        // Add a custom events
        $form.on('rescan.areYouSure', rescan);
        $form.on('reinitialize.areYouSure', reinitialize);
        $form.on('destroy.areYouSure', destroy);
        initForm($form);
    });
};
})(jQuery);
