<?php echo js_tag('vendor/tinymce/tinymce.min'); ?>
<?php echo js_tag('elements'); ?>
<?php echo js_tag('tabs'); ?>
<?php echo js_tag('items'); ?>
<script type="text/javascript" charset="utf-8">
jQuery(document).ready(function () {
    Omeka.Tabs.initialize();

    Omeka.Items.tagDelimiter = <?php echo js_escape(get_option('tag_delimiter')); ?>;
    Omeka.Items.enableTagRemoval();
    Omeka.Items.makeFileWindow();
    Omeka.Items.enableSorting();
    Omeka.Items.tagChoices('#tags', <?php echo js_escape(url(['controller'=>'tags', 'action'=>'autocomplete'], 'default', [], true)); ?>);

    Omeka.wysiwyg({
        selector: false,
        forced_root_block: false
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
</script>

<section class="seven columns alpha" id="edit-form">

    <?php echo flash(); ?>
    
    <div id="item-metadata">
    <?php foreach ($tabs as $tabName => $tabContent): ?>
        <?php $escapedTabId = text_to_id(html_escape($tabName)); ?>
        <?php if (!empty($tabContent)): ?>
            <div id="<?php echo $escapedTabId; ?>-metadata" role="tabpanel" aria-labelledby="<?php echo $escapedTabId; ?>-heading" tabindex="0">
            <fieldset class="set">
              <legend id="<?php echo $escapedTabId; ?>-heading"><?php echo html_escape(__($tabName)); ?></legend>
              <?php echo $tabContent; ?>
            </fieldset>
            </div>     
        <?php endif; ?>
    <?php endforeach; ?>
    </div>

</section>
<?php echo $csrf; ?>
