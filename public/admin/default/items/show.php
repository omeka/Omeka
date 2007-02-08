<h1><?=$item->title?></h1>
<h4>Metatext</h4>
<?php foreach($item->Metatext as $key => $metatext): ?>
<h5><?= $metatext->Metafield->name; ?>: <?= $metatext->text; ?></h5>
<?php endforeach; ?>	

<h2>Tags</h2>
<ul>
	<?=$item->tagString("<a href=\"whatever.php\">$0</a>", '.');?>
<form id="tags" method="post" action="">
	<input type="text" name="tags" value="Put tag string in me" />
	<input type="submit" name="submit" value="submit">
</form>
