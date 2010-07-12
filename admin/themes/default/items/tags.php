<?php head(array('title'=>'Browse Items by Tag')); ?>
<div id="primary">
<h2>Browse Items by Tag</h2>
<?php if (count($tags)): ?>
    <?php echo tag_cloud($tags, uri('items/browse/')); ?>
<?php else: ?>
    <h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
</div>
<?php foot(); ?>