<?php
/* This is really dumb;
// the short <?= php syntax interferes with the xml header!! */
   echo '<?xml version="1.0" encoding="UTF-8"?>'
?>

<items>
<?php foreach ($items as $item): ?>

<item id="<?php echo $item->id; ?>">
	
	<title><?php echo $item->title; ?></title>
	<tags>
	<?php foreach ($item->Tags as $tag): ?>
		<tag name="<?php echo htmlentities($tag->name); ?>"></tag>
	<?php
	endforeach;
	?>
	</tags>
	
	<files><?php foreach ($item->Files as $file): ?><file><title><?php echo $file->title; ?></title></file><?php endforeach; ?></files>
	
	<collection><?php echo $item->Collection->name; ?></collection>
	
	<type><?php echo $item->Type->name; ?></type>

</item>
<?php 
endforeach; 
?>

</items>
