<?php
$pageTitle = __('Browse Collections by Tag');
echo head(array('title' => $pageTitle));
echo flash();
?>
<?php if (count($tags)): ?>
    <?php echo tag_cloud($tags, 'collections/browse'); ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some collections.'); ?></p>
<?php endif; ?>
<?php echo foot(); ?>
