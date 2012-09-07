<?php head(array('title'=>metadata('collection', 'Name'), 'bodyid'=>'collections', 'bodyclass' => 'show')); ?>

<div id="primary">
    <h1><?php echo metadata('collection', 'Name'); ?></h1>

    <div id="description" class="element">
        <h2><?php echo __('Description'); ?></h2>
        <div class="element-text"><?php echo nls2p(metadata('collection', 'Description')); ?></div>
    </div><!-- end description -->

    <?php if (collection_has_collectors()): ?>
    <div id="collectors" class="element">
        <h2><?php echo __('Collector(s)'); ?></h2>
        <div class="element-text">
            <ul>
                <li><?php echo metadata('collection', 'Collectors', array('delimiter'=>'</li><li>')); ?></li>
            </ul>
        </div>
    </div><!-- end collectors -->
    <?php endif; ?>

    <p class="view-items-link"><?php echo link_to_browse_items(__('View the items in %s', metadata('collection', 'Name')), array('collection' => metadata('collection', 'id'))); ?></p>

    <div id="collection-items">
        <h2><?php echo __('Items in the %s Collection', metadata('collection', 'Name')); ?></h2>

        <?php foreach (loop('items') as $item): ?>
        <div class="item hentry">
            <h3><?php echo link_to_item(metadata('item', array('Dublin Core', 'Title')), array('class'=>'permalink')); ?></h3>

            <?php if (item_has_thumbnail()): ?>
            <div class="item-img">
                <?php echo link_to_item(item_square_thumbnail(array('alt'=>metadata('item', array('Dublin Core', 'Title'))))); ?>
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

<?php foot(); ?>
