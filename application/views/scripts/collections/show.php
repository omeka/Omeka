<?php
$collectionTitle = strip_formatting(metadata('collection', array('Dublin Core', 'Title')));
if ($collectionTitle == '') {
    $collectionTitle = __('[Untitled]');
}

$itemTitle = strip_formatting(metadata('item', array('Dublin Core', 'Title')));

?>

<?php echo head(array('title'=> $collectionTitle, 'bodyid'=>'collections', 'bodyclass' => 'show')); ?>

<div id="primary">
    <h1><?php echo $collectionTitle; ?></h1>

    <div id="description" class="element">
        <h2><?php echo __('Description'); ?></h2>
        <div class="element-text"><?php echo text_to_paragraphs(metadata('collection', 'Description')); ?></div>
    </div><!-- end description -->

    <?php if ($collection->hasContributor()): ?>
    <div id="contributors" class="element">
        <h2><?php echo __('Contributor(s)'); ?></h2>
        <div class="element-text">
            <ul>
                <li><?php echo metadata('collection', array('Dublin Core', 'Contributor'), array('all'=>true, 'delimiter'=>'</li><li>')); ?></li>
            </ul>
        </div>
    </div><!-- end contributors -->
    <?php endif; ?>

    <p class="view-items-link"><?php echo link_to_items_browse(__('View the items in %s', $collectionTitle), array('collection' => metadata('collection', 'id'))); ?></p>

    <div id="collection-items">
        <h2><?php echo __('Items in the %s Collection', $collectionTitle); ?></h2>

        <?php foreach (loop('items') as $item): ?>
        <div class="item hentry">
            <h3><?php echo link_to_item($itemTitle, array('class'=>'permalink')); ?></h3>

            <?php if (item_has_thumbnail()): ?>
            <div class="item-img">
                <?php echo link_to_item(item_image('square_thumbnail', array('alt'=>$itemTitle))); ?>
            </div>
            <?php endif; ?>

            <?php if ($text = metadata('item', array('Item Type Metadata', 'Text'), array('snippet'=>250))): ?>
            <div class="item-description">
                <p><?php echo $text; ?></p>
            </div>
            <?php elseif ($description = metadata('item', array('Dublin Core', 'Description'), array('snippet'=>250))): ?>
            <div class="item-description">
                <?php echo $description; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div><!-- end collection-items -->

    <?php fire_plugin_hook('public_append_to_collections_show', array('view' => $this)); ?>

</div><!-- end primary -->

<?php echo foot(); ?>
