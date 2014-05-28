<div class="collection record">
    <?php
    $title = metadata($collection, array('Dublin Core', 'Title'));
    $description = metadata($collection, array('Dublin Core', 'Description'), array('snippet' => 150));
    ?>
    <h3><?php echo link_to($this->collection, 'show', strip_formatting($title)); ?></h3>
    <?php if ($collectionImage = record_image($collection, 'square_thumbnail')): ?>
        <?php echo link_to($this->collection, 'show', $collectionImage, array('class' => 'image')); ?>
    <?php endif; ?>
    <?php if ($description): ?>
        <p class="collection-description"><?php echo $description; ?></p>
    <?php endif; ?>
</div>
