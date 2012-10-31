if (!Omeka) {
    var Omeka = {};
}

Omeka.Navigation = {};

Omeka.Navigation.updateNavList = function () {
    jQuery("#navigation_main_list").nestedSortable({
        listType: 'ul',
        handle: '.main_link',
        items: 'li',
        toleranceElement: '> div',
        placeholder: 'ui-sortable-highlight',
        forcePlaceholderSize: true,
        containment: '#content'
    });
    
    jQuery('div.sortable-item input[type="checkbox"]').click(function(e) {
        e.stopPropagation();
    });
};

Omeka.Navigation.updateSelectHomepageOptions = function () {
    var hPages = {};
    hPages[''] = '[Default]'; 
    jQuery( 'div.sortable-item input[type="checkbox"]' ).each(function(i,e) {
        hPages[jQuery(e).next().attr('href')] = jQuery(e).next().text();
    });
    var selectedValue = jQuery('#navigation_homepage_select option').filter(":selected").val();
    jQuery('#navigation_homepage_select').empty();
    for(var i in hPages) {
        jQuery('#navigation_homepage_select').append('<option value="' + i + '" label="' + hPages[i] + '">' + hPages[i]  + '</option>');
    }
    jQuery('#navigation_homepage_select option[value="' + selectedValue + '"]').attr('selected', 'selected');
};

Omeka.Navigation.updateHideButtons = function () {
    jQuery('div.sortable-item > input[type="checkbox"]').each(function(i, e) {
        var headerDiv = jQuery(e).parent(); 
        if (!headerDiv.find('div[class="drawer closed"]').length) {
            headerDiv.append('<div class="drawer closed">&#9654;</div>');
            headerDiv.find('.drawer').click(function(ee) {
                ee.preventDefault();
                var d = jQuery(ee.target).parent().next();
                d.toggle();
                if (d.is(':hidden')) {
                  jQuery(ee.target).html('&#9654;'); // right arrow
                  headerDiv.find('.drawer').removeClass('opened').addClass('closed');
                } else {
                  jQuery(ee.target).html('&#9660;'); // down arrow
                  headerDiv.find('.drawer').removeClass('closed').addClass('opened');
                }
            }).mousedown(function(ee) { ee.stopPropagation(); });
            headerDiv.next().hide();
        }
    });
};

Omeka.Navigation.updateVisitButtons = function () {
    jQuery('div.sortable-item > input[type="checkbox"]').each(function(i, e) {
        var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
        var buttonsDiv = jQuery(e).parent().next().find('div.main_link_buttons'); 
        if (!buttonsDiv.find('a[class="navigation_main_list_visit blue button"]').length) {
            buttonsDiv.append('<a class="navigation_main_list_visit blue button" href="' + hiddenInfo.uri + '">Visit</a>');
            buttonsDiv.find('.navigation_main_list_visit').click(function(ee) {
                ee.preventDefault();
                var url = jQuery(ee.target).parent().parent().find('.main_link_uri').val();
                window.open(url);
            });
        }
    });
};

Omeka.Navigation.updateDeleteButtons = function () {
    jQuery( 'input.can_delete_nav_link').each(function(i,e) {
        var buttonsDiv = jQuery(e).parent().next().find('div.main_link_buttons'); 
        if (!buttonsDiv.children('a[class="navigation_main_list_delete red button"]').length) {
            buttonsDiv.append('<a class="navigation_main_list_delete red button" href="">Delete</a>');
            buttonsDiv.children('.navigation_main_list_delete').click(function(ee) {
                ee.preventDefault();
                jQuery(ee.target).parent().parent().parent().parent().remove(); // removes li element
                Omeka.Navigation.updateNavList();
                Omeka.Navigation.updateSelectHomepageOptions();
            });
        } 
    });
};
                
Omeka.Navigation.updateNavLinkEditForms = function () {
    jQuery( 'div.sortable-item input[type="checkbox"]' ).each(function(i,e) {
        var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
        var bodyDiv = jQuery(e).parent().next(); 
        bodyDiv.find('.navigation-label').val(hiddenInfo.label);
        bodyDiv.find('.navigation-uri').val(hiddenInfo.uri);
        if (!hiddenInfo.can_delete) {
            bodyDiv.find('.navigation-uri').attr('disabled', 'disabled');
        }
    });
};

