<?php echo js('tiny_mce/tiny_mce'); 
// echo js('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php echo js('elements'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
// TinyMCE hates document.ready.
jQuery(window).load(function () {
    
    Omeka.wysiwyg({
        mode: "none",
        forced_root_block: ""
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');
});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?php echo js_escape(url('elements/element-form')); ?>,'File'<?php if ($id = metadata('file', 'id')) echo ', '.$id; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
//]]>   
</script>

<div class="seven columns alpha" id="edit-form">

    <?php echo flash(); ?>
    <div id="file-metadata">
        <div id="fullsize-file">
            <?php echo display_file($file, array('imageSize' => 'fullsize')); ?>
        </div> <!-- end fullsize-file div -->
    
        <?php foreach ($elementSets as $elementSet): ?>
        <fieldset>
            <h2><?php echo __($elementSet->name); ?></h2>    
            <?php echo display_element_set_form($file, $elementSet->name); ?>
        </fieldset>
        <?php endforeach; ?>
        <?php fire_plugin_hook('admin_append_to_files_form', array('file' => $file, 'view' => $this)); ?>
    </div> <!-- end file-metadata div -->
</div>