<?php head(); ?>
<h2>Tags</h2>
<?php
tag_cloud($tags, 2, uri('items/browse/'), 4, 1);
?>
<?php foot(); ?>