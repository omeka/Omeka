<?php head(); ?>

<tags>
<?php foreach ($tags as $tag): ?>

<tag><?php echo $tag[name]; ?></tag>
<?php endforeach; ?>
</tags>
