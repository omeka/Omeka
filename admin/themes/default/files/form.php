<?php echo js_tag('tiny_mce/tiny_mce'); 
// echo js_tag('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php echo js_tag('elements'); ?>
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

<section class="seven columns alpha" id="edit-form">
    <?php echo flash(); ?>
    <div id="file-metadata">
        <?php if (file_markup($file)): ?>
            <div id="item-images">
                <?php echo file_markup($file, array('imageSize' => 'square_thumbnail'), array('class' => 'admin-thumb panel')); ?>
            </div>
        <?php endif; ?>    

        <?php foreach ($elementSets as $elementSet): ?>
        <fieldset>
            <h2><?php echo __($elementSet->name); ?></h2>    
            <?php echo element_set_form($file, $elementSet->name); ?>
        </fieldset>
        <?php endforeach; ?>
        <?php fire_plugin_hook('admin_append_to_files_form', array('file' => $file)); ?>
    </div> <!-- end file-metadata div -->
</section>