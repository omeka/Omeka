<?php head(); ?>

<?php $items = items(); ?>
<?php $tags = tags(); ?>
<h2>About</h2>
<p>This is an example of a static page. To edit the content here, simply open the "about.php" file in a text editor.</p>
<?php foreach($items as $item): ?>
	<h2><?php echo $item->title; ?></h2>
<?php endforeach; ?>
<?php
tag_cloud($tags, uri('items/browse/'));
?>
<?php foot(); ?>