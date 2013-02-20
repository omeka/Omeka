<?php
$pageTitle = __('Browse Collections');
echo head(array('title'=>$pageTitle,'bodyclass' => 'collections browse'));
?>

<h1><?php echo $pageTitle; ?></h1>
<?php echo pagination_links(); ?>

<?php foreach (loop('collections') as $collection): ?>

<div class="collection">

    <h2><?php echo link_to_collection(); ?></h2>

    <?php if (metadata('collection', array('Dublin Core', 'Description'))): ?>
    <div class="element">
        <h3><?php echo __('Description'); ?></h3>
        <div class="element-text"><?php echo text_to_paragraphs(metadata('collection', array('Dublin Core', 'Description'), array('snippet'=>150))); ?></div>
    </div>
    <?php endif; ?>
    
    <?php if ($collection->hasContributor()): ?>
    <div class="element">
        <h3><?php echo __('Contributors(s)'); ?></h3>
        <div class="element-text">
            <p><?php echo metadata('collection', array('Dublin Core', 'Contributor'), array('all'=>true, 'delimiter'=>', ')); ?></p>
        </div>
    </div>
    <?php endif; ?>

    <p class="view-items-link"><?php echo link_to_items_browse(__('View the items in %s', metadata('collection', array('Dublin Core', 'Title'))), array('collection' => metadata('collection', 'id'))); ?></p>

    <?php fire_plugin_hook('public_collections_browse_each', array('view' => $this, 'collection' => $collection)); ?>

</div><!-- end class="collection" -->

<?php endforeach; ?>

<?php echo pagination_links(); ?>

<?php fire_plugin_hook('public_collections_browse', array('collections'=>$collections, 'view' => $this)); ?>

<?php echo foot(); ?>
