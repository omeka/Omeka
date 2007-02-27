<?php head(array('title' => 'Item'))?>

<?php error($item);?>

<h1><?php $item->title?></h1>
<h4>Metatext</h4>
<?php foreach($item->Metatext as $key => $metatext): ?>
<h5><?php $metatext->Metafield->name; ?>: <?php $metatext->text; ?></h5>

<?php endforeach; ?>
<?php

	$points[0]['latitude'] = 65;
	$points[0]['longitude'] = 65;
	$points[1]['latitude'] = 55;
	$points[1]['longitude'] = 65;
//	plugin('GeoLocation', 'map', null, null, 5, 200, 200, 'map', $points, array('clickable' => true) );
?>	

<h2>Tags</h2>
<ul>
	<?php $item->tagString("<a href=\"whatever.php\">$0</a>", '.');?>
<form id="tags" method="post" action="">
	<input type="text" name="tags" value="Put tag string in me" />
	<input type="submit" name="submit" value="submit">
</form>