<?php
$pageTitle = __('Browse Collections');
echo head(array('title'=>$pageTitle,'bodyid'=>'collections','bodyclass' => 'browse'));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <div class="pagination"><?php echo pagination_links(); ?></div>

    <?php foreach (loop('collections') as $collection): ?>
    <div class="collection">

        <h2><?php echo link_to_collection(); ?></h2>

        <div class="element">
            <h3><?php echo __('Description'); ?></h3>
            <div class="element-text"><?php echo nls2p(metadata('collection', 'Description', array('snippet'=>150))); ?></div>
        </div>

        <?php if(collection_has_collectors()): ?>
        <div class="element">
            <h3><?php echo __('Collector(s)'); ?></h3>
            <div class="element-text">
                <p><?php echo metadata('collection', 'Collectors', array('delimiter'=>', ')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <p class="view-items-link"><?php echo link_to_browse_items(__('View the items in %s', metadata('collection', 'Name')), array('collection' => metadata('collection', 'id'))); ?></p>

        <?php fire_plugin_hook('public_append_to_collections_browse_each', array('view' => $this)); ?>

    </div><!-- end class="collection" -->
    <?php endforeach; ?>

    <?php fire_plugin_hook('public_append_to_collections_browse', array('view' => $this)); ?>

</div><!-- end primary -->

<?php echo foot(); ?>
