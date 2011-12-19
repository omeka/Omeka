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
            'most' => __('Most'),
            'least' => __('Least'),
            'alpha' => __('Alphabetical'),
            'recent' => __('Recent')
        );

        foreach ($sortOptions as $key => $label) {
            $uri = html_escape(current_uri(array('sort' => $key)));
            $class = ($sort == $key) ? ' class="current"' : '';

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
