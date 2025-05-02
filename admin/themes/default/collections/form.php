<?php echo js_tag('vendor/tinymce/tinymce.min'); ?>
<?php echo js_tag('elements'); ?>
<?php echo js_tag('tabs'); ?>
<?php echo js_tag('items'); ?>
<script type="text/javascript">
jQuery(document).ready(function () {
    Omeka.Tabs.initialize();
    
    Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
    Omeka.Items.enableTagRemoval();
    Omeka.Items.tagChoices('#tags', <?php echo js_escape(url(array('controller'=>'tags', 'action'=>'autocomplete'), 'default', array(), true)); ?>);

    Omeka.wysiwyg({
        selector: false,
        forced_root_block: false
    });

    // Must run the element form scripts AFTER reseting textarea ids.
    jQuery(document).trigger('omeka:elementformload');

});

jQuery(document).bind('omeka:elementformload', function (event) {
    Omeka.Elements.makeElementControls(event.target, <?php echo js_escape(url('elements/element-form')); ?>,'Item'<?php if ($id = metadata('collection', 'id')) echo ', '.$id; ?>);
    Omeka.Elements.enableWysiwyg(event.target);
});
</script>

<section class="seven columns alpha" id="edit-form">
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
    <?php fire_plugin_hook('admin_collections_form', array('collection' => $collection, 'view' => $this)); ?>
</section>
<?php echo $csrf; ?>
