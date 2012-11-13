<?php if ($collection): ?>
    <?php
    $title = metadata($collection, array('Dublin Core', 'Title'));
    $description = metadata($collection, array('Dublin Core', 'Description'), array('snippet' => 150));
    ?>
    <h3><?php echo link_to($this->collection, 'show', strip_formatting($title)); ?></h3>
    <?php if ($description): ?>
        <p class="collection-description"><?php echo $description; ?></p>
    <?php endif; ?>
<?php else: ?>
    <p><?php echo __('No featured collections are available.'); ?></p>
<?php endif; ?>
