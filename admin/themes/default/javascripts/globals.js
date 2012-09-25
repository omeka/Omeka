if (typeof Omeka === 'undefined') {
    Omeka = {};
}

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
        theme_advanced_toolbar_align: "left",
        theme_advanced_buttons1: "bold,italic,underline,|,justifyleft,justifycenter,justifyright,|,bullist,numlist,|,link,formatselect,code",
        theme_advanced_buttons2: "",
        theme_advanced_buttons3: "",
        plugins: "paste,inlinepopups,media",
        media_strict: false,
        width: "100%"
    };

    // Overwrite default params with user-passed ones.
    for (var attribute in params) {
        // Account for annoying scripts that mess with prototypes.
        if (params.hasOwnProperty(attribute)) {
            initParams[attribute] = params[attribute];
        }
    }

    tinyMCE.init(initParams);
};

jQuery(document).ready(function () {
    // Adds confirm dialog for delete buttons.
    jQuery('.delete-confirm').click(function () {
        
        if (jQuery(this).is('input')) {
            var url = jQuery(this).parents('form').attr('action');
        } else if (jQuery(this).is('a')) {
            var url = jQuery(this).attr('href');
        }
        jQuery.post(url, function (response){
            jQuery(response).dialog({modal:true});
        });
        return false;
    });

    
    function saveScroll() {

        var $save   = jQuery("#save"),
            $window = jQuery(window),
            offset  = $save.offset(),
            topPadding = 62;
        
        if (document.getElementById("save")) {
            $window.scroll(function() {
                if($window.scrollTop() > offset.top && $window.width() > 767) {
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
    }

    saveScroll();

    // Generates mobile-friendly tables
    jQuery('table').each(function() {
        var table = jQuery(this); // cache table object
        var form_wrapper = table.closest("form");
        var head = table.find('thead th');
        var rows = table.find('tbody tr').clone(); // appending afterwards does not break original table
        
        jQuery(table).addClass("full");
    
        // create new table
        var newtable = jQuery(
          '<table class="mobile">' +
          '  <tbody>' +
          '  </tbody>' +
          '</table>'
        );
        
        if (form_wrapper.length) {
            var form_attributes = jQuery(form_wrapper).prop("attributes");
            var new_form = jQuery('<form></form>');
            var new_submits_before = table.prevAll().clone().get().reverse();
            var new_submits_after = table.nextAll().clone();
            console.log(new_submits_before);
            form_wrapper.addClass("full");
            if (form_attributes.length) {
                jQuery.each(form_attributes, function() {
                    jQuery(new_form).attr(this.name, this.value);
                });
            }
            new_form.attr("class","mobile");
        }
    
        // cache tbody where we'll be adding data
        var newtable_tbody = newtable.find('tbody');
    
        rows.each(function(i) {
          var cols = jQuery(this).find('td');
          var classname = i % 2 ? 'even' : 'odd';
          cols.each(function(k) {
            var new_tr = jQuery('<tr class="' + classname + '"></tr>').appendTo(newtable_tbody);
            new_tr.append(head.clone().get(k));
            new_tr.append(jQuery(this));
          });
        });
    
        if (form_wrapper.length) {
            form_wrapper.after(new_form);
            new_form.prepend(newtable);
            new_form.prepend(new_submits_before);
            new_form.append(new_submits_after).find('.batch-action').remove();
        } else {
            jQuery(this).after(newtable);
        }
        
    });

});