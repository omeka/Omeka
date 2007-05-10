<?php head(); ?>
<?php common('archive-nav'); ?>
<h2>Tags</h2>
<?php if ( $total ): ?>
	<?php
	tag_cloud($tags, 2, uri('items/browse/'), 4, 1);
	?>
<?php else: ?>
	<h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
<?php foot(); ?>