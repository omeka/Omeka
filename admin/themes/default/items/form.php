<?php echo js('tiny_mce/tiny_mce'); 
// echo js('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php // The following includes the Autocompleter class. ?>
<?php echo js('scriptaculous', 'javascripts', array('controls'));  ?>
<?php echo js('items'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[    
    Event.observe(window,'load', function() {
        new Control.Tabs('section-nav', {  
            afterChange: function(new_container) {  
                document.fire('omeka:edititemtabafterchanged');
            }  
        }); 

        var addImage = <?php echo js_escape(img('add.png')); ?>;
        var deleteImage = <?php echo js_escape(img('delete.gif')); ?>;
        Omeka.ItemForm.enableTagRemoval(addImage, deleteImage);
        Omeka.ItemForm.makeFileWindow();
        Omeka.ItemForm.tagChoices(<?php echo js_escape(uri(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>);

        // Must run the element form scripts AFTER reseting textarea ids.
        document.fire('omeka:elementformload');
        
        Omeka.ItemForm.enableAddFiles();
        Omeka.ItemForm.changeItemType(<?php echo js_escape(uri("items/change-type")) ?><?php if ($id = item('id')) echo ', '.$id; ?>);
    });
    
    document.observe('omeka:elementformload', function(e){
        Omeka.ItemForm.makeElementControls(<?php echo js_escape(uri('items/element-form')); ?><?php if ($id = item('id')) echo ', '.$id; ?>); 
        Omeka.ItemForm.enableWysiwyg();
    });
//]]>   
</script>

<?php echo flash(); ?>

<div id="public-featured">
    <?php if ( has_permission('Items', 'makePublic') ): ?>
        <div class="checkbox">
            <label for="public">Public:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'public', 'id'=>'public'), $item->public); ?></div>
        </div>
    <?php endif; ?>
    <?php if ( has_permission('Items', 'makeFeatured') ): ?>
        <div class="checkbox">
            <label for="featured">Featured:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'featured', 'id'=>'featured'), $item->featured); ?></div>
        </div>
    <?php endif; ?>
</div>
<div id="item-metadata">
<?php foreach ($tabs as $tabName => $tabContent): ?>
    <?php if (!empty($tabContent)): ?>
        <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
        <fieldset class="set">
            <legend><?php echo html_escape($tabName); ?></legend>
            <?php echo $tabContent; ?>        
        </fieldset>
        </div>     
    <?php endif; ?>
<?php endforeach; ?>
</div>