<?php
/* This is really dumb;
// the short <?= php syntax interferes with the xml header!! */
   echo '<?xml version="1.0" encoding="UTF-8"?>'
?>

<tags>
<?php foreach ($tags as $tag): ?>

<tag><?php echo $tag[name]; ?></tag>
<?php endforeach; ?>
</tags>
