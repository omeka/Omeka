<?php
$pageTitle = __('Edit Navigation');
head(array('title'=>$pageTitle, 'content_class' => 'vertical-nav', 'bodyclass'=>'settings primary')); ?>
<?php echo js('settings'); ?>

<style>
    #navigation_main_list { 
        list-style-type: none; 
        margin: 0; 
        padding: 0; 
        width: 80%; 
    }
    
    #navigation_main_list li { 
        margin: 3px 3px 6px 3px; 
        background-color: #EEEEEE;
        font-size: 14px;    
    }
    
    #navigation_main_list_new {
        margin-top: 20px;
    }
    
    .navigation_main_list_delete {
        float: right;
        margin-top: 3px;
        margin-right: 20px;
    }
</style>

<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        
        jQuery('#navigation_form > dd').wrapAll('<ul id="navigation_main_list" />');
        jQuery('#navigation_main_list > dd').wrapInner('<li />');
        jQuery('#navigation_main_list > dd > li').unwrap();
        
        jQuery( '#navigation_main_list' ).sortable();
    	jQuery( '#navigation_main_list' ).disableSelection();
    	
    	function updateSelectHomepageOptions() {
            var hPages = {}
            hPages[''] = '[Default]'; 
            jQuery( '#navigation_main_list > li > input[type="checkbox"]' ).each(function(i,e) {
                hPages[jQuery(e).next().attr('href')] = jQuery(e).next().text();
            });
	        
	        var selectedValue = jQuery('#navigation_homepage_select option').filter(":selected").val();
    	    jQuery('#navigation_homepage_select').empty();
    	    for(var i in hPages) {
    	        jQuery('#navigation_homepage_select').append('<option value="' + i + '" label="' + hPages[i] + '">' + hPages[i]  + '</option>')
    	    }
    	    jQuery('#navigation_homepage_select option[value="' + selectedValue + '"]').attr('selected', 'selected');
    	}
    	
    	function updateDeletables() {
    	    jQuery( 'input.can_delete_nav_link').each(function(i,e) {
    	       if (!jQuery(e).parent().children('a[class="navigation_main_list_delete"]').length) {
    	           jQuery(e).parent().append('<a class="navigation_main_list_delete" href="">Delete</a>');
           	       jQuery(e).parent().children('.navigation_main_list_delete').click(function(ee) {
           	           ee.preventDefault();
               	       jQuery(ee.target).parent().remove();
               	       updateSelectHomepageOptions();
           	       });
    	       } 
    	    });
    	}
    	
    	// update the homepage options whenver anyone checks or unchecks a link
    	jQuery( '#navigation_main_list > li > input[type="checkbox"]' ).click(function(){
    	    updateSelectHomepageOptions();
    	});
    	
    	updateDeletables();
    	
    	jQuery( '#navigation_main_list').after('<div id="navigation_main_list_new"><label for="navigation_main_list_new_text">New Link Text</label><br/><input id="navigation_main_list_new_text" type="text"/><br/><label for="navigation_main_list_new_uri">New Link URI</label><br/><input id="navigation_main_list_new_uri" type="text" /><br/><a href="" id="navigation_main_list_add" class="blue button">Add Link</a></div>');
    	
    	jQuery( '#navigation_main_list_add' ).click(function(e) {
    	    e.preventDefault();
            var n_text = jQuery( '#navigation_main_list_new_text' ).val();
            var n_uri = jQuery( '#navigation_main_list_new_uri' ).val();
            if (n_text && n_uri) {
                
                var n_id = 'navigation_main_nav_checkboxes_new_' + (new Date()).getTime();                
                var n_value = '1' +'|' + n_uri + '|' + n_text;
                
                jQuery( '#navigation_main_list' ).append('<li><input type="hidden" name="' + n_id + '" value="0"><input type="checkbox" name="' + n_id + '" id="' + n_id + '" value="' + n_value +  '" class="can_delete_nav_link"> <a href="' + n_uri + '">' + n_text + '</a></li>');
                
                
                jQuery( '#navigation_main_list_new_text' ).val('');
                jQuery( '#navigation_main_list_new_uri' ).val('');
                
                updateDeletables();
                updateSelectHomepageOptions();
            }
    	});
    	
    	jQuery('#navigation_form').submit(function(e) {

            // get link data
            var linkData = [];
            jQuery('#navigation_main_list > li > input[type="checkbox"]').each(function(i, e) {
                var linkInfo = {};
                linkInfo['id'] = jQuery(e).val();
                linkInfo['visible'] = jQuery(e).is(':checked');
                linkData.push(linkInfo);
            });
            
            // store link data in hidden element
            jQuery('#navigation_hidden').val(JSON.stringify(linkData)); 

    	})
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
