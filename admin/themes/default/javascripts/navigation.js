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
        $('#navigation_main_list .main_link').each(function() {
            var navLink = $(this),
                value = navLink.find('.navigation-uri').val(),
                label = navLink.find('.navigation-label').val();
            select.append(
                $('<option>').attr('value', value).text(label)
            );
        });
        
        select.val(selectedValue);
    };


    Omeka.Navigation.updateVisitButtons = function () {
        $('#navigation_main_list .link-status').each(function () {
            var enableCheckbox = $(this);
            var link = enableCheckbox.parents('.main_link');
            var hiddenInfo = $.parseJSON(enableCheckbox.val());
            var buttonsDiv = link.find('div.main_link_buttons'); 
            if (!buttonsDiv.find('.navigation_main_list_visit').length) {
                buttonsDiv.append('<button type="button" class="navigation_main_list_visit blue button" href="' + hiddenInfo.uri + '">' + Omeka.Navigation.visitText + '</button>');
                buttonsDiv.find('.navigation_main_list_visit').click(function () {
                    var url = link.find('.navigation-uri').val();
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
        $( '#new_nav_link_button_link' ).click(function () {
            var n_label = $('#new_nav_link_label').val();
            var n_uri = $('#new_nav_link_uri').val();
            $('#add-new-options .flash').each(function() {
                $(this).hide();
            });
            if (n_label && n_uri) {
                var newLink = $('#navigation_form .template').clone();
                var n_hidden_info = {
                    'can_delete': true,
                    'uri': n_uri,
                    'label': n_label
                }
                var timeId = (new Date()).getTime();
                var n_value = JSON.stringify(n_hidden_info);

                var needIdUpdates = ['.link-status', '.drawer-name', '.drawer-toggle', '.undo-delete', '.delete-drawer', '.drawer-contents', '.navigation-label', '.navigation-uri'];
                var needForUpdates = ['.label-label', '.uri-label'];
                var needNameUpdates = ['.link-status', '.navigation-label', '.navigation-uri'];
                var needLabelledByUpdates = ['.link-status', '.drawer-toggle', '.undo-delete', '.delete-drawer'];
                Omeka.Navigation.populateNewNavLinkAttributes(needIdUpdates, timeId, 'id', newLink);
                Omeka.Navigation.populateNewNavLinkAttributes(needForUpdates, timeId, 'for', newLink);
                Omeka.Navigation.populateNewNavLinkAttributes(needNameUpdates, timeId, 'name', newLink);
                Omeka.Navigation.populateNewNavLinkAttributes(needLabelledByUpdates, timeId, 'aria-labelledby', newLink);

                newLink.find('.drawer-name').text(n_label);
                newLink.find('.navigation-label').val(n_label);
                newLink.find('.navigation-uri').val(n_uri).removeAttr('disabled');
                newLink.removeClass('template');

                $('#navigation_main_list').append(newLink);
                newLink.find('.link-status').val(n_value); // does escaping for json data
                $('#new_nav_link_label').val('');
                $('#new_nav_link_uri').val('');

                var totalLinks = $('#navigation_main_list .main_link').length;
                $('.link-count').text(totalLinks);
                $('#new-link-success').show();
                Omeka.Navigation.updateForNewLinks();
            } else {
                if (!n_label) { $('#label-required').show(); }
                if (!n_uri) { $('#uri-required').show(); }
            }
        });
    };

    Omeka.Navigation.populateNewNavLinkAttributes = function (map, timeId, attribute, newLink) {
        for (var selector in map) {
            var element = newLink.find(map[selector]);
            var newAttribute = element.attr(attribute).replaceAll('[pageId]',timeId);
            element.attr(attribute, newAttribute);
        }
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
