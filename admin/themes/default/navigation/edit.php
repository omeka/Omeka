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
        margin: 3px 3px 3px 3px; 
        background-color: #EEEEEE;
        font-size: 14px; 
    }
</style>

<script type="text/javascript">
//<![CDATA[
    jQuery(document).ready(function () {
        
        jQuery('#navigation_form > dd').wrapAll('<ul id="navigation_main_list" />');
        jQuery('#navigation_main_list > dd').wrapInner('<li />');
        jQuery('#navigation_main_list > dd > li').unwrap();
        
        jQuery( "#navigation_main_list" ).sortable();
    	jQuery( "#navigation_main_list" ).disableSelection();
    	
    	jQuery('#navigation_form').submit(function(e) {

            // get link data
            var linkData = [];
            jQuery('#navigation_main_list > li > input[type="checkbox"]').each(function(i, e) {
                var linkInfo = {};
                linkInfo['id'] = jQuery(e).val();
                linkInfo['active'] = jQuery(e).is(':checked');
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
<?php echo $this->form; ?>
</div>

<?php foot(); ?>
