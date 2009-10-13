<?php head(array('title'=>'Browse Tags', 'content_class' => 'horizontal-nav','bodyclass'=>'tags browse-tags primary')); ?>
<h1>Browse Tags (<?php echo count($tags); ?> total)</h1>

<?php common('tags-nav'); ?>

<div id="primary">
<?php if ( total_results() ): ?>
    
    <p id="tags-nav">Sort by:   
        
        <a href="<?php echo html_escape(current_uri(array('sort'=>'most'))); ?>"<?php if($_GET['sort'] == 'most') echo ' class="current"'; ?>>Most</a>
            <a href="<?php echo html_escape(current_uri(array('sort'=>'least'))); ?>"<?php if($_GET['sort'] == 'least') echo ' class="current"'; ?>>Least</a> 

            <a href="<?php echo html_escape(current_uri(array('sort'=>'alpha'))); ?>"<?php if($_GET['sort'] == 'alpha') echo ' class="current"'; ?>>Alphabetical</a>

            <a href="<?php echo html_escape(current_uri(array('sort'=>'recent'))); ?>"<?php if($_GET['sort'] == 'recent') echo ' class="current"'; ?>>Recent</a>

    </p>

    
    
    
    <?php
    echo tag_cloud($tags, ($browse_for == 'Item') ? uri('items/browse/'): uri('exhibits/browse/'));
    ?>
<?php else: ?>
    <p>There are no tags to display.  You must first tag some items.</p>
<?php endif; ?>
</div>
<?php foot(); ?>