<?php
$pageTitle = __('Edit Navigation');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('settings'); ?>

<style>
    #navigation_main_list { 
        list-style-type: none; 
        margin: 0; 
        padding: 0; 
    }
    
    #navigation_main_list li { 
        background-color: #EEEEEE;
        font-size: 14px;
        width: 500px;
    }
    
    #navigation_main_list li div.navigation_main_link_body {
        margin:0;
        height: 200px;
    }
    
    #navigation_main_list_new {
        margin-top: 20px;
    }
    
    #navigation_main_list li div.navigation_main_link_body .navigation_main_list_delete {
        float: right;
        color: white;
        margin:0;
    }
    
    #navigation_main_list li div.navigation_main_link_body .navigation_main_list_visit {
        float: left;
        color: white;
        margin-bottom:0;
    }
    
    .navigation_main_link_header input {
        position: absolute;
        top; 50%;
        left: .5em;
    }
    
    .navigation_main_link_header a {
        margin-left: 1em;
    }
    
     #navigation_main_list li div.navigation_main_link_header span.ui-icon {
        position: static;
        float: right;
        margin-right: 20px;
        margin-top: 10px;
    }
    
</style>

<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        
        
        function updateAccordian() {
            
            jQuery( "#navigation_main_list" ).accordion("destroy");
    		
    		jQuery( "#navigation_main_list" ).accordion({
    			collapsible: true,
    			header: "> li > div.navigation_main_link_header",
    			active: false, 
    		}).sortable({
                axis: "y",
                handle: "div.navigation_main_link_header",
                stop: function( event, ui ) {
                	// IE doesn't register the blur when sorting
                	// so trigger focusout handlers to remove .ui-state-focus
                	ui.item.children( "div.navigation_main_link_header" ).triggerHandler( "focusout" );
            }});
            
            //jQuery( "#navigation_main_list" ).disableSelection();

            jQuery('div.navigation_main_link_header input[type="checkbox"]').click(function(e) {
                e.stopPropagation();
            });    
        }
    	
    	function updateSelectHomepageOptions() {
            var hPages = {}
            hPages[''] = '[Default]'; 
            jQuery( '#navigation_main_list > li > div.navigation_main_link_header input[type="checkbox"]' ).each(function(i,e) {
                hPages[jQuery(e).next().attr('href')] = jQuery(e).next().text();
            });
	        
	        var selectedValue = jQuery('#navigation_homepage_select option').filter(":selected").val();
    	    jQuery('#navigation_homepage_select').empty();
    	    for(var i in hPages) {
    	        jQuery('#navigation_homepage_select').append('<option value="' + i + '" label="' + hPages[i] + '">' + hPages[i]  + '</option>')
    	    }
    	    jQuery('#navigation_homepage_select option[value="' + selectedValue + '"]').attr('selected', 'selected');
    	}

    	function updateVisitButtons() {
    	    
            jQuery('#navigation_main_list > li > div.navigation_main_link_header > input[type="checkbox"]').each(function(i, e) {
                var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
                 if (!jQuery(e).parent().next().find('a[class="navigation_main_list_visit blue button"]').length) {
        	           jQuery(e).parent().next().append('<a class="navigation_main_list_visit blue button" href="' + hiddenInfo['uri'] + '">Visit</a>');
        	           
        	           jQuery(e).parent().next().find('.navigation_main_list_visit').click(function(ee) {
                  	           ee.preventDefault();
                  	           var url = jQuery(ee.target).parent().find('.navigation_main_link_uri').val();
                               window.open(url);
                  	   });
        	           
        	      }
            });
    	}
    	
    	function updateDeletables() {
    	    jQuery( 'input.can_delete_nav_link').each(function(i,e) {
    	       if (!jQuery(e).parent().next().children('a[class="navigation_main_list_delete red button"]').length) {
    	           jQuery(e).parent().next().append('<a class="navigation_main_list_delete red button" href="">Delete</a>');
           	       jQuery(e).parent().next().children('.navigation_main_list_delete').click(function(ee) {
           	           ee.preventDefault();
               	       jQuery(ee.target).parent().parent().remove();
               	       updateAccordian();
               	       updateSelectHomepageOptions();
           	       });
    	       } 
    	    });
    	}
    	    	
    	function updateNavLinkEditForms() {
    	     jQuery( '#navigation_main_list > li > div.navigation_main_link_header input[type="checkbox"]' ).each(function(i,e) {
                 var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
                 jQuery(e).parent().next().find('.navigation_main_link_label').val(hiddenInfo['label']);
                 jQuery(e).parent().next().find('.navigation_main_link_uri').val(hiddenInfo['uri']);
                 if (!hiddenInfo['can_delete']) {
                     jQuery(e).parent().next().find('.navigation_main_link_uri').attr('disabled', 'disabled');
                 }
	         });
    	}
    	
    	function addNewNavLinkForm() {
    	    
    	    // add the new nav link add button
        	jQuery( '#navigation_main_list').after('<div id="navigation_main_list_new"><label for="navigation_main_list_new_label">New Link Label</label><br/><input id="navigation_main_list_new_label" type="text"/><br/><label for="navigation_main_list_new_uri">New Link URI</label><br/><input id="navigation_main_list_new_uri" type="text" /><br/><a href="" id="navigation_main_list_add" class="blue button">Add Link</a></div>');

        	jQuery( '#navigation_main_list_add' ).click(function(e) {
        	    e.preventDefault();
                var n_label = jQuery( '#navigation_main_list_new_label' ).val();
                var n_uri = jQuery( '#navigation_main_list_new_uri' ).val();
                if (n_label && n_uri) {

                    var n_hidden_info = {
                        'can_delete': true,
                        'uri': n_uri,
                        'label': n_label,
                    };

                    var n_id = 'navigation_main_nav_checkboxes_new_' + (new Date()).getTime();                
                    var n_value = JSON.stringify(n_hidden_info);

                    var edit_nav_header_html = '<div class="navigation_main_link_header"><input type="hidden" name="' + n_id + '" value="0"><input type="checkbox" name="' + n_id + '" id="' + n_id + '" class="can_delete_nav_link"> <a href="' + n_uri + '">' + n_label + '</a></div>'

                    var link_label_html = '<p><label>Link Label</label><br/><input type="text" value="' + n_label + '" class="navigation_main_link_label" /></p>';
                    var link_uri_html = '<p><label>Link URI</label><br/><input type="text" value="' + n_uri + '" class="navigation_main_link_uri" /></p>';
                    var visit_url_html = '<a href="' + n_uri + '" class="navigation_main_list_visit blue button">Visit</a>';
                    var edit_nav_body_html = '<div class="navigation_main_link_body">' + link_label_html + link_uri_html + visit_url_html + '</div>';

                    jQuery( '#navigation_main_list' ).append('<li>' + edit_nav_header_html + edit_nav_body_html + '</li>');

                    jQuery( '#' + n_id).val(n_value); // does escaping for json data
                    jQuery( '#navigation_main_list_new_label' ).val('');
                    jQuery( '#navigation_main_list_new_uri' ).val('');

                    updateAccordian();
                    updateDeletables();
                    updateSelectHomepageOptions();
                    updateVisitButtons();
                }
        	});
    	}
    	
    	
    	
    	
    	
    	
    	
    	
    	// create the list of navigation links from checkboxes
    	jQuery('#navigation_form > dd').wrapAll('<ul id="navigation_main_list" />');
        jQuery('#navigation_main_list > dd').wrapInner('<li />');
        jQuery('#navigation_main_list > dd > li').unwrap();
        jQuery('#navigation_main_list > li').wrapInner('<div class="navigation_main_link_header" />');
        jQuery('#navigation_main_list > li').append('<div class="navigation_main_link_body"><p><label>Link Label</label><br/><input type="text" class="navigation_main_link_label" /></p><p><label>Link URI</label><br/><input type="text" class="navigation_main_link_uri" /></p></div>');
        
        // add data to edit Nav Link forms
        updateNavLinkEditForms();
        
        // add visit buttons
        updateVisitButtons();
        
        // turn list into an accordian widget
        updateAccordian();

        // add delete buttons to list
    	updateDeletables();
    	
        // add he new navigation link form
        addNewNavLinkForm();
    	
    	// set up form submission
    	jQuery('#navigation_form').submit(function(e) {            
            // get link data
            var linkData = [];
            jQuery('#navigation_main_list > li > div.navigation_main_link_header > input[type="checkbox"]').each(function(i, e) {
                                
                var hiddenInfo = jQuery.parseJSON(jQuery(e).val());
                                
                var newLabel = jQuery.trim(jQuery(e).parent().next().find('.navigation_main_link_label').val());
                var newUri = jQuery.trim(jQuery(e).parent().next().find('.navigation_main_link_uri').val());
                                                                
                var linkInfo = {};
                linkInfo['can_delete'] = hiddenInfo['can_delete'];
                linkInfo['visible'] = jQuery(e).is(':checked');
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
                
                linkData.push(linkInfo);
            });
            
            // store link data in hidden element
            jQuery('#navigation_hidden').val(JSON.stringify(linkData)); 

    	});
    	
    });
//]]>    
</script>

<div class="seven columns alpha">
<?php echo flash(); ?>
<h2>Main Navigation</h2>
<p>Check the links you would like to display in the main navigation.<br/> You can click and drag the links into your preferred display order.</p>
<?php echo $this->form; ?>
</div>

<?php foot(); ?>