Omeka.Navigation.addNewNavLinkForm = function () {
    // add the new nav link add button
    jQuery( '#new_nav_link_button_link' ).click(function(e) {
        e.preventDefault();
        var n_label = jQuery( '#new_nav_link_label' ).val();
        var n_uri = jQuery( '#new_nav_link_uri' ).val();
        if (n_label && n_uri) {
            var n_hidden_info = {
                'can_delete': true,
                'uri': n_uri,
                'label': n_label
            };
            var n_id = 'navigation_main_nav_checkboxes_new_' + (new Date()).getTime();                
            var n_value = JSON.stringify(n_hidden_info);
            var edit_nav_header_html = '<div class="sortable-item"><input type="hidden" name="' + n_id + '" value="0"><input type="checkbox" name="' + n_id + '" id="' + n_id + '" class="can_delete_nav_link"> <a href="' + n_uri + '">' + n_label + '</a></div>';
            var link_label_html = '<div><label class="main_link_label_label">Label</label><input type="text" value="' + n_label + '" class="main_link_label" /></div>';
            var link_uri_html = '<div><label class="main_link_uri_label">URI</label><input type="text" value="' + n_uri + '" class="main_link_uri" /></div>';
            var buttons_html = '<div class="main_link_buttons"></div>';
            var edit_nav_body_html = '<div class="drawer-contents">' + link_label_html + link_uri_html + buttons_html + '</div>';

            jQuery( '#navigation_main_list' ).append('<li><div class="main_link">' + edit_nav_header_html + edit_nav_body_html + '</div></li>');
            jQuery( '#' + n_id).val(n_value); // does escaping for json data
            jQuery( '#new_nav_link_label' ).val('');
            jQuery( '#new_nav_link_uri' ).val('');
            Omeka.Navigation.updateForNewLinks();
        }
    });
};

Omeka.Navigation.setUpFormSubmission = function () {
    jQuery('#navigation_form').submit(function(e) {
        // add ids to li elements so that we can pull out the parent/child relationships
        jQuery('#navigation_main_list li').each(function(i, e) {
            jQuery(e).attr('id', "list_" + i);
        });
        var parentChildData = jQuery("#navigation_main_list").nestedSortable('toArray', {startDepthCount: 0});
        
        // get link data
        var linkData = [];
        jQuery('div.sortable-item > input[type="checkbox"]').each(function(i, e) {
            var hiddenInfo = jQuery.parseJSON(jQuery(e).val());                
            var bodyDiv = jQuery(e).parent().next();
            var newLabel = jQuery.trim(bodyDiv.find('.navigation-label').val());
            var newUri = jQuery.trim(bodyDiv.find('.navigation-uri').val());                  
            var linkInfo = {};
            linkInfo.can_delete = hiddenInfo.can_delete;
            linkInfo.visible = jQuery(e).is(':checked');
            // use the user-specified label
            if (newLabel) {
                linkInfo.label = newLabel;
            } else {
                linkInfo.label = hiddenInfo.label;
            }
            // only deletable nav links can have their uri's updated
            if (hiddenInfo.can_delete && newUri) {
                linkInfo.uri = newUri;
            } else {
                linkInfo.uri = hiddenInfo.uri;
            }
            linkInfo.id = parseInt(parentChildData[i+1].item_id, 10);
            linkInfo.parent_id = parseInt(parentChildData[i+1].parent_id, 10);
            
            linkData.push(linkInfo);
        });
        
        // store link data in hidden element
        jQuery('#navigation_hidden').val(JSON.stringify(linkData)); 
    });
};

Omeka.Navigation.updateForNewLinks = function () {
    Omeka.Navigation.updateNavList();
    Omeka.Navigation.updateDeleteButtons();
    Omeka.Navigation.updateSelectHomepageOptions();
    Omeka.Navigation.updateVisitButtons();
    Omeka.Navigation.updateHideButtons();
};
