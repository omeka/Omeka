<?php head(); ?>

<div id="primary">
<h2>Tags</h2>
<?php if ( count($tags) ): ?>
    <?php
    echo tag_cloud($tags, uri('items/browse/'));
    ?>
<?php else: ?>
    <h2>There are no tags to display.  You must first tag some items.</h2>
<?php endif; ?>
</div>
<?php foot(); ?>