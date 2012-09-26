<?php
$pageTitle = __('Browse Items by Tag');
echo head(array('title'=>$pageTitle)); ?>
<div id="primary">
<h2><?php echo $pageTitle; ?></h2>
<?php if (count($tags)): ?>
    <?php echo tag_cloud($tags, 'items/browse'); ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>
</div>
<?php echo foot(); ?>
