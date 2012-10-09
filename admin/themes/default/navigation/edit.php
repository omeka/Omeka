<?php
$pageTitle = __('Edit Navigation');
echo head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js_tag('settings'); ?>
<?php echo js_tag('jquery.mjs.nestedSortable'); ?>


<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        
        function updateNavList() {
                        
            jQuery("#navigation_main_list").nestedSortable({
                listType: 'ul',
                handle: 'div',
                items: 'li',
                toleranceElement: '> div'
            });
            
            jQuery('div.navigation_main_link_header input[type="checkbox"]').click(function(e) {
                e.stopPropagation();
            });    
        }
    	
    	function updateSelectHomepageOptions() {
            var hPages = {}
            hPages[''] = '[Default]'; 
            jQuery( 'div.navigation_main_link_header input[type="checkbox"]' ).each(function(i,e) {
                hPages[jQuery(e).next().attr('href')] = jQuery(e).next().text();
            });
	        var selectedValue = jQuery('#navigation_homepage_select option').filter(":selected").val();
    	    jQuery('#navigation_homepage_select').empty();
    	    for(var i in hPages) {
    	        jQuery('#navigation_homepage_select').append('<option value="' + i + '" label="' + hPages[i] + '">' + hPages[i]  + '</option>')
    	    }
    	    jQuery('#navigation_homepage_select option[value="' + selectedValue + '"]').attr('selected', 'selected');
    	}

    	function updateHideButtons() {
            jQuery('div.navigation_main_link_header > input[type="checkbox"]').each(function(i, e) {
                var headerDiv = jQuery(e).parent(); 
                if (!headerDiv.find('div[class="navigation_main_list_hide"]').length) {
        	        headerDiv.append('<div class="navigation_main_list_hide">&#9654;</div>');
    	            headerDiv.find('.navigation_main_list_hide').click(function(ee) {
              	        ee.preventDefault();
              	        var d = jQuery(ee.target).parent().next();
              	        d.toggle();
              	        if (d.is(':hidden')) {
              	            jQuery(ee.target).html('&#9654;'); // right arrow
              	        } else {
              	            jQuery(ee.target).html('&#9660;'); // down arrow
              	        }
              	    });
              	    headerDiv.next().hide();       
        	    }
            });
    	}

    	function updateVisitButtons() {
            jQuery('div.navigation_main_link_header > input[type="checkbox"]').each(function(i, e) {
                var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
                var buttonsDiv = jQuery(e).parent().next().find('div.navigation_main_link_buttons'); 
                if (!buttonsDiv.find('a[class="navigation_main_list_visit blue button"]').length) {
        	        buttonsDiv.append('<a class="navigation_main_list_visit blue button" href="' + hiddenInfo['uri'] + '">Visit</a>');
    	            buttonsDiv.find('.navigation_main_list_visit').click(function(ee) {
              	        ee.preventDefault();
              	        var url = jQuery(ee.target).parent().parent().find('.navigation_main_link_uri').val();
                        window.open(url);
              	    });       
        	    }
            });
    	}
    	
    	function updateDeleteButtons() {
    	    jQuery( 'input.can_delete_nav_link').each(function(i,e) {
    	        var buttonsDiv = jQuery(e).parent().next().find('div.navigation_main_link_buttons'); 
    	        if (!buttonsDiv.children('a[class="navigation_main_list_delete red button"]').length) {
    	            buttonsDiv.append('<a class="navigation_main_list_delete red button" href="">Delete</a>');
           	        buttonsDiv.children('.navigation_main_list_delete').click(function(ee) {
           	            ee.preventDefault();
               	        jQuery(ee.target).parent().parent().parent().parent().remove(); // removes li element
               	        updateNavList();
               	        updateSelectHomepageOptions();
           	        });
    	        } 
    	    });
    	}
    	    	
    	function updateNavLinkEditForms() {
    	    jQuery( 'div.navigation_main_link_header input[type="checkbox"]' ).each(function(i,e) {
                var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
                var bodyDiv = jQuery(e).parent().next(); 
                bodyDiv.find('.navigation_main_link_label').val(hiddenInfo['label']);
                bodyDiv.find('.navigation_main_link_uri').val(hiddenInfo['uri']);
                if (!hiddenInfo['can_delete']) {
                    bodyDiv.find('.navigation_main_link_uri').attr('disabled', 'disabled');
                }
	        });
    	}
    	
    	function addNewNavLinkForm() {
    	    // add the new nav link add button
        	jQuery( '#new_nav_link_button_link' ).click(function(e) {
        	    e.preventDefault();
                var n_label = jQuery( '#new_nav_link_label' ).val();
                var n_uri = jQuery( '#new_nav_link_uri' ).val();
                if (n_label && n_uri) {
                    var n_hidden_info = {
                        'can_delete': true,
                        'uri': n_uri,
                        'label': n_label,
                    };
                    var n_id = 'navigation_main_nav_checkboxes_new_' + (new Date()).getTime();                
                    var n_value = JSON.stringify(n_hidden_info);
                    var edit_nav_header_html = '<div class="navigation_main_link_header"><input type="hidden" name="' + n_id + '" value="0"><input type="checkbox" name="' + n_id + '" id="' + n_id + '" class="can_delete_nav_link"> <a href="' + n_uri + '">' + n_label + '</a></div>';
                    var link_label_html = '<div><label class="navigation_main_link_label_label">Label</label><input type="text" value="' + n_label + '" class="navigation_main_link_label" /></div>';
                    var link_uri_html = '<div><label class="navigation_main_link_uri_label">URI</label><input type="text" value="' + n_uri + '" class="navigation_main_link_uri" /></div>';
                    var buttons_html = '<div class="navigation_main_link_buttons"></div>';
                    var edit_nav_body_html = '<div class="navigation_main_link_body">' + link_label_html + link_uri_html + buttons_html + '</div>';

                    jQuery( '#navigation_main_list' ).append('<li><div class="navigation_main_link">' + edit_nav_header_html + edit_nav_body_html + '</div></li>');
                    jQuery( '#' + n_id).val(n_value); // does escaping for json data
                    jQuery( '#new_nav_link_label' ).val('');
                    jQuery( '#new_nav_link_uri' ).val('');
                    updateNavList();
                    updateDeleteButtons();
                    updateSelectHomepageOptions();
                    updateVisitButtons();
                    updateHideButtons();
                }
        	});
    	}
    	    	                
        // add data to edit nav link forms
        updateNavLinkEditForms();
        
        // add visit buttons
        updateVisitButtons();
        
        // turn list of checkboxes into an navigation list widget
        updateNavList();

        // add delete buttons to list
    	updateDeleteButtons();
    	
        // add the new navigation link form
        addNewNavLinkForm();
        
        // add the hide buttons
        updateHideButtons();
    	
    	// set up form submission
    	jQuery('#navigation_form').submit(function(e) {
    	    
    	    // add ids to li elements so that we can pull out the parent/child relationships
        	jQuery('#navigation_main_list li').each(function(i, e) {
                jQuery(e).attr('id', "list_" + i);
            });
        	var parentChildData = jQuery("#navigation_main_list").nestedSortable('toArray', {startDepthCount: 0});
    	    
            // get link data
            var linkData = [];
            jQuery('div.navigation_main_link_header > input[type="checkbox"]').each(function(i, e) {
                var hiddenInfo = jQuery.parseJSON(jQuery(e).val());                
                var bodyDiv = jQuery(e).parent().next();
                var newLabel = jQuery.trim(bodyDiv.find('.navigation_main_link_label').val());
                var newUri = jQuery.trim(bodyDiv.find('.navigation_main_link_uri').val());                  
                var linkInfo = {};
                linkInfo['can_delete'] = hiddenInfo['can_delete'];
                linkInfo['visible'] = jQuery(e).is(':checked');
                // use the user-specified label
                if (newLabel) {
                    linkInfo['label'] = newLabel;
                } else {
                    linkInfo['label'] = hiddenInfo['label'];
                }
                // only deletable nav links can have their uri's updated
                if (hiddenInfo['can_delete'] && newUri) {
                    linkInfo['uri'] = newUri;
                } else {
                    linkInfo['uri'] = hiddenInfo['uri'];
                }
                linkInfo['id'] = parseInt(parentChildData[i+1]['item_id']);
                linkInfo['parent_id'] = parseInt(parentChildData[i+1]['parent_id']);
                
                linkData.push(linkInfo);
            });
            
            // store link data in hidden element
            jQuery('#navigation_hidden').val(JSON.stringify(linkData)); 
    	});
    });
