<?php
$pageTitle = __('Browse Items');
echo head(array('title'=>$pageTitle,'bodyid'=>'items','bodyclass' => 'browse'));
?>

<div id="primary">

    <h1><?php echo $pageTitle;?> <?php echo __('(%s total)', $total_results); ?></h1>

    <nav class="items-nav navigation" id="secondary-nav">
        <?php echo public_nav_items(); ?>
    </nav>

    <div id="pagination-top" class="pagination"><?php echo pagination_links(); ?></div>

    <?php foreach (loop('items') as $item): ?>
    <div class="item hentry">
        <div class="item-meta">

        <h2><?php echo link_to_item(metadata('item', array('Dublin Core', 'Title')), array('class'=>'permalink')); ?></h2>

        <?php if (metadata('item', 'has thumbnail')): ?>
        <div class="item-img">
            <?php echo link_to_item(item_image('square_thumbnail')); ?>
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

        <?php if (metadata('item', 'has tags')): ?>
        <div class="tags"><p><strong><?php echo __('Tags'); ?>:</strong>
            <?php echo tag_string('items'); ?></p>
        </div>
        <?php endif; ?>

        <?php fire_plugin_hook('public_append_to_items_browse_each', array('view' => $this)); ?>

        </div><!-- end class="item-meta" -->
    </div><!-- end class="item hentry" -->
    <?php endforeach; ?>

    <div id="pagination-bottom" class="pagination"><?php echo pagination_links(); ?></div>

    <?php fire_plugin_hook('public_append_to_items_browse', array('view' => $this)); ?>

</div><!-- end primary -->

<?php echo foot(); ?>
