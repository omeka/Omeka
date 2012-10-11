<?php echo js_tag('tiny_mce/tiny_mce'); 
// echo js_tag('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php echo js_tag('elements'); ?>
<?php echo js_tag('tabs'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
// TinyMCE hates document.ready.
jQuery(window).load(function () {
    Omeka.Tabs.initialize();
    
    Omeka.wysiwyg({
        mode: "none",
        forced_root_block: ""
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');

});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?php echo js_escape(url('elements/element-form')); ?>,'Item'<?php if ($id = metadata('collection', 'id')) echo ', '.$id; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
//]]>   
</script>

<div class="seven columns alpha" id="edit-form">

    <?php echo flash(); ?>
        
    <div id="collection-metadata">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php if (!empty($tabContent)): ?>
            <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
            <fieldset class="set">
                <h2><?php echo html_escape(__($tabName)); ?></h2>
                <?php echo $tabContent; ?>        
            </fieldset>
            </div>     
        <?php endif; ?>
    <?php endforeach; ?>
    </div>
    
</div>


<?php fire_plugin_hook('admin_append_to_collections_form', array('collection' => $collection, 'view' => $this)); ?>
