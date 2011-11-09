<?php
$pageTitle = __('Browse Tags');
head(array('title'=>$pageTitle, 'content_class' => 'horizontal-nav','bodyclass'=>'tags browse-tags primary')); ?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', count($tags)); ?></h1>
<?php common('tags-nav'); ?>
<div id="primary">
<?php if ( total_results() ): ?>
    <p id="tags-nav">Sort by:
        <a href="<?php echo html_escape(current_uri(array('sort'=>'most'))); ?>"<?php if($_GET['sort'] == 'most') echo ' class="current"'; ?>><?php echo __('Most'); ?></a>
        <a href="<?php echo html_escape(current_uri(array('sort'=>'least'))); ?>"<?php if($_GET['sort'] == 'least') echo ' class="current"'; ?>><?php echo __('Least'); ?></a> 
        <a href="<?php echo html_escape(current_uri(array('sort'=>'alpha'))); ?>"<?php if($_GET['sort'] == 'alpha') echo ' class="current"'; ?>><?php echo __('Alphabetical'); ?></a>
        <a href="<?php echo html_escape(current_uri(array('sort'=>'recent'))); ?>"<?php if($_GET['sort'] == 'recent') echo ' class="current"'; ?>><?php echo __('Recent'); ?></a>
    </p>
    <?php echo tag_cloud($tags, ($browse_for == 'Item') ? uri('items/browse/'): uri('exhibits/browse/')); ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>
</div>
<?php foot(); ?>