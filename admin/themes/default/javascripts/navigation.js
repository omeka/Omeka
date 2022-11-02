if (!Omeka) {
    var Omeka = {};
}

Omeka.Navigation = {};

(function ($) {
    Omeka.Navigation.updateNavList = function () {
        $("#navigation_main_list").nestedSortable({
            listType: 'ul',
            handle: '.main_link',
            items: 'li',
            revert: 200,
            toleranceElement: '> div',
            placeholder: 'ui-sortable-highlight',
            forcePlaceholderSize: true,
            containment: 'document',
            change: function () {
                $('#navigation_form').data('omekaFormDirty', true);
            }
        });
        
        $('#navigation_main_list').on('click', '.link-status', function(e) {
            e.stopPropagation();
        });
    };

    Omeka.Navigation.updateSelectHomepageOptions = function () {
        var select = $('#navigation_homepage_select'),
            selectedValue = select.val();

        // clear the links
        $('#navigation_homepage_select option').slice(1).remove();
        
        // add links
        $('.main_link').each(function() {
            var navLink = $(this).next(),
                value = $(this).find('.navigation-uri').val(),
                label = $(this).find('.navigation-label').val();
            select.append(
                $('<option>').attr('value', value).text(label)
            );
        });
        
        select.val(selectedValue);
    };


    Omeka.Navigation.updateVisitButtons = function () {
        $('div.sortable-item > input[type="checkbox"]').each(function () {
            var hiddenInfo = $.parseJSON($(this).val());
            var buttonsDiv = $(this).parent().next().find('div.main_link_buttons'); 
            if (!buttonsDiv.find('.navigation_main_list_visit').length) {
                buttonsDiv.append('<a class="navigation_main_list_visit blue button" href="' + hiddenInfo.uri + '">' + Omeka.Navigation.visitText + '</a>');
                buttonsDiv.find('.navigation_main_list_visit').click(function (event) {
                    event.preventDefault();
                    var url = $(this).parent().parent().find('.navigation-uri').val();
                    window.open(url);
                });
            }
        });
    };

    Omeka.Navigation.updateDeleteButtons = function () {
        $('#navigation_main_list').on('click', '.delete-drawer,.undo-delete', function () {
            var drawer = $(this).parents('.drawer');
            var drawerInput = drawer.find('.can_delete_nav_link');
            if (drawer.hasClass('deleted')) {
                drawerInput.attr('disabled', true);
            } else {
                drawerInput.removeAttr('disabled');
            }
            Omeka.Navigation.updateNavList();
            Omeka.Navigation.updateSelectHomepageOptions();
        });
    };

    Omeka.Navigation.updateNavLinkEditForms = function () {
        $( '#navigation_main_list .link-status' ).each(function () {
            var hiddenInfo = $.parseJSON($(this).val());
            var bodyDiv = $(this).parent().next(); 
            bodyDiv.find('.navigation-label').val(hiddenInfo.label);
            bodyDiv.find('.navigation-uri').val(hiddenInfo.uri);
            if (!hiddenInfo.can_delete) {
                bodyDiv.find('.navigation-uri').prop('disabled', true);
            }
        });
    };

    Omeka.Navigation.addNewNavLinkForm = function () {
        // add the new nav link add button
        $( '#new_nav_link_button_link' ).click(function (event) {
            event.preventDefault();
            var n_label = $( '#new_nav_link_label' ).val();
            var n_uri = $( '#new_nav_link_uri' ).val();
            if (n_label && n_uri) {
                var n_hidden_info = {
                    'can_delete': true,
                    'uri': n_uri,
                    'label': n_label
                };
                var n_id = 'navigation_main_nav_checkboxes_new_' + (new Date()).getTime();
                var n_value = JSON.stringify(n_hidden_info);
                var edit_nav_header_html = '<div class="sortable-item drawer"><span class="move icon" aria-role="tooltip" title="Move"></span><input type="hidden" name="' + n_id + '" value="0"><input type="checkbox" name="' + n_id + '" id="' + n_id + '" class="can_delete_nav_link">' + n_label + '</div>';
                var link_label_html = '<div><label class="main_link_label_label">' + Omeka.Navigation.labelText + '</label><input type="text" value="' + n_label + '" class="navigation-label" /></div>';
                var link_uri_html = '<div><label class="main_link_uri_label">' + Omeka.Navigation.urlText + '</label><input type="text" value="' + n_uri + '" class="navigation-uri" /></div>';
                var buttons_html = '<div class="main_link_buttons"></div>';
                var edit_nav_body_html = '<div class="drawer-contents opened">' + link_label_html + link_uri_html + buttons_html + '</div>';

                $( '#navigation_main_list' ).append('<li><div class="main_link">' + edit_nav_header_html + edit_nav_body_html + '</div></li>');
                $( '#' + n_id).val(n_value); // does escaping for json data
                $( '#new_nav_link_label' ).val('');
                $( '#new_nav_link_uri' ).val('');
                Omeka.Navigation.updateForNewLinks();
            }
        });
    };

    Omeka.Navigation.setUpFormSubmission = function () {
        $('#navigation_form').submit(function (event) {
            // add ids to li elements so that we can pull out the parent/child relationships
            $('#navigation_main_list li').each(function (index) {
                $(this).attr('id', "list_" + index);
            });
            var parentChildData = $("#navigation_main_list").nestedSortable('toArray', {startDepthCount: 0, excludeRoot: true});
            
            // get link data
            var linkData = [];
            $('#navigation_main_list .link-status').each(function (index) {
                if (this.disabled) {
                    return;
                }
                var hiddenInfo = $.parseJSON($(this).val());
                var bodyDiv = $(this).parent().next();
                var newLabel = $.trim(bodyDiv.find('.navigation-label').val());
                var newUri = $.trim(bodyDiv.find('.navigation-uri').val());
                var linkInfo = {};
                linkInfo.can_delete = hiddenInfo.can_delete;
                linkInfo.visible = $(this).is(':checked');
                // use the user-specified label
                if (newLabel) {
                    linkInfo.label = newLabel;
                } else {
                    linkInfo.label = hiddenInfo.label;
                }
                // only deletable nav links can have their uri's updated
                if (hiddenInfo.can_delete) {
                    linkInfo.uri = newUri;
                } else {
                    linkInfo.uri = hiddenInfo.uri;
                }
                linkInfo.id = parseInt(parentChildData[index].id, 10);
                linkInfo.parent_id = parseInt(parentChildData[index].parent_id, 10);
                
                linkData.push(linkInfo);
            });
            
            // store link data in hidden element
            $('#navigation_hidden').val(JSON.stringify(linkData)); 
        });
    };

    Omeka.Navigation.updateForNewLinks = function () {
        Omeka.Navigation.updateNavList();
        Omeka.Navigation.updateDeleteButtons();
        Omeka.Navigation.updateSelectHomepageOptions();
        Omeka.Navigation.updateVisitButtons();
    };
})(jQuery);
