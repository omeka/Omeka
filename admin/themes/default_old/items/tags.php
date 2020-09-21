<?php
$pageTitle = __('Browse Items by Tag');
echo head(array('title' => $pageTitle));
echo flash();
?>
<?php if (count($tags)): ?>
    <?php echo tag_cloud($tags, 'items/browse'); ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>
<?php echo foot(); ?>