//]]>    
</script>


<form action="<?php echo $this->form->getAction() ?>"
      enctype="<?php echo $this->form->getEnctype() ?>"
      method="<?php echo $this->form->getMethod() ?>"
      id="<?php echo $this->form->getId() ?>"
      class="<?php echo $this->form->getAttrib('class') ?>" >

<?php echo common('appearance-nav'); ?>

<?php echo flash(); ?>

<div class="seven columns alpha">

<h2><?php echo __('Navigation'); ?></h2>

    <p class="description"><?php echo __('Check the links you would like to display in the main navigation. You can click and drag the links into your preferred display order.'); ?></p>

    <?php echo $this->form->displayNavigationLinksFieldset(); ?>
    
    <?php echo $this->form->getElement(Omeka_Form_Navigation::HIDDEN_ELEMENT_ID); ?>

    <fieldset id="fieldset-new_nav_link_display">
        <h4><?php echo __('Add a Link to the Navigation'); ?></h4>
        
        <div class="field">
            <div class="two columns alpha">
            <label for="new_nav_link_label"><?php echo __('Link Label'); ?></label>
            </div>
            
            <div class="inputs five columns omega">
            <input type="text" id="new_nav_link_label" name="new_nav_link_label" />
            </div>
        </div>
        
        <div class="field">
            <div class="two columns alpha">
                <label for="new_nav_link_uri"><?php echo __('Link URI'); ?></label>
            </div>
            
            <div class="inputs five columns omega">
                <input type="text" id="new_nav_link_uri" name="new_nav_link_uri" />
            </div>
        </div>

        <a href="" id="new_nav_link_button_link" class="blue button"><?php echo __('Add Link'); ?></a>
    </fieldset>
    
</div>

<div id="save" class="three columns omega panel">

    <?php echo $this->formSubmit('submit', __('Save Changes'), array('class'=>'submit big green button')); ?>
    
    <?php echo $this->form->getDisplayGroup(Omeka_Form_Navigation::HOMEPAGE_SELECT_DISPLAY_ELEMENT_ID); ?>

</div>

</form>

<?php echo foot(); ?>
