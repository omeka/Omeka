<?php
$collectionTitle = metadata('collection', 'display_title');
if ($collectionTitle != '' && $collectionTitle != __('[Untitled]')) {
    $collectionTitle = ': &quot;' . $collectionTitle . '&quot; ';
} else {
    $collectionTitle = '';
}
$collectionTitle = __('Edit Collection #%s', metadata('collection', 'id')) . $collectionTitle;
?>

<?php
echo head(['title' => $collectionTitle, 'bodyclass' => 'collections']);
include 'form-tabs.php';
echo flash();
?>

<form method="post" enctype="multipart/form-data" id="collection-form" action="">
    <?php include 'form.php'; ?>

    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" name="submit" class="green full-width button" id="save-changes" value="<?php echo __('Save Changes'); ?>" />
            <a href="<?php echo html_escape(public_url('collections/show/'.metadata('collection', 'id'))); ?>" class="full-width blue button" target="_blank"><?php echo __('View Public Page'); ?></a>
            <?php if (is_allowed($collection, 'delete')): ?>
                <?php echo link_to_collection(__('Delete'), ['class' => 'full-width red button delete-confirm'], 'delete-confirm'); ?>
            <?php endif; ?>
            
            <?php fire_plugin_hook("admin_collections_panel_buttons", ['view' => $this, 'record' => $collection, 'collection' => $collection]); ?>

            <div id="public-featured">
                <div class="public">
                    <?php echo $this->formLabel('public', __('Public')); ?>
                    <?php echo $this->formCheckbox('public', $collection->public, [], ['1', '0']); ?>
                </div>

                <div class="featured">
                    <?php echo $this->formLabel('featured', __('Featured')); ?>
                    <?php echo $this->formCheckbox('featured', $collection->featured, [], ['1', '0']); ?>
                </div>
            </div>
            <?php fire_plugin_hook("admin_collections_panel_fields", ['view' => $this, 'record' => $collection]); ?>
        </div>
    </section>
</form>

<?php echo foot(); ?>
