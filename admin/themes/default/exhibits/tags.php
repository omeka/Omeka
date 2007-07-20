<?php head(); ?>
<div id="primary">
<h2>Exhibit Tags</h2>
<?php if ( total_results(true) ): ?>
	<?php
	tag_cloud($tags, uri('exhibits/browse/'));
	?>
<?php else: ?>
	<h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
</div>
<?php foot(); ?>