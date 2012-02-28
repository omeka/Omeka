<?php
$pageTitle = __('Browse Tags');
head(array('title'=>$pageTitle, 'content_class' => 'horizontal-nav','bodyclass'=>'tags browse-tags primary')); ?>
<h1><?php echo $pageTitle; ?> <?php echo __('(%s total)', count($tags)); ?></h1>
<?php common('tags-nav'); ?>
<div id="primary">
<?php if ( total_results() ): ?>
    <p id="tags-nav"><?php echo __('Sort by'); ?>:
        <?php
        $sortOptions = array(
            __('Most') => array('sort_field' => 'count', 'sort_dir' => 'd'),
            __('Least') => array('sort_field' => 'count'),
            __('Alphabetical') => array('sort_field' => 'name'),
            __('Recent') => array('sort_field' => 'time', 'sort_dir' => 'd')
        );

        foreach ($sortOptions as $label => $params) {
            $uri = html_escape(current_uri($params));
            $class = ($sort == $params) ? ' class="current"' : '';

            echo "<a href=\"$uri\"$class>$label</a>";
        }
        ?>
    </p>
    <?php echo tag_cloud($tags, ($browse_for == 'Item') ? uri('items/browse/'): uri('exhibits/browse/')); ?>
<?php else: ?>
    <p><?php echo __('There are no tags to display. You must first tag some items.'); ?></p>
<?php endif; ?>
</div>
<?php foot(); ?>
