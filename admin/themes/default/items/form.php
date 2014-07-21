<?php echo js_tag('vendor/tiny_mce/tiny_mce'); ?>
<?php echo js_tag('elements'); ?>
<?php echo js_tag('tabs'); ?>
<?php echo js_tag('items'); ?>
<script type="text/javascript" charset="utf-8">
//<![CDATA[
// TinyMCE hates document.ready.
jQuery(window).load(function () {
    Omeka.Tabs.initialize();

    Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
    Omeka.Items.enableTagRemoval();
    Omeka.Items.makeFileWindow();
    Omeka.Items.enableSorting();
    Omeka.Items.tagChoices('#tags', <?php echo js_escape(url(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>);

    Omeka.wysiwyg({
        mode: "none",
        forced_root_block: ""
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');

    Omeka.Items.enableAddFiles(<?php echo js_escape(__('Add Another File')); ?>);
    Omeka.Items.changeItemType(<?php echo js_escape(url("items/change-type")) ?><?php if ($id = metadata('item', 'id')) echo ', '.$id; ?>);
});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?php echo js_escape(url('elements/element-form')); ?>,'Item'<?php if ($id = metadata('item', 'id')) echo ', '.$id; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
//]]>
</script>

<section class="seven columns alpha" id="edit-form">

    <?php echo flash(); ?>
    
    <div id="item-metadata">
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

</section>
<?php echo $csrf; ?>
