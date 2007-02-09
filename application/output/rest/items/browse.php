<?php
// This is really dumb;
// the short <?= php syntax interferes with the xml header!!
echo '<?xml version="1.0" encoding="UTF-8"?>'
?>

<items>
<?php foreach ($items as $item): ?>
<item title="<?=$item->title; ?>"></item>
<?php endforeach; ?>
</items>