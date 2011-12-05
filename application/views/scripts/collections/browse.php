<?php
$pageTitle = __('Browse Collections');
head(array('title'=>$pageTitle,'bodyid'=>'collections','bodyclass' => 'browse'));
?>
<div id="primary">
    <h1><?php echo $pageTitle; ?></h1>
    <div class="pagination"><?php echo pagination_links(); ?></div>

    <?php while (loop_collections()): ?>
    <div class="collection">

        <h2><?php echo link_to_collection(); ?></h2>

        <div class="element">
            <h3><?php echo __('Description'); ?></h3>
            <div class="element-text"><?php echo nls2p(collection('Description', array('snippet'=>150))); ?></div>
        </div>

        <?php if(collection_has_collectors()): ?>
        <div class="element">
            <h3><?php echo __('Collector(s)'); ?></h3>
            <div class="element-text">
                <p><?php echo collection('Collectors', array('delimiter'=>', ')); ?></p>
            </div>
        </div>
        <?php endif; ?>

        <p class="view-items-link"><?php echo link_to_browse_items(__('View the items in %s', collection('Name')), array('collection' => collection('id'))); ?></p>

        <?php echo plugin_append_to_collections_browse_each(); ?>

    </div><!-- end class="collection" -->
    <?php endwhile; ?>

    <?php echo plugin_append_to_collections_browse(); ?>

</div><!-- end primary -->

<?php foot(); ?>
