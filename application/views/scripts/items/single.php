<div class="item record">
    <?php
    $title = metadata($item, 'rich_title', ['no_escape' => true]);
    $description = metadata($item, ['Dublin Core', 'Description'], ['snippet' => 150]);
    ?>
    <h3><?php echo link_to($item, 'show', $title); ?></h3>
    <?php if (metadata($item, 'has files')) {
        echo link_to_item(
            item_image(null, [], 0, $item),
            ['class' => 'image'], 'show', $item
        );
    }
    ?>
    <?php if ($description): ?>
        <p class="item-description"><?php echo $description; ?></p>
    <?php endif; ?>
</div>
