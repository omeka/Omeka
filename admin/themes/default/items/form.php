<?php echo js('tiny_mce/tiny_mce'); 
// echo js('tiny_mce/tiny_mce_src'); // Use the 'tiny_mce_src' file for debugging.
?>
<?php echo js('items'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
// TinyMCE hates document.ready.
jQuery(window).load(function () {
    Omeka.Items.initializeTabs();

    var addImage = <?php echo js_escape(img('silk-icons/add.png')); ?>;
    var deleteImage = <?php echo js_escape(img('silk-icons/delete.png')); ?>;
    Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
    Omeka.Items.enableTagRemoval(addImage, deleteImage);
    Omeka.Items.makeFileWindow();
    Omeka.Items.tagChoices('#tags', <?php echo js_escape(uri(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>);

    Omeka.wysiwyg({
        mode: "none",
        forced_root_block: ""
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');

    Omeka.Items.enableAddFiles(<?php echo js_escape(__('Add Another File')); ?>);
    Omeka.Items.changeItemType(<?php echo js_escape(uri("items/change-type")) ?><?php if ($id = item('id')) echo ', '.$id; ?>);
});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Items.makeElementControls(event.target, <?php echo js_escape(uri('items/element-form')); ?><?php if ($id = item('id')) echo ', '.$id; ?>);
    Omeka.Items.enableWysiwyg(event.target);
});
//]]>   
</script>

<?php echo flash(); ?>

<div id="public-featured">
    <?php if ( has_permission('Items', 'makePublic') ): ?>
        <div class="checkbox">
            <label for="public"><?php echo __('Public'); ?>:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'public', 'id'=>'public'), $item->public); ?></div>
        </div>
    <?php endif; ?>
    <?php if ( has_permission('Items', 'makeFeatured') ): ?>
        <div class="checkbox">
            <label for="featured"><?php echo __('Featured'); ?>:</label> 
            <div class="checkbox"><?php echo checkbox(array('name'=>'featured', 'id'=>'featured'), $item->featured); ?></div>
        </div>
    <?php endif; ?>
</div>
<div id="item-metadata">
<?php foreach ($tabs as $tabName => $tabContent): ?>
    <?php if (!empty($tabContent)): ?>
        <div id="<?php echo text_to_id(html_escape($tabName)); ?>-metadata">
        <fieldset class="set">
            <legend><?php echo html_escape(__($tabName)); ?></legend>
            <?php echo $tabContent; ?>        
        </fieldset>
        </div>     
    <?php endif; ?>
<?php endforeach; ?>
</div>
