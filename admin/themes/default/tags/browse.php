<?php head(array('title'=>'Browse Tags', 'body_class'=>'tags')); ?>
<?php common('archive-nav'); ?>
<div id="primary">
<h1>Tags</h1>
<?php if ( total_results(true) ): ?>
	<?php
	tag_cloud($tags, uri('items/browse/'));
	?>
<?php else: ?>
	<h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
</div>
<?php foot(); ?>