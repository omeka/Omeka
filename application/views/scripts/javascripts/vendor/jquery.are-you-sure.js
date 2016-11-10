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
        'fieldSelector': ':input:not(:disabled, :submit, :button)',
        'ignoreEvents': {
            // eventType: selector = maps events and selectors that will ignore the warning
            'click keydown': 'input#Delete'
        }
    }, options);

    var serializeForm = function($form) {
        var values = [];
        $form.find(settings.fieldSelector).each(function() {
            var $field = $(this);
            var value = getValue($field);
            if (value !== null) {
                values.push($field.attr('name') +'='+ value);
            }
        });
        return values.join('&');
    };

    var getValue = function($field) {
        if ($field.hasClass('ays-ignore')
            || $field.hasClass('aysIgnore')
            || $field.attr('data-ays-ignore')
            || $field.attr('name') === undefined) {
            return null;
        }

        var val;
        var type = $field.attr('type');
        if ($field.is('select')) {
            type = 'select';
        }

        switch (type) {
            case 'checkbox':
            case 'radio':
                val = $field.is(':checked');
                break;
            case 'select':
                val = $field.val();
                val = $.isArray(val) ? val.join(',') : val;
                break;
            default:
                val = $field.val();
        }

        return val;
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
